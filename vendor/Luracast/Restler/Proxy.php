<?php
namespace Luracast\Restler;


class Proxy
{
    private $instance;
    private $class;
    private static $log = array();

    public function __construct($for)
    {
        $this->instance = $for;
        $this->class = get_class($for);
    }

    public function __destruct()
    {
        Scope::$restler->cache->set('log',static::$log);
    }

    public function getClass(){
        return $this->class;
    }

    public function getInstance(){
        return $this->instance;
    }

    public function __call($name, $arguments)
    {
        static::$log[]="{$this->class}->$name()";
        return call_user_func_array(
            array($this->instance, $name),
            $arguments
        );
    }

    public function __callStatic($name, $arguments)
    {
        static::$log[]="{$this->class}::$name()";
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