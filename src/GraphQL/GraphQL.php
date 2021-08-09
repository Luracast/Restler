<?php


namespace Luracast\Restler\GraphQL;

use Exception;
use GraphQL\Error\DebugFlag;
use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use Illuminate\Support\Str;
use Luracast\Restler\Contracts\AccessControlInterface;
use Luracast\Restler\Contracts\AuthenticationInterface;
use Luracast\Restler\Contracts\DependentTrait;
use Luracast\Restler\Data\Route;
use Luracast\Restler\Defaults;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\StaticProperties;
use Luracast\Restler\Utils\ClassName;
use Luracast\Restler\Utils\CommentParser;
use Luracast\Restler\Utils\PassThrough;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

/**
 * query language support
 */
class GraphQL
{
    use DependentTrait;

    public const UI_GRAPHQL_PLAYGROUND = 'graphql-playground';
    public const UI_GRAPHIQL = 'graphiql';
    public const INVALID_TYPES = ['mixed', 'array'];
    public static string $UI = self::UI_GRAPHQL_PLAYGROUND;

    public static array $serverConfig = [
        'rootValue' => ['prefix' => 'You said: '],
        'queryBatching' => true,
        'debugFlag' => DebugFlag::NONE,
    ];

    public static array $context = [];
    public static array $definitions = [];
    public static array $mutations = [];
    public static array $queries = [];
    public static bool $showDescriptions = false;
    /**
     * Access Control - uses Defaults::$apiAccessLevel when set to null
     *
     * @var int|null set the default api access mode
     *      value of 0 = public api
     *      value of 1 = hybrid api using `@access hybrid` comment
     *      value of 2 = protected api using `@access protected` comment
     *      value of 3 = protected api using `protected function` method
     */
    public static ?int $apiAccessLevel = null;
    private static ?array $authClasses = null;
    private Restler $restler;
    private StaticProperties $graphQL;
    private ServerRequestInterface $request;

    public function __construct(Restler $restler, StaticProperties $graphQL, ServerRequestInterface $request)
    {
        $this->restler = $restler;
        $graphQL->context['restler'] = $restler;
        $graphQL->context['maker'] = [$restler, 'make'];
        $this->request = $graphQL->context['request'] = $request;
        $this->graphQL = $graphQL;
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
     * @param array $map $className => Resource name or just $className
     * @throws Exception
     */
    public static function mapApiClasses(array $map): void
    {
        static::checkDependencies();
        try {
            foreach ($map as $className => $name) {
                if (is_numeric($className)) {
                    $className = $name;
                    $name = ClassName::short($className);
                }
                $className = Defaults::$aliases[$className] ?? $className;
                if (!class_exists($className)) {
                    throw new Exception(
                        'Class not found',
                        500
                    );
                }
                $class = new ReflectionClass($className);
                $methods = $class->getMethods(
                    ReflectionMethod::IS_PUBLIC +
                    ReflectionMethod::IS_PROTECTED
                );
                $scope = null;
                foreach ($methods as $method) {
                    if ($method->isStatic()) {
                        continue;
                    }
                    //method name should not begin with _
                    if ($method->getName()[0] == '_') {
                        continue;
                    }
                    $metadata = [];
                    if ($doc = $method->getDocComment()) {
                        try {
                            $metadata = CommentParser::parse($doc);
                        } catch (Exception $e) {
                            throw new HttpException(
                                500,
                                "Error while parsing comments of `{$className}::{$method->getName()}` method. " . $e->getMessage(
                                )
                            );
                        }
                        //@access should not be private
                        if ('private' == ($metadata['access'] ?? false)) {
                            continue;
                        }
                    }
                    if (is_null($scope)) {
                        $scope = Routes::scope($class);
                    }
                    static::addMethod($method, $name, $metadata, $scope);
                }
            }
        } catch (Throwable $e) {
            throw new HttpException(
                $e->getCode(),
                "Failed to map `$className` class to GraphQL. " . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public static function addMethod(
        ReflectionMethod $method,
        string $baseName = '',
        ?array $metadata = null,
        array $scope = []
    ): void {
        $route = Route::fromMethod($method, $metadata, $scope);
        $route->authClasses = static::$authClasses;
        if ($mutation = $route->mutation ?? false) {
            static::addRoute($mutation, $route, true);
            return;
        }
        if ($query = $route->query ?? false) {
            static::addRoute($query, $route, false);
            return;
        }
        if (!empty($route->url)) {
            $name = empty($baseName) ? lcfirst($route->url) : lcfirst($baseName) . ucfirst($route->url);
        } else {
            $single = empty($baseName) ? '' : Str::singular($baseName);
            switch ($route->httpMethod) {
                case 'POST':
                    $name = 'make' . $single;
                    break;
                case 'DELETE':
                    $name = 'remove' . $single;
                    break;
                case 'PUT':
                case 'PATCH':
                    $name = 'update' . $single;
                    break;
                default:
                    $name = isset($route->parameters['id'])
                        ? 'get' . $single
                        : lcfirst($baseName);
            }
        }
        static::addRoute($name, $route, 'GET' !== $route->httpMethod);
    }

    public static function addRoute(string $name, Route $route, bool $isMutation = false): void
    {
        $target = $isMutation ? 'mutations' : 'queries';
        static::$$target[$name] = $route->toGraphQL();
    }

    /**
     * @return array {@type associative}
     *               CLASS_NAME => vendor/project:version
     */
    public static function dependencies(): array
    {
        return ['GraphQL\Type\Definition\Type' => 'webonyx/graphql-php'];
    }

    /**
     * Creates enum and makes sure is name is unique to avoid conflicts
     * @param array $config
     * @return EnumType
     */
    public static function enum(array $config): EnumType
    {
        $name = $config['name'] ?? 'enum';
        $number = 1;
        while (isset(self::$definitions[$name])) {
            $config['name'] = $name = $name . (++$number);
        }
        self::$definitions[$name] = new EnumType($config);
        return self::$definitions[$name];
    }

    /**
     * loads graphql client
     * @param string $query
     * @param array $variables
     * @return ResponseInterface|array
     * @throws HttpException
     */
    public function get(string $query = '', array $variables = [])
    {
        if (!empty($query)) {
            return $this->handle();
        }
        return PassThrough::file(__DIR__ . '/client/' . static::$UI . '.html');
    }

    private function handle()
    {
        try {
            $data = [];
            $data['query'] = new ObjectType(['name' => 'Query', 'fields' => static::$queries]);
            if (!empty(self::$mutations)) {
                $data['mutation'] = new ObjectType(['name' => 'Mutation', 'fields' => static::$mutations]);
            }
            $schema = new Schema($data);
            $config = ServerConfig::create(
                static::$serverConfig +
                ['context' => $this->graphQL->context, 'schema' => $schema]
            );
            $server = new StandardServer($config);
            return $server->executePsrRequest(
                $this->request->withParsedBody(json_decode((string)$this->request->getBody(), true))
            );
        } catch (Exception $exception) {
            return [
                'errors' => [['message' => $exception->getMessage()]]
            ];
        }
    }

    /**
     * runs graphql queries
     * @param string $query {@from body}
     * @param array $variables {@from body}
     *
     * @return array|mixed[]
     */
    public function post(string $query = '', array $variables = [])
    {
        return $this->handle();
    }
}
