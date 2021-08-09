<?php


use Luracast\Restler\Filters\RateLimiter;
use ratelimited\Authors;
use Luracast\Restler\Defaults;
use Luracast\Restler\OpenApi3\Explorer;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

define('BASE', __DIR__ . '/../../..');
include BASE . "/vendor/autoload.php";

Defaults::$cacheDirectory = BASE . '/api/common/store';
Defaults::$implementations[DataProviderInterface::class] = [SerializedFileDataProvider::class];

RateLimiter::setLimit('hour', 10);
Routes::setFilters(RateLimiter::class);
Routes::addAuthenticator(KeyAuth::class);
Routes::mapApiClasses([
    Authors::class,
    Explorer::class
]);

(new Restler())->handle();
