<?php

use Luracast\Restler\Data\Route;

include __DIR__ . '/../vendor/autoload.php';

$route = Route::fromMethod(new ReflectionMethod(\Say::class, 'hello'));

$map = $route->toGraphQL(function () {
});

print_r($map);
