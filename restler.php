<?php

use Luracast\Restler\Defaults;

if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    //if composer auto loader is found use it
    $loader = require_once __DIR__ . '/vendor/autoload.php';
    $loader->setUseIncludePath(true);
    class_alias('Luracast\\Restler\\Restler', 'Restler');
} else {
    //otherwise use the restler auto loader
    require_once __DIR__ . '/src/AutoLoader.php';
    return call_user_func(function () {
        $loader = Luracast\Restler\AutoLoader::instance();
        spl_autoload_register($loader);
        return $loader;
    });
}
Defaults::$cacheDirectory = __DIR__ . '/cache';
