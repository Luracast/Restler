<?php

namespace Luracast\Restler\Contracts;


use Luracast\Restler\OpenApi3\Security\Scheme;

interface ExplorableAuthenticationInterface extends AuthenticationInterface
{
    public static function scheme(): Scheme;
}