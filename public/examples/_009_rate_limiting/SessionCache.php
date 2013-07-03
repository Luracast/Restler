<?php

use \Luracast\Restler\iCache;

class SessionCache implements iCache
{
    function __construct()
    {
        @session_start();
    }

    public function set($name, $data)
    {
        $_SESSION[$name] = $data;
    }

    public function get($name, $ignoreErrors = false)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    public function clear($name, $ignoreErrors = false)
    {
        unset($_SESSION[$name]);
    }

    public function isCached($name)
    {
        return isset($_SESSION[$name]);
    }
}
