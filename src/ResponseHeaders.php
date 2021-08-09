<?php


namespace Luracast\Restler;

use ArrayAccess;

class ResponseHeaders implements ArrayAccess
{
    private $container = [];

    public function getArrayCopy(): array
    {
        return $this->container;
    }

    public function __get($name)
    {
        return $this->container[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->container[$name] = $value;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
