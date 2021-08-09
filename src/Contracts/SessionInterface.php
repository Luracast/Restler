<?php


namespace Luracast\Restler\Contracts;

use Iterator;
use SessionHandlerInterface;
use SessionIdInterface;

interface SessionInterface extends Iterator
{
    public function __construct(SessionHandlerInterface $handler, SessionIdInterface $sessionId, string $id = '');

    public function get(string $name);

    public function has(string $name): bool;

    public function set(string $name, $value): bool;

    public function unset(string $name): bool;

    public function flash(string $name);

    public function hasFlash(string $name): bool;

    public function setFlash(string $name, $value): bool;

    public function unsetFlash(string $name): bool;

    public function getId(): string;

    public function start(array $options = []): bool;

    public function regenerateId(): bool;

    public function commit(): bool;

    public function save(): bool;

    /**
     * @return int
     *
     * PHP_SESSION_DISABLED if sessions are disabled.
     * PHP_SESSION_NONE if sessions are enabled, but none exists.
     * PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
     */
    public function status(): int;

    public function destroy(): bool;
}
