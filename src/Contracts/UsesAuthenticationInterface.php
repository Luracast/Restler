<?php

namespace Luracast\Restler\Contracts;

interface UsesAuthenticationInterface
{
    public function _setAuthenticationStatus(bool $isAuthenticated = false, bool $isAuthFinished = false): void;
}
