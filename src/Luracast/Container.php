<?php
namespace Luracast\Restler;

use Exception;
use Luracast\Restler\Contracts\ContainerInterface;
use Luracast\Restler\Exceptions\ContainerException;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Exceptions\NotFound;
use Luracast\Restler\Utils\ClassName;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Throwable;

class Container implements ContainerInterface
{
    /** @var callable|null */
    private $initiateProperties = null;
    private ?array $instances = null;
    private ?array $config = null;

    public function __construct(&$config = [])
    {
        $this->init($config);
    }

    public function init(&$config): void
    {
        $this->instances = [];
        $this->config = &$config;
    }


    public function config(string $name)
    {
        return $this->config[$name] ?? false;
    }

    public function setPropertyInitializer(callable $function): void
    {
        $this->initiateProperties = $function;
    }

    /**
     * @param $abstract
     * @param array $parameters
     * @return bool|mixed
     * @throws Exception
     */
    public function make($abstract, array $parameters = [])
    {
        return $this->resolve($abstract, $parameters);
    }

    /**
     * Build an instance of the given class
     *
     * @param string $abstract
     * @param array $arguments
     * @return mixed
     *
     * @throws Exception
     * @throws HttpException
     * @throws ReflectionException
     */
    public function &resolve(string $abstract, array &$arguments = [])
    {
        if ($instance = $this->instances[$abstract] ?? false) {
            return $instance;
        }
        $class = ClassName::get($abstract);
        if ($class && $instance = $this->instances[$class] ?? false) {
            return $instance;
        }
        $reflector = new ReflectionClass($class);
        if (!$reflector->isInstantiable()) {
            throw new ContainerException("[$class] is not instantiable");
        }
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            $instance = new $class();
        } else {
            $parameters = $constructor->getParameters();
            $dependencies = &$this->getDependencies($parameters, $arguments);
            $instance = $reflector->newInstanceArgs($dependencies);
        }
        if (is_object($instance)) {
            $this->instances[$abstract] = $instance;
            if ($class) {
                $this->instances[$class] = $instance;
            }
        }
        if ($this->initiateProperties) {
            call_user_func($this->initiateProperties, $instance);
        }
        return $instance;
    }

    /**
     * Build up a list of dependencies for a given methods parameters
     *
     * @param array $parameters
     * @param array $arguments
     * @return array
     * @throws Exception
     */
    public function &getDependencies(array $parameters, array &$arguments = [])
    {
        $dependencies = [];
        /**
         * @var ReflectionParameter $parameter
         */
        foreach ($parameters as $index => $parameter) {
            $byRef = $parameter->isPassedByReference();
            if (isset($arguments[$parameter->name])) {
                $byRef
                    ? $dependencies[] = &$arguments[$parameter->name]
                    : $dependencies[] = $arguments[$parameter->name];
            } elseif (isset($arguments[$index])) {
                $byRef
                    ? $dependencies[] = &$arguments[$index]
                    : $dependencies[] = $arguments[$index];
            } elseif (is_null($dependency = @$parameter->getClass())) {
                $byRef
                    ? $dependencies[] = &$this->resolvePrimitive($parameter)
                    : $dependencies[] = $this->resolvePrimitive($parameter);
            } elseif ($dependency->name === StaticProperties::class) {
                $byRef
                    ? $dependencies[] = &$this->resolveStaticProperties($parameter)
                    : $dependencies[] = $this->resolveStaticProperties($parameter);
            } else {
                $byRef
                    ? $dependencies[] = &$this->resolve($dependency->name)
                    : $dependencies[] = $this->resolve($dependency->name);
            }
        }

        return $dependencies;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed|null|string
     * @throws Exception
     */
    protected function &resolvePrimitive(ReflectionParameter $parameter)
    {
        if (@$parameter->isArray()) {
            if (($value = $this->config[$parameter->name] ?? false) && is_array($value)) {
                return $value;
            }
        }
        if ($parameter->isDefaultValueAvailable()) {
            $value = $parameter->getDefaultValue();
            return $value;
        }

        $this->unresolvablePrimitive($parameter);
    }

    /**
     * Throw an exception for an unresolvable primitive.
     *
     * @param ReflectionParameter $parameter
     * @return void
     *
     * @throws Exception
     */
    protected function unresolvablePrimitive(ReflectionParameter $parameter)
    {
        $message =
            "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";
        throw new ContainerException($message);
    }

    protected function &resolveStaticProperties(ReflectionParameter $parameter)
    {
        if ($value = $this->config[$parameter->name] ?? false) {
            return $value;
        }
        $class = ucfirst($parameter->name);
        if (class_exists($class) || $class = ClassName::get($class) ?? false) {
            $value = $this->config[$parameter->name] = new StaticProperties($class);
            return $value;
        }
        if ($parameter->isDefaultValueAvailable()) {
            $defaultValue = $parameter->getDefaultValue();
            return $defaultValue;
        }

        $this->unresolvablePrimitive($parameter);
    }

    public function instance($abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
        try {
            if ($class = ClassName::get($abstract)) {
                $this->instances[$class] = $instance;
            }
        } catch (HttpException $e) {
        }
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     */
    public function get($id)
    {
        try {
            if ($instance = $this->instances[$id] ?? $this->instances[ClassName::get($id)] ?? false) {
                return $instance;
            }
        } catch (Throwable $t) {
            throw new ContainerException('Error while retrieving the entry `' . $id . '`');
        }
        throw new NotFound(' No entry was found for `' . $id . '`` identifier');
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        if (isset($this->instances[$id])) {
            return true;
        }
        try {
            $class = ClassName::get($id);
        } catch (Throwable $t) {
            return false;
        }
        return isset($this->instances[$class]);
    }
}
