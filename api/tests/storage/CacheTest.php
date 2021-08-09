<?php


use Psr\SimpleCache\CacheInterface;

class CacheTest
{
    const KEY = 'cache';
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function get()
    {
        return ['exists' => $this->cache->has(self::KEY), 'value' => $this->cache->get(self::KEY, null)];
    }

    public function post(string $value): bool
    {
        return $this->cache->set(self::KEY, $value);
    }
}
