<?php


use Luracast\Restler\OpenApi3\Explorer;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

define('BASE', __DIR__ . '/../../..');
include BASE . "/vendor/autoload.php";

Routes::addAuthenticator(AccessControl::class);

Routes::mapApiClasses([
    '' => Access::class,
    Explorer::class
]);

(new Restler())->handle();
