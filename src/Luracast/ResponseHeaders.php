<?php


namespace Luracast\Restler;

use ArrayAccess;

class ResponseHeaders implements ArrayAccess
{
    private array $container = [];
    private ?int $status = null;
    private int $start = 0;
    private int $end = 0;
    private int $length = 0;

    public function setStatus($code): void
    {
        $this->status = $code;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setRange(int $start, int $end, ?int $total = null): void
    {
        $this->start = $start;
        $this->end = $end;
        if (!$total) {
            $total = $this->getContentLength() ?? $end;
        }
        $this->setContentLength($end - $start);
        $this->container['Content-Range'] = "bytes $start-$end/$total";
    }

    public function getRange(): array
    {
        return [$this->start, $this->end, $this->length];
    }

    public function getContentLength(): int
    {
        return $this->length ?? $this->container['Content-Length'] ?? 0;
    }

    public function setContentLength(int $length): void
    {
        $this->container['Content-Length'] = $this->length = $length;
    }

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

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }
}
