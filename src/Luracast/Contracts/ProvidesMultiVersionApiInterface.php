<?php

namespace Luracast\Restler\Contracts;

/**
 * Lets Restler know that the implementing api
 * provides mani versions from the single class
 *
 * @package Luracast\Restler\Contracts
 */
interface ProvidesMultiVersionApiInterface
{
    /**
     * Maximum api version supported by the api class
     * @return int
     */
    public static function getMaximumSupportedVersion(): int;
}
