<?php


namespace Luracast\Restler\Cache;


class InMemory extends Base
{
    private array $store = [];

    public function get(string $key, mixed $default = null): mixed
    {
        $stored = $this->store[$key] ?? false;
        if (!$stored) return $default;
        [$value, $expires] = $stored;
        if (false === $expires) return $default;
        if (time() <= $expires) {
            $this->delete($key);
            return $default;
        }
        return $value;
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $timestamp = $this->timestamp($ttl);
        $this->store[$key] = [$value, $timestamp];
        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->store[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->store = [];
        return true;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->store);
    }
}
