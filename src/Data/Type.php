<?php


namespace Luracast\Restler\Data;


use Error;
use Exception;
use Luracast\Restler\Contracts\GenericRequestInterface;
use Luracast\Restler\Contracts\GenericResponseInterface;
use Luracast\Restler\Exceptions\Invalid;
use Luracast\Restler\Routes;
use Luracast\Restler\Utils\ClassName;
use Luracast\Restler\Utils\CommentParser;
use Luracast\Restler\Utils\Type as TypeUtil;
use ReflectionClass;
use ReflectionProperty;
use ReflectionType;
use Reflector;

/**
 * @method static string() creates a string
 * @method static nullableString() creates a nullable string
 * @method static stringArray() creates an array of strings
 * @method static nullableStringArray() creates a nullable array of strings
 *
 * @method static int() creates an integer
 * @method static nullableInt() creates a nullable integer
 * @method static intArray() creates an array of integers
 * @method static nullableIntArray() creates a nullable array of integers
 *
 * @method static float() creates a floating point number
 * @method static nullableFloat() creates a nullable floating point number
 * @method static floatArray() creates an array of floating point numbers
 * @method static nullableFloatArray() creates a nullable array of floating point numbers
 *
 * @method static object(string $className, array $properties) creates an object with properties
 * @method static nullableObject(string $className, array $properties) creates a nullable object with properties
 * @method static objectArray(string $className, array $properties) creates an array of objects with given properties
 * @method static nullableObjectArray(string $className, array $properties) creates a nullable array of objects with given properties
 */
abstract class Type extends ValueObject
{
    public const SCALAR = [
        'int' => 'integer',
        'integer' => 'integer',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'float' => 'float',
        'string' => 'string'
    ];

    public const NULLABLE = 0;
    public const NOT_NULLABLE = 1;
    public const DETECT_NULLABLE = 3;

    /**
     * Data type of the variable being validated.
     * It will be mostly string
     *
     * @var string only single type is allowed. if multiple types are specified,
     * Restler will pick the first. if null is one of the values, it will be simply set the nullable flag
     * if multiple is true, type denotes the content type here
     */
    public $type = 'string';

    /**
     * @var bool is it a list?
     */
    public bool $multiple = false;

    /**
     * @var bool can it hold null value?
     */
    public bool $nullable = true;

    /**
     * @var bool does it hold scalar data or object data
     */
    public bool $scalar = true;

    /**
     * @var string|null if the given data can be classified to sub types it will be specified here
     */
    public ?string $format = null;

    /**
     * @var array|null of children to be validated. used only for non scalar type
     */
    public $properties = null;

    public string $description = '';

    public ?string $reference = null;
    // ==================================================================
    //
    // REGEX VALIDATION
    //
    // ------------------------------------------------------------------
    /**
     * RegEx pattern to match the value
     *
     * @var string regular expression
     */
    public $pattern;

    public static function fromProperty(?ReflectionProperty $property, ?array $doc = null, array $scope = [])
    {
        if ($doc) {
            $var = $doc;
        } else {
            try {
                $var = CommentParser::parse($property->getDocComment() ?? '')['var']
                    ?? ['type' => ['string']];
            } catch (Exception $e) {
                //ignore
            }
        }
        return static::from($property, $var, $scope);
    }

    /**
     * @param Reflector|null $reflector
     * @param array $metadata
     * @param array $scope
     * @return static
     */
    protected static function from(?Reflector $reflector, array $metadata = [], array $scope = [])
    {
        $instance = new static();
        $types = $metadata['type'] ?? [];
        $properties = $metadata[CommentParser::$embeddedDataName] ?? [];
        $itemTypes = $properties['type'] ?? [];
        $instance->description = $metadata['description'] ?? '';
        $instance->apply(
            $reflector && method_exists($reflector, 'hasType') && $reflector->hasType()
                ? $reflector->getType() : null,
            $types,
            $itemTypes,
            $scope
        );
        $instance->pattern = $properties['pattern'] ?? null;
        return $instance;
    }

