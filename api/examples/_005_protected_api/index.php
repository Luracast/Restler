<?php


use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

require __DIR__ . '/../../../vendor/autoload.php';

Routes::addAuthenticator(SimpleAuth::class);
Routes::mapApiClasses([
    '' => Simple::class,
    Secured::class
]);

(new Restler())->handle();
