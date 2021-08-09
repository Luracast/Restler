<?php

namespace Luracast\Restler;


use ErrorException;
use Exception;
use Luracast\Restler\Contracts\{AccessControlInterface,
    AuthenticationInterface,
    FilterInterface,
    ProvidesMultiVersionApiInterface,
    RequestMediaTypeInterface,
    ResponseMediaTypeInterface,
    UserIdentificationInterface,
    UsesAuthenticationInterface};
use Luracast\Restler\Data\Param;
use Luracast\Restler\Data\Route;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\MediaTypes\Json;
use Luracast\Restler\Utils\{ClassName, CommentParser, Text, Type};
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class Routes
{
    public static array $prefixingParameterNames = [
        'id',
    ];

    public static array $formatsByName = [
        'email' => 'email',
        'password' => 'password',
        'phone' => 'tel',
        'mobile' => 'tel',
        'tel' => 'tel',
        'search' => 'search',
        'date' => 'date',
        'created_at' => 'datetime',
        'modified_at' => 'datetime',
        'url' => 'url',
        'link' => 'url',
        'href' => 'url',
        'website' => 'url',
        'color' => 'color',
        'colour' => 'color',
    ];

    /**
     * @internal
     */
    public static array $authClasses = [];
    /**
     * @internal
     */
    public static array $preAuthFilterClasses = [];
    /**
     * @internal
     */
    public static array $postAuthFilterClasses = [];

    /**
     * @internal
     */
    public static array $requestFormatMap = [
        'default' => Json::class,
        Json::MIME => Json::class,
    ];
    /**
     * @internal
     */
    public static array $responseFormatMap = [
        'default' => Json::class,
        Json::EXTENSION => Json::class,
        Json::MIME => Json::class,
        'extensions' => ['.json'],
    ];
    /**
     * @internal
     */
    public static array $requestFormatOverridesMap = [];
    public static array $responseFormatOverridesMap = ['extensions' => []];
    /**
     * @internal
     */
    public static int $minimumVersion = 1;
    /**
     * @internal
     */
    public static int $maximumVersion = 1;

    public static array $requestMediaTypes = [Json::MIME];
    public static array $responseMediaTypes = [Json::MIME];

    public static array $requestMediaTypeOverrides = [];
    public static array $responseMediaTypeOverrides = [];
    public static array $models = [];
    /**
     * @var null|string class to use for caching purpose, uses Defaults when null
     */
    public static ?string $cacheClass = null;
    protected static array $routes = [];
    private static array $parsedScopes = [];

    public static function setApiVersion(int $maximum = 1, int $minimum = 1): void
    {
        static::$maximumVersion = $maximum;
        static::$minimumVersion = $minimum;
    }

    /**
     * @param string ...$types
     * @throws Exception
     */
    public static function setMediaTypes(string ...$types): void
    {
        static::_setMediaTypes(
            RequestMediaTypeInterface::class,
            $types,
            static::$requestFormatMap,
            static::$requestMediaTypes
        );

        static::_setMediaTypes(
            ResponseMediaTypeInterface::class,
            $types,
            static::$responseFormatMap,
            static::$responseMediaTypes
        );
    }

    /**
     * @param string $interface
     * @param array $types
     * @param array $formatMap
     * @param array $mediaTypes
     * @throws Exception
     * @internal
     */
    public static function _setMediaTypes(
        string $interface,
        array $types,
        array &$formatMap,
        array &$mediaTypes
    ): void {
        if (!count($types)) {
            return;
        }
        $formatMap = [];
        $mediaTypes = [];
        $extensions = [];
        $writable = $interface === ResponseMediaTypeInterface::class;
        foreach ($types as $type) {
            if (!Type::implements($type, $interface)) {
                throw new Exception(
                    $type . ' is an invalid media type class; it must implement ' .
                    $interface . ' interface'
                );
            }
            foreach ($type::supportedMediaTypes() as $mime => $extension) {
                $mediaTypes[] = $mime;
                if ($writable) {
                    $extensions[".$extension"] = true;
                    if (!isset($formatMap[$extension])) {
                        $formatMap[$extension] = $type;
                    }
                }
                if (!isset($formatMap[$mime])) {
                    $formatMap[$mime] = $type;
                }
            }
        }
        $formatMap['default'] = $types[0];
        if ($writable) {
            $formatMap['extensions'] = array_keys($extensions);
        }
    }

    /**
     * @param string ...$types
     * @throws Exception
     */
    public static function setRequestMediaTypes(string ...$types): void
    {
        static::_setMediaTypes(
            RequestMediaTypeInterface::class,
            $types,
            static::$requestFormatMap,
            static::$requestMediaTypes
        );
    }

    /**
     * @param string ...$types
     * @throws Exception
     */
    public static function setResponseMediaTypes(string ...$types): void
    {
        static::_setMediaTypes(
            ResponseMediaTypeInterface::class,
            $types,
            static::$responseFormatMap,
            static::$responseMediaTypes
        );
    }

    /**
     * @param string ...$types
     * @throws Exception
     */
    public static function setOverridingRequestMediaTypes(string ...$types): void
    {
        static::$requestMediaTypeOverrides = $types;
        $ignore = [];
        static::_setMediaTypes(
            RequestMediaTypeInterface::class,
            $types,
            static::$requestFormatOverridesMap,
            $ignore
        );
    }

    /**
     * @param string ...$types
     * @throws Exception
     */
    public static function setOverridingResponseMediaTypes(string ...$types): void
    {
        static::$responseMediaTypeOverrides = $types;
        $ignore = [];
        static::_setMediaTypes(
            ResponseMediaTypeInterface::class,
            $types,
            static::$responseFormatOverridesMap,
            $ignore
        );
    }

    /**
     * Add multiple api classes through this method.
     *
     * This method provides better performance when large number
     * of API classes are in use as it processes them all at once,
     * as opposed to hundreds (or more) addAPIClass calls.
     *
     *
     * All the public methods that do not start with _ (underscore)
     * will be will be exposed as the public api by default.
     *
     * All the protected methods that do not start with _ (underscore)
     * will exposed as protected api which will require authentication
     *
     * @param array $map [$resourcePath => $className, $className2 ...]
     *                   array of associative arrays containing the
     *                   class name & optional url prefix for mapping.
     *
     * @throws Exception
     */
    public static function mapApiClasses(array $map): void
    {
        if (Defaults::$productionMode && static::handleCache()) {
            return;
        }
        $versionMap = [];
        $maxVersionMethod = 'getMaximumSupportedVersion';
        try {
            foreach ($map as $path => $className) {
                if (is_numeric($path)) {
                    $path = null;
                }
                if (isset(Defaults::$aliases[$className])) {
                    $className = Defaults::$aliases[$className];
                }
                $info = ClassName::parse($className);
                $currentVersion = $info['version'];
                $found = $info['version_found'];
                if (is_null($path)) {
                    $path = Defaults::$autoRoutingEnabled ? strtolower($info['name']) : '';
                } else {
                    $path = trim($path, '/');
                }
                if (!empty($path)) {
                    $path .= '/';
                }
                if (!class_exists($className)) {
                    $nextClass = ClassName::build($info['name'], $info['namespace'], $currentVersion, !$found);
                    if (!class_exists($nextClass)) {
                        throw new ErrorException("Class '$className' not found");
                    }
                    $className = $nextClass;
                }
                if (Type::implements($className, ProvidesMultiVersionApiInterface::class)) {
                    $max = $className::$maxVersionMethod();
                    for ($i = $currentVersion; $i <= $max; $i++) {
                        $versionMap[$path][$i] = $className;
                    }
                } else {
                    $versionMap[$path][$currentVersion] = $className;
                }
                for (
                    $version = $currentVersion + 1;
                    $version <= static::$maximumVersion;
                    $version++
                ) {
                    if (isset($versionMap[$path][$version])) {
                        continue;
                    }
                    $nextClass = ClassName::build($info['name'], $info['namespace'], $version);
                    if (class_exists($nextClass)) {
                        if (Type::implements($nextClass, ProvidesMultiVersionApiInterface::class)) {
                            $max = $className::$maxVersionMethod();
                            for ($i = $version; $i <= $max; $i++) {
                                $versionMap[$path][$i] = $nextClass;
                            }
                        } else {
                            $versionMap[$path][$version] = $nextClass;
                        }
                    }
                }
            }
            foreach ($versionMap as $path => $classes) {
                foreach ($classes as $version => $class) {
                    static::addAPIForVersion($class, $path, $version);
                }
            }
            if (Defaults::$productionMode) {
                static::handleCache(true);
            }
        } catch (Throwable $e) {
            throw new Exception(
                "mapAPIClasses failed. " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    private static function handleCache(bool $save = false): bool
    {
        if (!$save && !empty(static::$routes)) {
            return true;
        }
        $cacheClass = ClassName::get(static::$cacheClass ?? CacheInterface::class);
        /** @var CacheInterface $cache */
        $cache = new $cacheClass();
        if ($save) {
            return $cache->set('routes', static::$routes);
        }
        if (!$routes = $cache->get('routes', false)) {
            return false;
        }
        static::fromArray($routes);
        return true;
    }

    /**
     * Import previously created routes from cache
     *
     * @param array $routes
     */
    public static function fromArray(array $routes): void
    {
        static::$routes = $routes;
    }

    /**
     * Route the public and protected methods of an Api class
     *
     * @param string $className
     * @param string $resourcePath
     * @param int $version
     *
     * @throws Exception
     * @throws HttpException
     */
    protected static function addAPIForVersion(string $className, string $resourcePath, int $version = 1): void
    {
        /*
         * Mapping Rules
         * =============
         *
         * - Optional parameters should not be mapped to URL
         * - If a required parameter is of primitive type
         *      - If one of the self::$prefixingParameterNames
         *              - Map it to URL
         *      - Else If request method is POST/PUT/PATCH
         *              - Map it to body
         *      - Else If request method is GET/DELETE
         *              - Map it to query string
         * - If a required parameter is not primitive type
         *      If request method is POST/PUT/PATCH
         *              - Map it to body
         *     - Else If request method is GET/DELETE
         *              - Map it to query string with name[property]=value syntax
         */
        $class = new ReflectionClass($className);
        try {
            $classMetadata = CommentParser::parse($class->getDocComment());
            $classMetadata['resource']['summary'] = $classMetadata['summary'] ?? '';
            $classMetadata['resource']['description'] = $classMetadata['description'] ?? '';
            unset($classMetadata['summary']);
            unset($classMetadata['description']);
        } catch (Exception $e) {
            throw new HttpException(500, "Error while parsing comments of `$className` class. " . $e->getMessage());
        }
        $classMetadata['scope'] = $scope = static::scope($class);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);
        foreach ($methods as $method) {
            if ($method->isStatic()) {
                continue;
            }
            $methodUrl = strtolower($method->getName());
            //method name should not begin with _
            if ($methodUrl[0] == '_') {
                continue;
            }
            if ($doc = $method->getDocComment()) {
                try {
                    $metadata = CommentParser::parse($doc) + $classMetadata;
                } catch (Exception $e) {
                    throw new HttpException(
                        500,
                        "Error while parsing comments of `{$className}::{$method->getName()}` method. " . $e->getMessage(
                        )
                    );
                }
            } else {
                $metadata = $classMetadata;
            }

            //@access should not be private
            if ('private' == ($metadata['access'] ?? false)) {
                continue;
            }

            $route = Route::fromMethod($method, $metadata, $scope);
            $route->action[0] = $className;
            $route->resource['path'] = $resourcePath;
            $allowAmbiguity = (isset($metadata['smart-auto-routing']) && $metadata['smart-auto-routing'] != 'true')
                || !Defaults::$smartAutoRouting;

            // if manual route
            if (preg_match_all(
                '/@url\s+(GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS)[ \t]*\/?(\S*)/s',
                $doc,
                $matches,
                PREG_SET_ORDER
            )) {
                foreach ($matches as $match) {
                    $httpMethod = $match[1];
                    $url = rtrim($resourcePath . $match[2], '/');
                    self::addRoute($route->withLink($url, $httpMethod), $version);
                }
                //if auto route enabled, do so
            } elseif (Defaults::$autoRoutingEnabled) {
                // no configuration found so use convention
                if (preg_match_all('/^(GET|POST|PUT|PATCH|DELETE|OPTIONS)/i', $methodUrl, $matches)) {
                    $httpMethod = strtoupper($matches[0][0]);
                    $methodUrl = substr($methodUrl, strlen($httpMethod));
                } else {
                    $httpMethod = 'GET';
                }
                if ($methodUrl == 'index') {
                    $methodUrl = '';
                }
                $url = empty($methodUrl) ? rtrim($resourcePath, '/') : $resourcePath . $methodUrl;
                $pathParams = $allowAmbiguity
                    ? array_filter(
                        $route->parameters,
                        fn(Param $p) => $p->scalar && !$p->multiple
                    )
                    : $route->filterParams(true, Param::FROM_PATH);
                if (empty($pathParams) || $allowAmbiguity) {
                    self::addRoute($route->withLink($url, $httpMethod), $version);
                } elseif (end($pathParams)->variadic) {
                    self::addRoute($route->withLink($url . '/*', $httpMethod), $version);
                    return;
                } else {
                    $lastPathParam = end($pathParams);
                }
                $prefixed = false;
                foreach ($pathParams as $p) {
                    if (!empty($methodUrl) && !$prefixed && in_array($p->name, self::$prefixingParameterNames)) {
                        $url = preg_replace(
                            '/' . $methodUrl . '$/',
                            '{' . $p->name . '}/' . $methodUrl,
                            $url
                        );
                        $prefixed = true;
                    } else {
                        $url .= '/{' . $p->name . '}';
                    }
                    if ($allowAmbiguity || $p === $lastPathParam) {
                        self::addRoute($route->withLink($url, $httpMethod), $version);
                    }
                }
            }
        }
    }

    public static function scope(ReflectionClass $class)
    {
        if (!isset(self::$parsedScopes[$name = $class->getName()])) {
            if ($class->isInternal()) {
                return ['*' => ''];
            }
            $code = file_get_contents($class->getFileName());
            $namespace = $class->getNamespaceName();
            self::$parsedScopes[$name] = [
                    '*' => empty($namespace) ? '' : $namespace . '\\',
                ] +
                self::parseUseStatements(
                    $code,
                    $name
                );
        }
        return self::$parsedScopes[$name];
    }

    /**
     * Parses PHP code.
     *
     * @param string $code
     * @param string|null $forClass
     * @return array of [class => [alias => class, ...]]
     */
    protected static function parseUseStatements(string $code, ?string $forClass = null): array
    {
        if (!defined('T_NAME_QUALIFIED')) {
            define('T_NAME_QUALIFIED', 314);
        }
        $tokens = token_get_all($code);
        $namespace = $class = $classLevel = $level = null;
        $res = $uses = [];
        while ($token = current($tokens)) {
            next($tokens);
            switch (is_array($token) ? $token[0] : $token) {
                case T_NAMESPACE:
                    $namespace = ltrim(self::fetch($tokens, [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED]) . '\\', '\\');
                    $uses = [];
                    break;

                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                    if ($name = self::fetch($tokens, T_STRING)) {
                        $class = $namespace . $name;
                        $classLevel = $level + 1;
                        $res[$class] = $uses;
                        if ($class === $forClass) {
                            return $res[$class];
                        }
                    }
                    break;

                case T_USE:
                    while (!$class && ($name = self::fetch($tokens, [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED]))) {
                        $name = ltrim($name, '\\');
                        if (self::fetch($tokens, '{')) {
                            while ($suffix = self::fetch($tokens, [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED])) {
                                if (self::fetch($tokens, T_AS)) {
                                    $uses[self::fetch($tokens, T_STRING)] = $name . $suffix;
                                } else {
                                    $tmp = explode('\\', $suffix);
                                    $uses[end($tmp)] = $name . $suffix;
                                }
                                if (!self::fetch($tokens, ',')) {
                                    break;
                                }
                            }
                        } elseif (self::fetch($tokens, T_AS)) {
                            $uses[self::fetch($tokens, T_STRING)] = $name;
                        } else {
                            $tmp = explode('\\', $name);
                            $uses[end($tmp)] = $name;
                        }
                        if (!self::fetch($tokens, ',')) {
                            break;
                        }
                    }
                    break;

                case T_CURLY_OPEN:
                case T_DOLLAR_OPEN_CURLY_BRACES:
                case '{':
                    $level++;
                    break;

                case '}':
                    if ($level === $classLevel) {
                        $class = $classLevel = null;
                    }
                    $level--;
            }
        }

        return $forClass ? $res[$forClass] : $res;
    }

    private static function fetch(&$tokens, $take): ?string
    {
        $res = null;
        while ($token = current($tokens)) {
            [$token, $s] = is_array($token) ? $token : [$token, $token];
            if (in_array($token, (array)$take, true)) {
                $res .= $s;
            } elseif (!in_array($token, [T_DOC_COMMENT, T_WHITESPACE, T_COMMENT], true)) {
                break;
            }
            next($tokens);
        }
        return $res;
    }

    public static function addRoute(Route $route, int $version = 1)
    {
        if (empty($route->path)) {
            //compute from the human readable url to machine computable typed route path
            $route->path = preg_replace_callback(
                '/{[^}]+}|:[^\/]+/',
                function ($matches) use ($route): string {
                    $match = trim($matches[0], '{}:');
                    $param = $route->parameters[$match];
                    return '{' . Routes::typeChar($param->type) . $param->index . '}';
                },
                $route->url
            );
        }
        //check for wildcard routes
        if (substr($route->path, -1, 1) == '*') {
            $path = rtrim($route->path, '/*');
            /** @var Route|false $existing */
            if (
                !Defaults::$productionMode &&
                $existing = static::$routes["v$version"]['*'][$path][$route->httpMethod] ?? false
            ) {
                throw new HttpException(
                    500,
                    'Ambigous route mappings detected. ' .
                    $existing . ' is overwritten by ' . $route
                    , [
                        'existing' => $existing,
                        'overwriting' => $route
                    ]
                );
            }
            static::$routes["v$version"]['*'][$path][$route->httpMethod] = $route;
        } else {
            /** @var Route|false $existing */
            if (
                !Defaults::$productionMode &&
                $existing = static::$routes["v$version"][$route->path][$route->httpMethod] ?? false
            ) {
                throw new HttpException(
                    500,
                    'Ambigous route mappings detected. ' .
                    $existing . ' is overwritten by ' . $route
                    , [
                        'existing' => $existing,
                        'overwriting' => $route
                    ]
                );
            }
            static::$routes["v$version"][$route->path][$route->httpMethod] = $route;
            //create an alias with index if the base name is index
            if (
                (is_array($route->action) && 'index' == $route->action[1]) ||
                (is_string($route->action) && 'index' == $route->action)
            ) {
                $path = ltrim("$route->path/index", '/');
                /** @var Route|false $existing */
                if (
                    !Defaults::$productionMode &&
                    $existing = static::$routes["v$version"][$path][$route->httpMethod] ?? false
                ) {
                    throw new HttpException(
                        500,
                        'Ambiguous route mappings detected. ' .
                        $existing . ' is overwritten by ' . $route
                        , [
                            'existing' => $existing,
                            'overwriting' => $route
                        ]
                    );
                }
                static::$routes["v$version"][$path][$route->httpMethod] = $route;
            }
        }
    }

    /**
     * @access private
     * @param string|null $type
     * @return string
     */
    public static function typeChar(string $type = null)
    {
        if (!$type) {
            return 's';
        }
        switch ($type[0]) {
            case 'i':
            case 'f':
                return 'n';
        }
        return 's';
    }

    /**
     * protected methods will need at least one authentication class to be set
     * in order to allow that method to be executed
     *
     * @param string $className of the authentication class
     * @throws Exception
     */
    public static function addAuthenticator(string $className): void
    {
        $implements = class_implements($className);
        if (!isset($implements[AuthenticationInterface::class])) {
            throw new Exception(
                $className .
                ' is an invalid authenticator class; it must implement ' .
                'AuthenticationInterface.'
            );
        }
        if (!in_array($className, Defaults::$implementations[AuthenticationInterface::class])) {
            Defaults::$implementations[AuthenticationInterface::class][] = $className;
        }
        if (isset($implements[AccessControlInterface::class]) &&
            !in_array($className, Defaults::$implementations[AccessControlInterface::class])) {
            Defaults::$implementations[AccessControlInterface::class][] = $className;
        }
        static::$authClasses[] = $className;
    }

    /**
     * Classes implementing FilterInterface can be added for filtering out
     * the api consumers.
     *
     * It can be used for rate limiting based on usage from a specific ip
     * address or filter by country, device etc.
     *
     * @param string ...$classNames
     * @throws Exception
     */
    public static function setFilters(string ...$classNames): void
    {
        if (Defaults::$productionMode && static::handleCache()) {
            return;
        }
        static::$postAuthFilterClasses = [];
        static::$preAuthFilterClasses = [];
        foreach ($classNames as $className) {
            $implements = class_implements($className);
            if (!isset($implements[FilterInterface::class])) {
                throw new Exception(
                    $className . ' is an invalid filter class; it must implement ' .
                    'FilterInterface.'
                );
            }
            if (isset($implements[UsesAuthenticationInterface::class])) {
                static::$postAuthFilterClasses[] = $className;
            } else {
                static::$preAuthFilterClasses[] = $className;
            }
        }
    }

    /**
     * @param string $path
     * @param string $httpMethod
     * @param ServerRequestInterface|null $request
     * @param int $version
     * @param array $data
     * @return Route
     * @throws HttpException
     */
    public static function find(
        string $path,
        string $httpMethod,
        ?ServerRequestInterface $request = null,
        int $version = 1,
        array $data = []
    ) {
        if (empty(static::$routes)) {
            throw new HttpException(
                500,
                'No routes defined. Please call `Router::mapApiClasses` or `Router::addApi` first.'
            );
        }
        if (!$p = static::$routes["v$version"] ?? false) {
            throw new HttpException(
                404,
                $version == 1 ? '' : "Version $version is not supported"
            );
        }
        $path = rtrim($path, '/');
        $status = 404;
        $message = null;
        $methods = [];
        $later = [];
        if (isset($p[$path][$httpMethod])) {
            //================== static routes ==========================
            return static::populate($p[$path][$httpMethod], $data, $request);
        } elseif (isset($p['*'])) {
            //================== wildcard routes ========================
            uksort(
                $p['*'],
                fn($a, $b) => strlen($b) - strlen($a)
            );
            foreach ($p['*'] as $key => $value) {
                if (empty($key)) {
                    if ($route = $value[$httpMethod] ?? false) {
                        $later[$httpMethod] = $route;
                    }
                } elseif (strpos($path, $key) === 0 && isset($value[$httpMethod])) {
                    //path found, convert rest of the path to parameters
                    $path = substr($path, strlen($key) + 1);
                    /** @var Route $route */
                    $route = $value[$httpMethod];
                    $route->data = explode('/', $path);
                    return $route;
                }
            }
        }
        //================== dynamic routes =============================
        //add newline char if trailing slash is found
        if (substr($path, -1) == '/') {
            $path .= PHP_EOL;
        }
        //if double slash is found fill in newline char;
        $path = str_replace('//', '/' . PHP_EOL . '/', $path);
        ksort($p);
        foreach ($p as $key => $value) {
            if (!isset($value[$httpMethod])) {
                continue;
            }
            /** @var Route $route */
            $route = $value[$httpMethod];
            $regex = str_replace(
                ['{', '}'],
                ['(?P<', '>[^/]+)'],
                $key
            );
            if (preg_match_all(":^$regex$:i", $path, $matches, PREG_SET_ORDER)) {
                $matches = $matches[0];
                $found = true;
                $params = array_column($route->parameters, null, 'index');
                foreach ($matches as $k => $v) {
                    if (is_numeric($k)) {
                        unset($matches[$k]);
                        continue;
                    }
                    $index = intval(substr($k, 1));

                    /** @var Param $param */
                    $param = $params[$index];
                    if ($k[0] == 's' || strpos($k, static::pathVarTypeOf($v)) === 0) {
                        //remove the newlines
                        $data[$param->name] = trim($v, PHP_EOL);
                    } else {
                        $status = 400;
                        $message = 'invalid value specified for `'
                            . $param->name . '`';
                        $found = false;
                        break;
                    }
                }
                if ($found) {
                    return static::populate($route, $data, $request);
                }
            }
        }
        if ($status == 404) {
            if ($route = $later[$httpMethod] ?? false) {
                $route->apply(explode('/', $path), false);
                return $route;
            }
            //check if other methods are allowed
            if (isset($p[$path])) {
                $status = 405;
                $methods = array_keys($p[$path]);
            }
        }
        $e = new HttpException($status, $message);
        if ($status == 405) {
            $e->setHeader('Allow', implode(', ', $methods));
        }
        throw $e;
    }

    /**
     * Populates the parameter values
     *
     * @param Route $route
     * @param array $data
     * @param ServerRequestInterface|null $request
     * @return Route
     *
     * @access private
     */
    protected static function populate(Route $route, array $data, ?ServerRequestInterface $request = null): Route
    {
        if (Defaults::$smartParameterParsing) {
            if (count($route->parameters)) {
                /** @var Param $param */
                $param = array_values($route->parameters)[0];
                if (
                    !array_key_exists($param->name, $data) &&
                    array_key_exists(Defaults::$fullRequestDataName, $data) &&
                    !is_null($d = $data[Defaults::$fullRequestDataName]) &&
                    static::typeMatch($param->type, $d)
                ) {
                    $data[$param->name] = $d;
                } else {
                    $bodyParams = $route->filterParams(true);
                    if (1 == count($bodyParams)) {
                        /** @var Param $param */
                        $param = array_values($bodyParams)[0];
                        if (!array_key_exists($param->name, $data) &&
                            array_key_exists(Defaults::$fullRequestDataName, $data) &&
                            !is_null($d = $data[Defaults::$fullRequestDataName])) {
                            $data[$param->name] = $d;
                        }
                    }
                }
                $headerParams = $route->filterParams(true, Param::FROM_HEADER);
                foreach ($headerParams as $param) {
                    if ($request && $request->hasHeader($param->name)) {
                        $data[$param->name] = $request->getHeaderLine($param->name);
                    } else {
                        unset($data[$param->name]);
                    }
                }
            }
        }
        $route->data = $data;
        return $route;
    }

    protected static function typeMatch(string $type, $var): bool
    {
        switch ($type) {
            case 'boolean':
            case 'bool':
                return is_bool($var);
            case 'array':
            case 'object':
                return is_array($var);
            case 'string':
            case 'int':
            case 'integer':
            case 'float':
            case 'number':
                return is_scalar($var);
        }
        return true;
    }

    /**
     * @access private
     * @param $var
     * @return string
     */
    protected static function pathVarTypeOf($var): string
    {
        if (is_numeric($var)) {
            return 'n';
        }
        if ($var === 'true' || $var === 'false') {
            return 'b';
        }
        return 's';
    }

    /**
     * @param ServerRequestInterface|null $request
     * @param callable $maker
     * @param array $excludedPaths
     * @param array $excludedHttpMethods
     * @param int $version
     * @return array
     */
    public static function findAll(
        ServerRequestInterface $request,
        callable $maker,
        array $excludedPaths = [],
        array $excludedHttpMethods = [],
        int $version = 1
    ): array {
        $map = [];
        $all = self::$routes["v$version"];
        $filter = [];
        if (isset($all['*'])) {
            $all = $all['*'] + $all;
            unset($all['*']);
        }
        $verifiedAuthClasses = [];
        if (is_array($all)) {
            foreach ($all as $fullPath => $routes) {
                /**
                 * @var string $httpMethod
                 * @var Route $route
                 */
                foreach ($routes as $httpMethod => $route) {
                    if (in_array($httpMethod, $excludedHttpMethods)) {
                        continue;
                    }
                    foreach ($excludedPaths as $exclude) {
                        if (empty($exclude)) {
                            if ($fullPath == $exclude || $fullPath == 'index') {
                                continue 2;
                            }
                        } elseif (Text::beginsWith($fullPath, $exclude)) {
                            continue 2;
                        }
                    }
                    $hash = "$httpMethod " . $route->url;
                    if (!isset($filter[$hash])) {
                        $route->httpMethod = $httpMethod;
                        $map[$route->path][] = [
                            'access' => static::verifyAccess(
                                $route,
                                $request,
                                $maker,
                                $verifiedAuthClasses
                            ),
                            'route' => $route,
                            'hash' => $hash,
                        ];
                        $filter[$hash] = true;
                    }
                }
            }
        }
        ksort($map, SORT_NATURAL);
        return $map;
    }

    /**
     * @param Route $route
     * @param ServerRequestInterface $request
     * @param callable $maker
     * @param array $verifiedClasses
     * @return bool
     */
    public static function verifyAccess(
        Route $route,
        ServerRequestInterface $request,
        callable $maker,
        array &$verifiedClasses
    ): bool {
        if ($route->access <= Route::ACCESS_HYBRID) {
            return true;
        }
        $ignore = new ResponseHeaders();
        $authenticated = false;
        foreach ($route->authClasses as $class) {
            $accessControl = Type::implements($class, AccessControlInterface::class);
            if ($accessControl || !array_key_exists($class, $verifiedClasses)) {
                try {
                    $req = $request->withMethod($route->httpMethod)
                        ->withUri($request->getUri()->withPath('/' . $route->path));
                    /** @var AuthenticationInterface $instance */
                    $instance = $maker($class, $route, true);
                    $userIdentifier = $maker(UserIdentificationInterface::class, $route);
                    $allowed = $instance->_isAllowed($req, $userIdentifier, $ignore);
                    if ($accessControl) {
                        return $allowed;
                    }
                    $verifiedClasses[$class] = $allowed;
                } catch (HttpException $httpException) {
                    if ($accessControl) {
                        return 401 !== $httpException->getCode();
                    }
                    if (!array_key_exists($class, $verifiedClasses) || false == $verifiedClasses[$class]) {
                        $verifiedClasses[$class] = 401 !== $httpException->getCode();
                    }
                }
            }
            if (true === $verifiedClasses[$class]) {
                $authenticated = true;
            }
        }
        if (!($authenticated)
            && $route->access > Route::ACCESS_HYBRID) {
            return false;
        }
        return true;
    }

    /**
     * Export current routes for cache
     *
     * @return array
     */
    public static function toArray(): array
    {
        return static::$routes;
    }

    /**
     * @param ReflectionClass $class
     * @param bool $forResponse
     * @return array|bool|mixed
     * @throws Exception
     */
    protected static function parseMagic(ReflectionClass $class, bool $forResponse = true)
    {
        if (!$c = CommentParser::parse($class->getDocComment())) {
            return false;
        }
        $p = 'property';
        $r = empty($c[$p]) ? [] : $c[$p];
        $p .= '-' . ($forResponse ? 'read' : 'write');
        if (!empty($c[$p])) {
            $r = array_merge($r, $c[$p]);
        }

        return $r;
    }
}
