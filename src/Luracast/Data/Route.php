<?php


namespace Luracast\Restler\Data;


use GraphQL\Type\Definition\ResolveInfo;
use Luracast\Restler\Contracts\{AuthenticationInterface,
    RequestMediaTypeInterface,
    ResponseMediaTypeInterface,
    SelectivePathsInterface,
    UserIdentificationInterface,
    ValidationInterface
};
use Luracast\Restler\Defaults;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Exceptions\InvalidAuthCredentials;
use Luracast\Restler\GraphQL\Error;
use Luracast\Restler\GraphQL\GraphQL;
use Luracast\Restler\ResponseHeaders;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\Utils\{ClassName, CommentParser, Convert, Type, Validator};
use Psr\Http\Message\ServerRequestInterface;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionUnionType;
use Throwable;

class Route extends ValueObject
{
    public const ACCESS_PUBLIC = 0;
    public const ACCESS_HYBRID = 1;
    public const ACCESS_PROTECTED_BY_COMMENT = 2;
    public const ACCESS_PROTECTED_METHOD = 3;

    public const ACCESS = [
        'public' => self::ACCESS_PUBLIC,
        'hybrid' => self::ACCESS_HYBRID,
        'protected' => self::ACCESS_PROTECTED_BY_COMMENT
    ];


    public const PROPERTY_TAGS = [
        'query',
        'mutation',
        'summary',
        'description',
        'status',
        'header',
        'cache',
        'expires',
        'throttle',
        'throws',
        'view',
        'error-view' => 'errorView',
        'deprecated',
        'resource'
    ];

    public const INTERNAL_TAGS = [
        'param',
        'return',
    ];

    public const METHOD_TAGS = [
        'access' => 'setAccess',
        'class' => 'setClassProperties',
        'format' => 'overrideFormats',
        'request-format' => 'overrideFormats',
        'response-format' => 'overrideFormats',
    ];

    public $query;
    public $mutation;

    public string $httpMethod = 'GET';
    /**
     * @var string|null target uri. human readable, for documentation
     */
    public ?string $url = null;

    /**
     * @var string|null path used for routing
     */
    public ?string $path = null;

    public string $summary = '';

    public string $description = '';

    public ?array $action = null;

    /**
     * @var Param[]
     */
    public array $parameters = [];

    public ?\Luracast\Restler\Data\Returns $return = null;

    /**
     * @var int http status
     */
    public int $status = 200;

    /**
     * @var array headers set through comments
     */
    public array $header = [];

    /**
     * @var string[] cache setting from comments
     */
    public array $cache = [];

    public ?int $expires = null;

    public ?int $throttle = null;

    public bool $deprecated = false;


    public array $resource = ['path' => '', 'summary' => '', 'description' => ''];

    public array $throws = [];

    /**
     * @var int access level
     */
    public int $access = self::ACCESS_PUBLIC;

    public array $requestMediaTypes = [];

    public array $responseMediaTypes = [];

    /**
     * @internal
     */
    public array $requestFormatMap = [];

    /**
     * @internal
     */
    public array $responseFormatMap = [];

    public array $authClasses = [];
    public array $preAuthFilterClasses = [];
    public array $postAuthFilterClasses = [];
    /**
     * @var array [class => [property => $value ...]...]
     * values to set on initialization of classes
     */
    public array $set = [];
    /**
     * @var array|mixed|string[]
     */
    public $scope;
    /**
     * @var array
     */
    protected $arguments = [];

