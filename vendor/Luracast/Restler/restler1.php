<?php
/**
 * Restler 1 compatibility mode enabler
 */
use Luracast\Restler\Defaults;

//changes in iAuthenticate
Defaults::$authenticationMethod = 'isAuthenticated';
eval('
interface iAuthenticate{
    public function isAuthenticated();
}
');

//changes in routing
Defaults::$autoRoutingEnabled = false;
Defaults::$autoValidationEnabled = false;