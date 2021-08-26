<?php

namespace Luracast\Restler;

use ArrayAccess;
use ReflectionClass;
use ReflectionException;

class StaticProperties implements ArrayAccess
{
    private array $allowed = [];
    private array $properties = [];
    private string $className;

    /**
     * StaticProperties constructor.
     * @param string $className class name for capturing static properties
     * @param array|null $allowed {@type string} names of the static properties
     */
    public function __construct(string $className, ?array $allowed = null)
    {
        $this->className = $className;
        try {
            $this->allowed = array_fill_keys(
                $allowed ?? array_keys((new ReflectionClass($className))->getStaticProperties()),
                true
            );
        } catch (ReflectionException $e) {
            $this->allowed = [];
        }
    }

    public function &__get($name)
    {
        if (static::__isset($name)) {
            $value = $this->className::$$name;
            if (!array_key_exists($name, $this->properties)) {
                $this->properties[$name] = [$value, $value];
            }
            $newValue = &$this->properties[$name][0];
            $oldValue = &$this->properties[$name][1];
            if ($value === $oldValue) {
                return $newValue;
            }
            return $value;
        }
    }

    public function __isset($name)
    {
        return isset($this->allowed[$name]);
    }

    public function __set($name, $value)
    {
        if (static::__isset($name)) {
            $this->properties[$name] = [$value, $this->className::$$name];
        }
    }

    public function __unset($name)
    {
        unset($this->properties[$name]);
    }

    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function &offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->__set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->__unset($offset);
    }
}
