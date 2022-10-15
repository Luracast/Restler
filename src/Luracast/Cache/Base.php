<?php


namespace Luracast\Restler\Cache;


use DateInterval;
use DateTime;
use Psr\SimpleCache\CacheInterface;

abstract class Base implements CacheInterface
{
    protected function timestamp(null|int|\DateInterval $ttl = null)
    {
        if (is_null($ttl)) {
            return false;
        }
        $now = (new DateTime('now'));
        if ($ttl instanceof DateInterval) {
            return $now->add($ttl)->getTimestamp();
        } elseif (is_int($ttl)) {
            return $now->getTimestamp() + $ttl;
        }
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null): iterable
    {
        return array_map(
            fn($key) => $this->get($key, $default),
            (array)$keys
        );
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(iterable $values, $ttl = null): bool
    {
        $result = array_map(
            fn($key, $value) => $this->set($key, $value, $ttl),
            array_keys((array)$values),
            $values
        );
        return count(array_filter($result)) > 0;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $result = array_map([$this, 'delete'], (array)$keys);
        return count(array_filter($result)) > 0;
    }
}
