<?php


namespace Luracast\Restler\Cache;


use Psr\SimpleCache\CacheInterface;

abstract class Base implements CacheInterface
{
    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        return array_map(
            fn($key) => $this->get($key, $default),
            (array)$keys
        );
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        return array_map(
            fn($key, $value) => $this->set($key, $value, $ttl),
            array_keys((array)$values),
            $values
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        return array_map([$this, 'delete'], (array)$keys);
    }
}
