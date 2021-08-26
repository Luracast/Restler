<?php


namespace Luracast\Restler\Cache;


class InMemory extends Base
{
    private array $store = [];

    public function get($key, $default = null)
    {
        return $this->store[$key] ?? $default;
    }

    public function set($key, $value, $ttl = null): void
    {
        $this->store[$key] = $value;
    }

    public function delete($key): void
    {
        unset($this->store[$key]);
    }

    public function clear(): void
    {
        $this->store = [];
    }

    public function has($key)
    {
        return array_key_exists($key, $this->store);
    }
}
