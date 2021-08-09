<?php
namespace Luracast\Restler\Data;

use Exception;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as GraphQLType;
use Luracast\Restler\Defaults;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\GraphQL\GraphQL;
use Luracast\Restler\Routes;
use Luracast\Restler\Utils\ClassName;
use Luracast\Restler\Utils\CommentParser;
use Luracast\Restler\Utils\Text;
use Luracast\Restler\Utils\Type as TypeUtil;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionUnionType;
use Reflector;

/**
 * ValueObject for validation information. An instance is created and
 * populated by Restler to pass it to iValidate implementing classes for
 * validation
 */
class Param extends Type
{
    public const KEEP_NON_NUMERIC = false;
    public const KEEP_NUMERIC = true;

    public const FROM_PATH = 'path';
    public const FROM_QUERY = 'query';
    public const FROM_BODY = 'body';
    public const FROM_HEADER = 'header';

    public const ACCESS_PUBLIC = 0;
    public const ACCESS_PROTECTED = 1;
    public const ACCESS_PRIVATE = 2;

    public const ACCESS = [
        'public' => self::ACCESS_PUBLIC,
        'protected' => self::ACCESS_PROTECTED,
        'private' => self::ACCESS_PRIVATE,
    ];

    /**
     * Name of the variable being validated
     *
     * @var string variable name
     */
    public $name;
    /**
     * @var int
     */
    public $index;
    /**
     * @var string proper name for given parameter
     */
    public $label;
    /**
     * @var string|null html element that can be used to represent the parameter for
     *      input
     */
    public ?string $field = null;
    /**
     * @var array with hasDefault boolean as the first value and default value for the parameter as second
     */
    public array $default = [false, null];

    /**
     * @var bool is it required or not
     */
    public ?bool $required = null;

    /**
     * @var string body or header or query where this parameter is coming from
     *      in the http request
     */
    public $from;

    /**
     * @var bool variadic parameter, so needs expansion of array
     */
    public bool $variadic = false;

    /**
     * Should we attempt to fix the value?
     * When set to false validation class should throw
     * an exception or return false for the validate call.
     * When set to true it will attempt to fix the value if possible
     * or throw an exception or return false when it cant be fixed.
     *
     * @var bool true or false
     */
    public bool $fix = false;

    // ==================================================================
    //
    // VALUE RANGE
    //
    // ------------------------------------------------------------------
    /**
     * Given value should match one of the values in the array
     *
     * @var string[] of choices to match to
     */
    public $choice;
    /**
     * If the type is string it will set the lower limit for length
     * else will specify the lower limit for the value
     *
     * @var number minimum value
     */
    public $min;
    /**
     * If the type is string it will set the upper limit limit for length
     * else will specify the upper limit for the value
     *
     * @var number maximum value
     */
    public $max;

    /**
     * only for arrays
     *
     * @var int minimum array count
     */
    public $minCount;
    /**
     * Only for arrays
     *
     * @var int maximum array count
     */
    public $maxCount;

    // ==================================================================
    //
    // CUSTOM VALIDATION
    //
    // ------------------------------------------------------------------
    /**
     * Rules specified for the parameter in the php doc comment.
     * It is passed to the validation method as the second parameter
     *
     * @var array custom rule set
     */
    public $rules;

    /**
     * Specifying a custom error message will override the standard error
     * message return by the validator class
     *
     * @var string custom error response
     */
    public $message;

    // ==================================================================
    //
    // METHODS
    //
    // ------------------------------------------------------------------

    /**
     * Name of the method to be used for validation.
     * It will be receiving two parameters $input, $rules (array)
     *
     * @var string validation method name
     */
    public string $method;

    public int $access = self::ACCESS_PUBLIC;

    /**
     * Instance of the API class currently being called. It will be null most of
     * the time. Only when method is defined it will contain an instance.
     * This behavior is for lazy loading of the API class
     *
     * @var null|object will be null or api class instance
     */
    public ?object $apiClassInstance = null;

    public static function fromMethod(ReflectionMethod $method, ?array $doc = null, array $scope = []): array
    {
        if (empty($scope)) {
            $scope = Routes::scope($method->getDeclaringClass());
        }
        return static::fromAbstract($method, $doc, $scope);
    }

    private static function fromAbstract(
        ReflectionFunctionAbstract $function,
        ?array $doc = null,
        array $scope = []
    ): array {
        if (is_null($doc)) {
            try {
                $doc = CommentParser::parse($function->getDocComment());
            } catch (Exception $e) {
                //ignore
            }
        }
        $params = [];
        foreach ($function->getParameters() as $reflectionParameter) {
            $params[] = static::fromParameter($reflectionParameter, $doc, $scope);
        }
        return array_column($params, null, 'name');
    }

    public static function fromParameter(ReflectionParameter $parameter, ?array $doc, array $scope): self
    {
        $param = static::from($parameter, $doc['param'][$parameter->getPosition()] ?? [], $scope);
        if ($parameter->isVariadic()) {
            $param->multiple = true;
            $param->variadic = true;
        }
        return $param;
    }

