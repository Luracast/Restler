<?php
use Luracast\Restler\Defaults;
use Luracast\Restler\Format\HtmlFormat;

if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    //if composer auto loader is found use it
    $loader = require __DIR__ . '/vendor/autoload.php';
    $loader->setUseIncludePath(true);
    class_alias('Luracast\\Restler\\Restler', 'Restler');
} else {
    //otherwise use the restler auto loader
    require_once __DIR__.'/src/Luracast/Restler/AutoLoader.php';
    return call_user_func(function () {
        $loader = Luracast\Restler\AutoLoader::instance();
        spl_autoload_register($loader);
        return $loader;
    });
}
Defaults::$cacheDirectory = __DIR__ . '/cache';
HtmlFormat::$viewPath = __DIR__ . '/views';
