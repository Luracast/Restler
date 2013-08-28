<?php
namespace Luracast\Restler;


class Proxy
{
    private $instance;

    public function __construct($for)
    {
        $this->instance = $for;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(
            array($this->instance, $name),
            $arguments
        );
    }

    public function __callStatic($name, $arguments)
    {
        return call_user_func_array(
            get_class($this->instance) . ':' . $name,
            $arguments
        );
    }

    public function __get($name)
    {
        return $this->instance->$name;
    }

    public function __set($name, $value)
    {
        $this->instance->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->instance->$name);
    }

    public function __unset($name)
    {
        unset($this->instance->$name);
    }

}