    protected function apply(?ReflectionType $reflectionType, array $types, array $subTypes, array $scope = []): void
    {
        $name = $types[0];
        if ($reflectionType && ($n = $reflectionType->getName()) && $n !== 'Generator') {
            $name = $n;
        }
        $this->nullable = in_array('null', $types);
        if (empty($types) || in_array('mixed', $types) || ($this->nullable && 1 == count($types))) {
            $this->type = 'mixed';
        } elseif ('array' == $name && count($subTypes)) {
            $this->multiple = true;
            $this->type = $subTypes[0];
            $this->scalar = TypeUtil::isScalar($subTypes[0]);
        } else {
            $this->multiple = false;
            $this->type = $name;
            if ($reflectionType) {
                $this->nullable = $reflectionType->allowsNull();
                $this->scalar = 'array' !== $name && $reflectionType->isBuiltin();
            } else {
                $this->scalar = TypeUtil::isScalar($types[0]);
            }
        }
        if (!$this->scalar && $qualified = ClassName::resolve($this->type, $scope)) {
            $this->type = $qualified;
            $class = new ReflectionClass($qualified);
            $isParameter = Param::class === static::class;
            $interface = $isParameter ? GenericRequestInterface::class : GenericResponseInterface::class;
            $method = $isParameter ? 'requests' : 'responds';

            if ($class->implementsInterface($interface)) {
                $generics = explode(',', $this->format);
                foreach ($generics as $key => $generic) {
                    if ($generic = ClassName::resolve($generic, $scope)) {
                        $generics[$key] = $generic;
                    }
                }
                /** @var Type $type */
                $type = call_user_func_array([$class->name, $method], $generics);
                $this->properties = $type->properties;
                $this->type = $type->type;
                $this->multiple = $type->multiple;
                $this->nullable = $type->nullable;
            } else {
                $this->properties = static::propertiesFromClass($class);
            }
        }
    }

    protected static function propertiesFromClass(
        ReflectionClass $reflectionClass,
        array $selectedProperties = [],
        array $requiredProperties = []
    ) {
        $isParameter = Param::class === static::class;
        $filter = !empty($selectedProperties);
        $properties = [];
        $scope = Routes::scope($reflectionClass);
        //When Magic properties exist
        if ($c = CommentParser::parse($reflectionClass->getDocComment())) {
            $p = 'property';
            $magicProperties = empty($c[$p]) ? [] : $c[$p];
            $p .= '-' . ($isParameter ? 'write' : 'read');
            if (!empty($c[$p])) {
                $magicProperties = array_merge($magicProperties, $c[$p]);
            }
            foreach ($magicProperties as $magicProperty) {
                if (!$name = $magicProperty['name'] ?? false) {
                    throw new Exception(
                        '@property comment is not properly defined in ' . $reflectionClass->getName() . ' class'
                    );
                }
                if ($filter && !in_array($name, $selectedProperties)) {
                    continue;
                }
                $properties[$name] = static::from(null, $magicProperty, $scope);
            }
        }
        if (empty($magicProperties)) {
            $reflectionProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($reflectionProperties as $reflectionProperty) {
                if ($reflectionProperty->isStatic()) {
                    continue;
                }
                $name = $reflectionProperty->getName();
                if ($filter && !in_array($name, $selectedProperties)) {
                    continue;
                }
                $properties[$name] = static::fromProperty($reflectionProperty, null, $scope);
            }
        }
        $modifyRequired = !empty($requiredProperties);
        if ($modifyRequired) {
            /**
             * @var string $name
             * @var Type $property
             */
            foreach ($properties as $name => $property) {
                $property->required = in_array($name, $requiredProperties);
            }
        }
        return $properties;
    }

    public static function fromClass(ReflectionClass $reflectionClass)
    {
        $isParameter = Param::class === static::class;
        $interface = $isParameter ? GenericRequestInterface::class : GenericResponseInterface::class;
        $method = $isParameter ? 'requests' : 'responds';
        if ($reflectionClass->implementsInterface($interface)) {
            return call_user_func([$reflectionClass->name, $method]);
        }
        $instance = new static();
        $instance->scalar = false;
        $instance->type = $reflectionClass->name;
        $instance->properties = self::propertiesFromClass($reflectionClass);
        return $instance;
    }

