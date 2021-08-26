<?php

namespace Luracast\Restler\Contracts;

/**
 * Interface to identify the user
 *
 * When the user is known we will be able to monitor, rate limit and do more
 */
interface UserIdentificationInterface
{
    /**
     * A way to uniquely identify the current api consumer
     *
     * When his user id is known it should be used otherwise ip address
     * can be used
     *
     * @param bool $includePlatform Should we consider user alone or should
     *                              consider the application/platform/device
     *                              as well for generating unique id
     *
     * @return string
     */
    public function getUniqueIdentifier(bool $includePlatform = false): string;

    /**
     * User identity to be used for caching purpose
     *
     * When the dynamic cache service places an object in the cache, it needs to
     * label it with a unique identifying string known as a cache ID. This
     * method gives that identifier
     *
     * @return string
     */
    public function getCacheIdentifier(): string;

    /**
     * Authentication classes should call this method
     *
     * @param string $id user id as identified by the authentication classes
     *
     * @return void
     */
    public function setUniqueIdentifier(string $id): void;

    /**
     * User identity for caching purpose
     *
     * In a role based access control system this will be based on role
     *
     * @param string $id
     *
     * @return void
     */
    public function setCacheIdentifier(string $id): void;

    public function getPlatform(): ?string;

    public function getBrowser(): ?string;

    public function getIpAddress(bool $ignoreProxies = false): string;
}