    public static function fromMethod(ReflectionMethod $method, ?array $metadata = null, array $scope = []): self
    {
        if (empty($scope)) {
            $scope = Routes::scope($method->getDeclaringClass());
        }
        if (is_null($metadata)) {
            $metadata = CommentParser::parse($method->getDocComment());
        }
        $route = new self();
        if (!empty($metadata)) {
            foreach (self::PROPERTY_TAGS as $key => $property) {
                if (is_numeric($key)) {
                    $key = $property;
                }
                if (isset($metadata[$key])) {
                    $route->{$property} = $metadata[$key];
                }
            }
        }
        //$methodName = $metadata['url'][0] ?? $method->getName();
        //$methodName = Text::slug(strtok($methodName, '/'),'');
        $methodName = $method->getName();

        if (preg_match_all('/^(GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS)/i', $methodName, $matches)) {
            $route->httpMethod = strtoupper($matches[0][0]);
            $methodName = substr($methodName, strlen($route->httpMethod));
        } else {
            $route->httpMethod = 'GET';
        }
        $route->url = str_replace('index', '', $methodName);
        $route->action = [$method->class, $method->getName()];
        $reflectionType = $method->hasReturnType() ? $method->getReturnType() : null;
        if ($reflectionType instanceof ReflectionUnionType) {
            $types = $reflectionType->getTypes();
            if ('null' === end($types)->getName()) {
                $metadata['return']['type'][] = 'null';
            }
            $reflectionType = $types[0];
        }
        $route->return = Returns::fromReturnType(
            $reflectionType,
            $metadata['return'] ?? ['type' => ['array']],
            $scope
        );
        $route->parameters = Param::fromMethod($method, $metadata, $scope);
        foreach (self::METHOD_TAGS as $key => $func) {
            call_user_func([$route, $func], $key, $metadata[$key] ?? null, $method, $metadata, $scope);
        }
        if (empty($route->responseMediaTypes)) {
            $route->responseMediaTypes = Routes::$responseMediaTypes;
        }
        if (empty($route->requestFormatMap)) {
            $route->requestFormatMap = Routes::$requestFormatMap;
        } elseif (empty($route->requestFormatMap['default'])) {
            $route->requestFormatMap['default'] = array_values($route->requestFormatMap)[0];
        }
        if (empty($route->requestMediaTypes)) {
            $route->requestMediaTypes = Routes::$requestMediaTypes;
        }
        if (empty($route->responseFormatMap)) {
            $route->responseFormatMap = Routes::$responseFormatMap;
        } elseif (empty($route->responseFormatMap['default'])) {
            $route->responseFormatMap['default'] = array_values($route->responseFormatMap)[0];
        }
        $route->scope = $scope;

        return $route;
    }

    public function withLink(string $url, string $httpMethod = 'GET'): self
    {
        $instance = clone $this;
        $instance->url = $url;
        $instance->httpMethod = $httpMethod;
        $prevPathParams = $instance->filterParams(true, Param::FROM_PATH);
        $pathParams = [];
        //compute from the human readable url to machine computable typed route path
        $instance->path = preg_replace_callback(
            '/{[^}]+}|:[^\/]+/',
            function ($matches) use (&$pathParams, $instance): string {
                $match = trim($matches[0], '{}:');
                $param = $instance->parameters[$match];
                $param->from = Param::FROM_PATH;
                $param->required = true;
                $pathParams[$match] = $param;
                return '{' . Routes::typeChar($param->type) . $param->index . '}';
            },
            $instance->url
        );
        $noBody = 'GET' === $httpMethod || 'DELETE' === $httpMethod;
        foreach ($prevPathParams as $name => $param) {
            //remap unused path parameters to query or body
            if (!isset($pathParams[$name])) {
                $param->from = $noBody ? Param::FROM_QUERY : Param::FROM_BODY;
            }
        }
        if ($noBody) {
            //map body parameters to query
            $bodyParams = $instance->filterParams(true, Param::FROM_BODY);
            foreach ($bodyParams as $name => $param) {
                $param->from = Param::FROM_QUERY;
            }
        }
        $instance->setAuthAndFilters();
        return $instance;
    }

    public function filterParams(bool $include, string $from = Param::FROM_BODY): array
    {
        return array_filter(
            $this->parameters,
            fn($v) => $include ? $from === $v->from : $from !== $v->from
        );
    }

    private function setAuthAndFilters(): void
    {
        foreach (Routes::$preAuthFilterClasses as $preFilter) {
            if (Type::implements($preFilter, SelectivePathsInterface::class)) {
                if (!$preFilter::isPathSelected($this->path)) {
                    continue;
                }
            }
            $this->preAuthFilterClasses[] = $preFilter;
        }
        foreach (Routes::$authClasses as $authClass) {
            if (Type::implements($authClass, SelectivePathsInterface::class)) {
                if (!$authClass::isPathSelected($this->path)) {
                    continue;
                }
            }
            $this->authClasses[] = $authClass;
        }
        foreach (Routes::$postAuthFilterClasses as $postFilter) {
            if (Type::implements($postFilter, SelectivePathsInterface::class)) {
                if (!$postFilter::isPathSelected($this->path)) {
                    continue;
                }
            }
            $this->postAuthFilterClasses[] = $postFilter;
        }
    }

    public function addParameter(Param $parameter): void
    {
        $parameter->index = count($this->parameters);
        $this->parameters[$parameter->name] = $parameter;
    }

