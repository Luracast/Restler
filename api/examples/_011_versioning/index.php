<?php


use Luracast\Restler\Defaults;
use Luracast\Restler\OpenApi3\Explorer;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use v1\BodyMassIndex;

define('BASE', __DIR__ . '/../../..');
include BASE . "/vendor/autoload.php";

Defaults::$useUrlBasedVersioning = true;

Routes::setApiVersion(2);
Routes::mapApiClasses([
    'bmi' => BodyMassIndex::class,
    Explorer::class
]);

(new Restler())->handle();
