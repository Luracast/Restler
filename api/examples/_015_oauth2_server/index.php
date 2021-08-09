<?php


use Auth\Server;
use Luracast\Restler\Defaults;
use Luracast\Restler\MediaTypes\Html;
use Luracast\Restler\MediaTypes\Json;
use Luracast\Restler\MediaTypes\Upload;
use Luracast\Restler\Middleware\SessionMiddleware;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

define('BASE', __DIR__ . '/../../..');
include BASE . "/vendor/autoload.php";

Defaults::$cacheDirectory = BASE . '/api/common/store';
Html::$template = 'blade'; //'handlebar'; //'twig'; //'php';
Restler::$middleware[] = new SessionMiddleware();
Routes::setOverridingRequestMediaTypes(Json::class, Upload::class);
Routes::setOverridingResponseMediaTypes(Json::class, Html::class);
Routes::addAuthenticator(Server::class);
Routes::mapApiClasses([
    '' => Server::class
]);

(new Restler())->handle();
