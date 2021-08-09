<?php


use Luracast\Restler\MediaTypes\Json;
use Luracast\Restler\MediaTypes\Xml;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

require __DIR__ . '/../../../vendor/autoload.php';

Routes::setOverridingResponseMediaTypes(Json::class, Xml::class);

Routes::mapApiClasses([
    BMI::class
]);

(new Restler())->handle();
