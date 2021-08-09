<?php

use Luracast\Restler\Defaults;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

define('BASE', __DIR__ . '/../../..');

require BASE . '/vendor/autoload.php';

Defaults::$cacheDirectory = BASE . '/api/common/store';
Defaults::$implementations[DataProviderInterface::class] = [SerializedFileDataProvider::class];
Defaults::$implementations[HttpClientInterface::class] = [SimpleHttpClient::class];

Routes::mapApiClasses([
    '' => Storage::class
]);

(new Restler())->handle();
