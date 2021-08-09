<?php

declare(strict_types=1);


use Luracast\Restler\OpenApi3\Security\AuthorizationCode as AuthorizationCodeFlow;
use Luracast\Restler\OpenApi3\Security\OAuth2;

include __DIR__ . "/../vendor/autoload.php";

$value = new OAuth2(
    new AuthorizationCodeFlow(
        'examples/_015_oauth2_server/authorize',
        'examples/_015_oauth2_server/grant',
        'examples/_015_oauth2_server/grant',
        []
    )
);

var_dump($value->toArray());
