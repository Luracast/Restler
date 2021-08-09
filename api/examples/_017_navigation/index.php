<?php


use Luracast\Restler\Defaults;
use Luracast\Restler\MediaTypes\Html;
use Luracast\Restler\Middleware\SessionMiddleware;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

define('BASE', __DIR__ . '/../../..');
include BASE . "/vendor/autoload.php";
Defaults::$cacheDirectory = BASE . '/api/common/store';
Html::$template = 'php'; //'handlebar'; //'twig'; //'blade';
Restler::$middleware[] = new SessionMiddleware();
Routes::setResponseMediaTypes(
    Html::class
);
Routes::mapApiClasses([
    '' => Website::class,
    Products::class
]);

(new Restler())->handle();
