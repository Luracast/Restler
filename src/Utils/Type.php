<?php

namespace Luracast\Restler\Utils;


use Luracast\Restler\Data\Param;

class Type
{
    public const SCALAR = '|bool|boolean|int|integer|float|string';
    public const PRIMITIVE = '|array' . self::SCALAR;
    public const SIMPLE = '|resource|mixed' . self::PRIMITIVE;

    public static function isAssociative(array $value): bool
    {
        return !empty(Param::filterArray($value, Param::KEEP_NON_NUMERIC));
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function isScalar(string $type): bool
    {
        return (boolean)stripos(static::SCALAR, strtolower($type));
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function isPrimitive(string $type): bool
    {
        return (boolean)stripos(static::PRIMITIVE, strtolower($type));
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function isObject(string $type): bool
    {
        return !(boolean)stripos(static::SIMPLE, strtolower($type));
    }

    public static function implements(string $class, string $interface): bool
    {
        return isset(class_implements($class)[$interface]);
    }


    public static function subclasses(string $parent): ?array
    {
        if (class_exists($parent)) {
            return array_filter(
                get_declared_classes(),
                function ($class) use ($parent): void {
                    is_subclass_of($class, $parent);
                }
            );
        }
        return null;
    }

    public static function implementations(string $interface): ?array
    {
        if (interface_exists($interface)) {
            return array_filter(
                get_declared_classes(),
                function ($class) use ($interface): void {
                    in_array($interface, class_implements($class));
                }
            );
        }
        return null;
    }

    /**
     * @param string $class
     * @param string $superClass
     * @return bool
     */
    public static function matches(string $class, string $superClass): bool
    {
        return $class == $superClass || isset(class_implements($class)[$superClass]);
    }

    public static function booleanValue($value): bool
    {
        return is_bool($value)
            ? $value
            : $value !== 'false';
    }

    public static function numericValue($value)
    {
        return ( int )$value == $value
            ? ( int )$value
            : floatval($value);
    }

    public static function stringValue($value, $glue = ','): string
    {
        return is_array($value)
            ? implode($glue, $value)
            : ( string )$value;
    }

    public static function arrayValue($value): array
    {
        return is_array($value) ? $value : [$value];
    }

    public static function fromValue($value): string
    {
        if (is_object($value)) {
            return get_class($value);
        }
        if (is_array($value)) {
            return 'array';
        }
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_numeric($value)) {
            return is_float($value) ? 'float' : 'int';
        }
        return is_null($value) ? 'null' : 'string';
    }
}