    public static function fromSampleData($data, ?string $name = null, int $nullability = self::NOT_NULLABLE)
    {
        if (is_null($data) || (is_array($data) && empty($data))) {
            throw new Invalid('data can\'t be empty');
        }
        /** @var Type $obj */
        $obj = static::fromValue($data);
        if (is_array($data)) {
            if (empty($name)) {
                throw new Invalid('name can\'t be empty for object type');
            }
            $properties = Param::filterArray($data, Param::KEEP_NON_NUMERIC);
            if (empty($properties)) {
                //array of items
                /** @var Type $value */
                $value = static::fromSampleData($data[0], $name);
                $value->multiple = true;
                return $value;
            }
            foreach ($properties as $key => $value) {
                $obj->properties[$key] = static::fromSampleData($value, $name . ucfirst($key));
            }
            $obj->type = $name;
        }
        switch ($nullability) {
            case self::NULLABLE:
                $obj->nullable = true;
                break;
            case self::NOT_NULLABLE:
                $obj->nullable = false;
                break;
            default:
                $obj->nullable = !(bool)$data;
        }
        return $obj;
    }

    public static function fromValue($value, string $name = 'object'): Type
    {
        $instance = new static();
        if (is_scalar($value)) {
            $instance->scalar = true;
            if (is_numeric($value)) {
                $instance->type = is_float($value) ? 'float' : 'int';
            } elseif (is_bool($value)) {
                $instance->type = 'boolean';
            } elseif (is_null($value)) {
                $instance->nullable = true;
                $instance->type = 'string';
            } else {
                $instance->type = 'string';
            }
        } else {
            $instance->scalar = false;
            $instance->type = $name;
        }
        return $instance;
    }

    public static function make(string $type, bool $multiple = false, bool $nullable = false)
    {
        $instance = static::__set_state(compact('type', 'multiple', 'nullable'));
        $instance->scalar = TypeUtil::isScalar($type);
        return $instance;
    }

    public static function __callStatic($name, $arguments)
    {
        $parts = array_map('strtolower', preg_split('/(?=[A-Z])/', $name));
        $type = array_pop($parts);
        if ('array' == $type) {
            array_unshift($parts, $type);
            $type = array_pop($parts);
        }
        $data = [];
        if ('object' === $type && !empty($arguments) && 2 == count($arguments)) {
            [$name, $properties] = $arguments;
            $data['type'] = $name;
            $data['scalar'] = false;
            $data['properties'] = [];
            if (is_array($properties)) {
                foreach ($properties as $key => $value) {
                    $var = is_array($value) ? array_shift($value) : $value;
                    $args = is_array($value) ? $value : [];
                    $data['properties'][$key] = call_user_func([static::class, __FUNCTION__], $var, $args);
                }
            }
        } elseif ($type = self::SCALAR[$type] ?? false) {
            $data['type'] = $type;
            $data['scalar'] = true;
        } else {
            throw new Error(
                sprintf(
                    "Call to undefined method %s::%s()",
                    static::class,
                    $name
                )
            );
        }
        $instance = new static();
        $instance->applyProperties($data, true);
        $instance->nullable = in_array('nullable', $parts);
        $instance->multiple = in_array('multiple', $parts) || in_array('array', $parts);
        return $instance;
    }

    abstract public function toGraphQL();

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $str = '';
        if ($this->nullable) {
            $str .= '?';
        }
        $str .= $this->type;
        if ($this->multiple) {
            $str .= '[]';
        }
        $str .= '; // ' . static::class;
        if (!$this->scalar) {
            $str = 'new ' . $str;
        }
        return $str;
    }

    public function __sleep(): array
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): array
    {
        return array_filter(parent::jsonSerialize());
    }
}