    public function __clone()
    {
        $this->parameters = array_map(
            fn($param) => clone $param,
            $this->parameters
        );
        $this->return = clone $this->return;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function toGraphQL(): array
    {
        $config = [
            'type' => $this->return->toGraphQL(),
            'args' => [],
            'resolve' => function ($root, $args, array $context, ResolveInfo $info) {
                try {
                    /** @var Restler $restler */
                    $restler = $context['restler'];
                    $authenticated = $this->authenticate(
                        $context['request'],
                        $restler->responseHeaders,
                        $context['maker'],
                        max($this->access, GraphQL::$apiAccessLevel ?? Defaults::$apiAccessLevel)
                    );
                    $context['root'] = $root;
                    $context['info'] = $info;
                    /** @var Convert $convert */
                    $convert = $context['maker'](Convert::class);
                    return $convert->toArray($this->call($args, $authenticated, true, $context['maker']));
                } catch (Throwable $throwable) {
                    $source = strtolower(pathinfo($throwable->getFile(), PATHINFO_FILENAME));
                    throw new Error($source, $throwable);
                }
            }
        ];
        /**
         * @var string $name
         * @var Type $param
         */
        foreach ($this->parameters as $name => $param) {
            $config['args'][$name] = $param->toGraphQL();
        }
        return $config;
    }

    public function authenticate(
        ServerRequestInterface $request,
        ResponseHeaders $responseHeaders,
        callable $maker,
        ?int $accessLevel = null
    ): bool {
        if (is_null($accessLevel)) {
            $accessLevel = $this->access;
        }
        if (!$accessLevel) {
            return false;
        }
        if ($accessLevel > self::ACCESS_HYBRID && empty($this->authClasses)) {
            throw new HttpException(
                403,
                'access denied. no applicable authentication class.'
            );
        }
        $unauthorized = false;
        foreach ($this->authClasses as $i => $authClass) {
            try {
                /** @var AuthenticationInterface $auth */
                $auth = call_user_func($maker, $authClass, $this);
                $userIdentifier = $maker(UserIdentificationInterface::class);
                if (!$auth->_isAllowed($request, $userIdentifier, $responseHeaders)) {
                    throw new HttpException(401, null, ['from' => $authClass]);
                }
                $unauthorized = false;
                //make this auth class as the first one
                array_splice($this->authClasses, $i, 1);
                array_unshift($this->authClasses, $authClass);
                break;
            } catch (InvalidAuthCredentials $e) { //provided credentials does not authenticate
                throw $e;
            } catch (HttpException $e) {
                if (!$unauthorized) {
                    $unauthorized = $e;
                }
            }
        }
        if ($accessLevel > self::ACCESS_HYBRID && $unauthorized) {
            throw $unauthorized;
        }
        return $unauthorized ? false : true;
    }

    public function call(array $arguments, bool $authenticated = false, bool $validate = true, callable $maker = null)
    {
        if (!$maker) {
            $maker = fn($class) => new $class();
        }
        $this->apply($arguments, $authenticated);
        if ($validate) {
            $this->validate($maker(Validator::class), $maker);
        }
        return $this->handle($maker);
    }

    public function apply(array $arguments, bool $authenticated = false): array
    {
        $p = [];
        foreach ($this->parameters as $parameter) {
            if (
                Param::ACCESS_PRIVATE === $parameter->access ||
                (!$authenticated && Param::ACCESS_PROTECTED === $parameter->access)
            ) {
                $p[$parameter->index] = $parameter->default[1];
            } elseif ($parameter->variadic) {
                $p[$parameter->index] = $arguments[$parameter->name]
                    ?? array_slice($arguments, $parameter->index);
            } else {
                $p[$parameter->index] = $arguments[$parameter->name]
                    ?? $arguments[$parameter->index]
                    ?? $parameter->default[1]
                    ?? null;
            }
        }
        if (empty($p) && !empty($arguments)) {
            $this->arguments = array_values($arguments);
        } else {
            $this->arguments = $p;
        }
        return $p;
    }

    public function validate(ValidationInterface $validator, callable $maker): void
    {
        foreach ($this->parameters as $param) {
            $i = $param->index;
            $info = &$param->rules;
            if (!isset ($info['validate']) || $info['validate'] != false) {
                if (isset($info['method'])) {
                    $param->apiClassInstance = $maker($this->action[0]);
                }
                $value = $this->arguments[$i];
                $this->arguments[$i] = null;
                if (empty(Validator::$exceptions)) {
                    $info['autofocus'] = true;
                }
                $this->arguments[$i] = $validator::validate($value, $param);
                unset($info['autofocus']);
            }
        }
    }

    public function handle(callable $maker)
    {
        $arguments = [];
        if ($this->parameters) {
            foreach ($this->parameters as $param) {
                $argument = $this->arguments[$param->index];
                //expand variadic parameters
                $param->variadic
                    ? $arguments = array_merge($arguments, $argument)
                    : $arguments [] = $argument;
            }
        } else {
            $arguments = $this->arguments;
        }
        $action = $this->action;
        switch ($this->access) {
            case self::ACCESS_PROTECTED_METHOD:
                $object = $maker($action[0]);
                $reflectionMethod = new ReflectionMethod(
                    $object,
                    $action[1]
                );
                $reflectionMethod->setAccessible(true);
                return $reflectionMethod->invokeArgs(
                    $object,
                    $arguments
                );
            default:
                if (is_array($action) && count($action) && is_string($action[0]) && class_exists($action[0])) {
                    $action[0] = $maker($action[0]);
                }
                return call_user_func_array($action, $arguments);
        }
    }

    public function __toString()
    {
        if (is_array($this->action)) {
            $action = $this->action;
            if (!is_string($action[0])) {
                $action[0] = get_class($action[0]);
            }
            return implode('::', $action) . '()';
        }
        if (is_string($this->action)) {
            return $this->action . '()';
        }
        return 'closure()';
    }

    private function setAccess(
        string $name,
        ?string $access = null,
        ReflectionFunctionAbstract $function,
        ?array $metadata = null,
        array $scope = []
    ): void {
        if ($function->isProtected()) {
            $this->access = self::ACCESS_PROTECTED_METHOD;
        } elseif ($access = self::ACCESS[$access ?? ''] ?? null) {
            $this->access = $access;
        } elseif (isset($metadata['protected'])) {
            $this->access = self::ACCESS_PROTECTED_BY_COMMENT;
        }
    }

    private function setClassProperties(
        string $name,
        ?array $class = null,
        ReflectionFunctionAbstract $function,
        ?array $metadata = null,
        array $scope = []
    ): void {
        $classes = $class ?? [];
        foreach ($classes as $class => $value) {
            $class = ClassName::resolve($class, $scope);
            $value = $value[CommentParser::$embeddedDataName] ?? [];
            foreach ($value as $k => $v) {
                $this->set[$class][$k] = $v;
            }
        }
    }

    private function overrideFormats(
        string $name,
        ?array $formats = null,
        ReflectionFunctionAbstract $function,
        ?array $metadata = null,
        array $scope = []
    ): void {
        if (!$formats) {
            return;
        }
        $overrides = [];
        $resolver = function ($value) use ($scope, &$overrides) {
            $value = ClassName::resolve(trim($value), $scope);
            foreach ($overrides as $key => $override) {
                if (false === array_search($value, $override)) {
                    throw new HttpException(
                        500,
                        "Given media type is not present in overriding list. " .
                        "Please call `Router::setOverriding{$key}MediaTypes(\"$value\");` before other router methods."
                    );
                }
            }
            return $value;
        };
        $overrides = [
            'Request' => Routes::$requestMediaTypeOverrides,
            'Response' => Routes::$responseMediaTypeOverrides,
        ];
        switch ($name) {
            case 'request-format':
                unset($overrides['Response']);
                $formats = array_map($resolver, $formats);
                $this->setRequestMediaTypes(...$formats);
                break;
            case 'response-format':
                unset($overrides['Request']);
                $formats = array_map($resolver, $formats);
                $this->setResponseMediaTypes(...$formats);
                break;
            default:
                $formats = array_map($resolver, $formats);
                $this->setRequestMediaTypes(...$formats);
                $this->setResponseMediaTypes(...$formats);
        }
    }

    public function setRequestMediaTypes(string ...$types): void
    {
        Routes::_setMediaTypes(
            RequestMediaTypeInterface::class,
            $types,
            $this->requestFormatMap,
            $this->requestMediaTypes
        );
    }

    public function setResponseMediaTypes(string ...$types): void
    {
        Routes::_setMediaTypes(
            ResponseMediaTypeInterface::class,
            $types,
            $this->responseFormatMap,
            $this->responseMediaTypes
        );
    }
}
