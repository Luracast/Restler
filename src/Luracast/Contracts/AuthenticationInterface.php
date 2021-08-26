<?php

namespace Luracast\Restler\Contracts;

/**
 * Interface for creating authentication classes
 *
 * @package Luracast\Restler\Contracts
 */
interface AuthenticationInterface extends FilterInterface
{
    /**
     * @return string string to be used with WWW-Authenticate header
     * @example Basic
     * @example Digest
     * @example OAuth
     */
    public static function getWWWAuthenticateString(): string;
}
