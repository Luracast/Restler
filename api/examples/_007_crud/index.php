<?php


use Luracast\Restler\Defaults;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

define('BASE', __DIR__ . '/../../..');
include BASE . "/vendor/autoload.php";

Defaults::$cacheDirectory = BASE . '/api/common/store';
Defaults::$implementations[DataProviderInterface::class] = [SerializedFileDataProvider::class];

Routes::mapApiClasses([
    Authors::class
]);

(new Restler())->handle();