    protected static function from(?Reflector $reflector, array $metadata = [], array $scope = [])
    {
        $hasDefault = false;
        $instance = new static();
        $types = $metadata['type'] ?? [];
        $properties = $metadata[CommentParser::$embeddedDataName] ?? [];
        $itemTypes = $properties['type'] ?? [];
        unset($properties['type']);
        $instance->description = $metadata['description'] ?? '';
        if ($reflector && method_exists(
                $reflector,
                'isDefaultValueAvailable'
            ) && $reflector->isDefaultValueAvailable()) {
            $default = $reflector->getDefaultValue();
            $instance->default = [true, $default];
            $hasDefault = true;
            $types[] = TypeUtil::fromValue($default);
        }
        if ($reflector && Defaults::$fullRequestDataName === $reflector->name) {
            $types = ['array'];
            $instance->format = 'associative';
            $itemTypes = [];
        } elseif (empty($types)) {
            array_unshift($types, 'string');
        } elseif (in_array('array', $types) && empty($itemTypes)) {
            array_unshift($itemTypes, 'string');
        }
        if ($reflector && method_exists($reflector, 'hasType') && $reflector->hasType()) {
            $reflectionType = $reflector->getType();
            if ($reflectionType instanceof ReflectionUnionType) {
                $reflectionTypes = $reflectionType->getTypes();
                if ('null' === end($reflectionTypes)->getName()) {
                    $metadata['return']['type'][] = 'null';
                }
                $reflectionType = $reflectionTypes[0];
            }
        }
        $instance->apply(
            $reflectionType ?? null,
            $types,
            $itemTypes,
            $scope
        );
        $instance->required = TypeUtil::booleanValue(
            $properties['required'] ?? $reflector && method_exists(
                $reflector,
                'isOptional'
            ) && !$reflector->isOptional()
        );
        if ($reflector) {
            $instance->name = $reflector->getName();
            if (method_exists($reflector, 'getPosition')) {
                $instance->index = $reflector->getPosition();
            }
        } else {
            $instance->name = $metadata['name'] ?? null;
        }

        $instance->label = $properties['label']
            ?? Text::title($instance->name);

        if (isset($properties['min'])) {
            $instance->minCount = $properties['min'][0];
            $instance->min = $properties['min'][1];
        }
        if (isset($properties['max'])) {
            $instance->maxCount = $properties['max'][0];
            $instance->max = $properties['max'][1];
        }
        $instance->pattern = $properties['pattern'] ?? null;
        $instance->message = $properties['message'] ?? null;
        $instance->choice = $properties['choice'] ?? null;
        unset($properties['choice']);
        $instance->fix = $properties['fix'] ?? false;

        $instance->from = $properties['from']
            ?? (
            in_array($instance->name, Routes::$prefixingParameterNames)
                ? self::FROM_PATH
                : self::FROM_BODY
            );
        if (!$instance->format) {
            $instance->format = $properties['format']
                ?? Routes::$formatsByName[$instance->name]
                ?? null;
        }
        if ($access = self::ACCESS[$properties['access'] ?? ''] ?? false) {
            unset($properties['access']);
            if (!$hasDefault) {
                if (array_key_exists('default', $properties)) {
                    $instance->default = [true, $properties['default']];
                } elseif ($instance->nullable) {
                    $instance->default = [true, null];
                } else {
                    throw new Exception(
                        'Invalid parameter. private or protected parameter requires ' .
                        'default value either in the function or with {@default value} comment'
                    );
                }
            }
            $instance->access = $access;
        }
        $instance->rules = $properties;
        return $instance;
    }

    public static function fromFunction(ReflectionFunction $function, ?array $doc = null, array $scope = []): array
    {
        if (empty($scope)) {
            $scope = Routes::scope($function->getClosureScopeClass());
        }
        return static::fromAbstract($function, $doc, $scope);
    }

    public static function filterArray(array $data, bool $onlyNumericKeys): array
    {
        $callback = $onlyNumericKeys ? 'is_numeric' : 'is_string';
        return array_filter($data, $callback, ARRAY_FILTER_USE_KEY);
    }

    public function toGraphQL()
    {
        if (in_array($this->type, GraphQL::INVALID_TYPES)) {
            throw new HttpException(500, 'Parameter with data type `' . $this->type . '` is not supported');
        }
        $data = [];
        if (GraphQL::$showDescriptions && $this->description) {
            $data['description'] = $this->description;
        }
        if (!empty($this->choice)) {
            $keys = $this->rules['select'] ?? $this->choice;
            if (count($this->choice) !== count($keys)) {
                throw new HttpException(500, '`@choice` and `@select` items count mismatch');
            }
            $type = GraphQL::enum(
                [
                    'name' => ucfirst($this->name) . 'Enum',
                    'values' => array_combine($keys, $this->choice),
                ]
            );
        } elseif ($this->scalar) {
            $type = $this->type !== 'bool' && in_array($this->name, Routes::$prefixingParameterNames)
                ? GraphQLType::id()
                : call_user_func([GraphQLType::class, $this->type]);
            if (!$this->required && $this->default[0]) {
                $data['defaultValue'] = $this->default[1];
            }
        } else {
            $class = ClassName::short($this->type) . 'Input';
            if (isset(GraphQL::$definitions[$class])) {
                $type = GraphQL::$definitions[$class];
            } else {
                $config = ['name' => $class, 'fields' => []];
                if (is_array($this->properties)) {
                    /** @var Type $property */
                    foreach ($this->properties as $name => $property) {
                        $config['fields'][$name] = $property->toGraphQL();
                    }
                }
                $type = $this instanceof Param
                    ? new InputObjectType($config)
                    : new ObjectType($config);
            }
            GraphQL::$definitions[$class] = $type;
        }
        if (!$this->nullable) {
            $type = GraphQLType::nonNull($type);
        }
        if ($this->multiple) {
            $type = GraphQLType::listOf($type);
        }
        $data['type'] = $type;
        return $data;
    }
}

