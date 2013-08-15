<?php
if (is_readable(__DIR__.'/autoload.php')) {
    //if composer autoloader is found use it
    $loader = require_once 'autoload.php';
    $loader->setUseIncludePath(true);
    class_alias('Luracast\\Restler\\Restler', 'Restler');
} else {
    //otherwise use the restler autoloader
    require_once 'Luracast/Restler/AutoLoader.php';
    return call_user_func(function () {
        $loader = Luracast\Restler\AutoLoader::instance();
        spl_autoload_register($loader);
        return $loader;
    });
}

