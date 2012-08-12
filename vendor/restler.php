<?php
require_once 'Luracast/Restler/RestlerAutoLoader.php';
use Luracast\Restler\RestlerAutoLoader;

return call_user_func(function () {
    $loader = RestlerAutoLoader::instance();
    spl_autoload_register ( $loader );
    return $loader;
});
