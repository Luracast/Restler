<?php

use Luracast\Restler\Contracts\ContainerInterface;
use Luracast\Restler\Contracts\UserIdentificationInterface;
use Luracast\Restler\Core;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Exceptions\Redirect;
use Luracast\Restler\Utils\ClassName;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

if (!function_exists('instance')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $make
     * @param array $parameters
     * @return mixed|ContainerInterface
     * @throws HttpException
     */
    function instance(string $make = null, array $parameters = [])
    {
        /** @var ContainerInterface $container */
        static $container = null;
        if (is_null($make)) {
            return $container;
        }
        if (ContainerInterface::class === $make) {
            $class = ClassName::get(ContainerInterface::class);
            return new $class(...$parameters);
        }
        if (!$container) {
            return null;
        }
        return $container->make($make, $parameters);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the application.
     *
     * @param string $path
     * @return string
     * @throws HttpException
     */
    function base_path(string $path = ''): string
    {
        /** @var UriInterface $url */
        $url = instance(Core::class)->baseUrl;
        return (string)($path ? $url->withPath($path) : $url);
    }
}

if (!function_exists('nested')) {
    /**
     * Get the value deeply nested inside an array / object
     *
     * Using isset() to test the presence of nested value can give a false positive
     *
     * This method serves that need
     *
     * When the deeply nested property is found its value is returned, otherwise
     * null is returned.
     * @param array|object $from array to extract the value from
     * @param string|array $key ... pass more to go deeply inside the array
     *                              alternatively you can pass a single array
     * @return mixed|null
     */
    function nested($from, $key/**, $key2 ... $key`n` */)
    {
        if (is_array($key)) {
            $keys = $key;
        } else {
            $keys = func_get_args();
            array_shift($keys);
        }
        foreach ($keys as $key) {
            if (is_array($from) && isset($from[$key])) {
                $from = $from[$key];
                continue;
            } elseif (is_object($from) && isset($from->{$key})) {
                $from = $from->{$key};
                continue;
            }
            return null;
        }
        return $from;
    }
}

if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param array|string|null $key
     * @param mixed $default
     * @return ServerRequestInterface|array|mixed
     * @throws HttpException
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return instance(ServerRequestInterface::class);
        }
        /** @var Core $core */
        if (!$core = instance(Core::class)) {
            return $default;
        }
        $data = $core->getRequestData() ?? [];
        if (is_array($key)) {
            $values = [];
            foreach ($key as $k) {
                $values[$k] = nested($data, explode('.', $k));
            }
            return $values ?? $default;
        }
        return nested($data, explode('.', $key)) ?? $default;
    }
}

if (!function_exists('user')) {
    /**
     * @throws HttpException
     */
    function user(): ?UserIdentificationInterface
    {
        return instance(UserIdentificationInterface::class);
    }
}

if (!function_exists('redirect')) {
    /**
     * Throws redirect exception
     *
     * @param string|UriInterface $to
     * @param int $status
     * @param array $headers
     * @throws Redirect
     */
    function redirect($to = '/', int $status = 302, array $headers = [])
    {
        throw new Redirect((string)$to, $status, $headers);
    }
}
