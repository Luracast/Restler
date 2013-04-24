<?php
//set_include_path(get_include_path() . PATH_SEPARATOR . getcwd());
$loader = require_once 'autoload.php';
$loader->setUseIncludePath(true);
/*
require_once 'Luracast/Restler/AutoLoader.php';
use Luracast\Restler\AutoLoader;

return call_user_func(function ()
{
    $loader = AutoLoader::instance();
    spl_autoload_register($loader);
    return $loader;
});
*/

