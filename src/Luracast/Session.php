<?php


namespace Luracast\Restler;


use InvalidArgumentException;
use Luracast\Restler\Contracts\SessionInterface;
use SessionHandlerInterface;
use SessionIdInterface;

class Session implements SessionInterface
{
    private array $oldIds = [];
    private int $status = PHP_SESSION_NONE;
    /**
     * @var array
     */
    private $contents = [];
    private array $flash_in = [];
    /** @var array */
    private $flash_out = [];
    private \SessionHandlerInterface $handler;
    private \SessionIdInterface $sessionId;
    private string $id;

    public function __construct(SessionHandlerInterface $handler, SessionIdInterface $sessionId, string $id = '')
    {
        $this->handler = $handler;
        $this->sessionId = $sessionId;
        $this->id = $id;

        if ($this->id !== '') {
            $this->status = PHP_SESSION_ACTIVE;
            $data = unserialize($handler->read($id));
            $this->contents = $data['contents'] ?? [];
            $this->flash_out = $data['flash'] ?? [];
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function get(string $name)
    {
        $key = mb_strtolower($name);
        if (isset($this->contents[$key])) {
            return $this->contents[$key];
        }
        throw new InvalidArgumentException("$name does not exist");
    }

    public function has(string $name): bool
    {
        $key = mb_strtolower($name);
        return isset($this->contents[$key]);
    }

    public function set(string $name, $value): bool
    {
        if ($this->status !== PHP_SESSION_ACTIVE) {
            $this->start();
        }
        $key = mb_strtolower($name);
        $this->contents[$key] = $value;
        return true;
    }

    public function unset(string $name): bool
    {
        $key = mb_strtolower($name);
        if (isset($this->contents[$key])) {
            unset($this->contents[$key]);
            return true;
        }
        return false;
    }

    public function flash(string $name)
    {
        $key = mb_strtolower($name);
        if (isset($this->flash_out[$key])) {
            return $this->flash_out[$key];
        }
        if (isset($this->flash_in[$key])) {
            return $this->flash_in[$key];
        }
        throw new InvalidArgumentException("$name does not exist");
    }

    public function hasFlash(string $name): bool
    {
        $key = mb_strtolower($name);
        return isset($this->flash_in[$key]) || isset($this->flash_out[$key]);
    }

    public function setFlash(string $name, $value): bool
    {
        if ($this->status !== PHP_SESSION_ACTIVE) {
            $this->start();
        }
        $key = mb_strtolower($name);
        $this->flash_in[$key] = $value;
        return true;
    }

    public function unsetFlash(string $name): bool
    {
        $key = mb_strtolower($name);
        if (isset($this->flash_in[$key])) {
            unset($this->flash_in[$key]);
            return true;
        }
        return false;
    }

    public function start(array $options = []): bool
    {
        if ($this->status === PHP_SESSION_ACTIVE) {
            return true;
        }

        $this->status = PHP_SESSION_ACTIVE;

        if ($this->id === '') {
            $this->id = $this->sessionId->create_sid();
            $this->contents = [];
            $this->flash_in = [];
            $this->flash_out = [];
        }
        return true;
    }

    public function regenerateId(): bool
    {
        if ($this->status !== PHP_SESSION_ACTIVE) {
            return false;
        }

        $this->oldIds[] = $this->id;
        $this->id = $this->sessionId->create_sid();

        return true;
    }

    public function commit(): bool
    {
        if ($this->status !== PHP_SESSION_ACTIVE) {
            return false;
        }
        $data = ['contents' => $this->contents, 'flash' => $this->flash_in];
        return $this->handler->write($this->id, serialize($data));
    }

    public function save(): bool
    {
        return $this->commit();
    }

    /**
     * @return int
     *
     * PHP_SESSION_DISABLED if sessions are disabled.
     * PHP_SESSION_NONE if sessions are enabled, but none exists.
     * PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
     */
    public function status(): int
    {
        return $this->status;
    }

    public function destroy(): bool
    {
        if ($this->status === PHP_SESSION_NONE) {
            return true;
        }

        $this->oldIds[] = $this->id;
        $this->handler->destroy($this->id);
        $this->status = PHP_SESSION_NONE;
        $this->id = '';
        $this->contents = [];
        return true;
    }

    public function current()
    {
        return current($this->contents);
    }

    public function next()
    {
        return next($this->contents);
    }

    public function key()
    {
        return key($this->contents);
    }

    public function valid()
    {
        return key($this->contents) !== null;
    }

    public function rewind(): void
    {
        reset($this->contents);
    }
}
