<?php


use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

require __DIR__ . '/../../../vendor/autoload.php';

Routes::mapApiClasses([
    Currency::class
]);

(new Restler())->handle();
