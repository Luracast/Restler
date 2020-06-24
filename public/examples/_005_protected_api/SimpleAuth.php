<?php
use Luracast\Restler\iAuthenticate;

class SimpleAuth implements iAuthenticate
{
    const KEY = 'rEsTlEr3';

    function __isAllowed()
    {
        return isset($_GET['key']) && $_GET['key'] == SimpleAuth::KEY;
    }

    public function __getWWWAuthenticateString()
    {
        return 'Query name="key"';
    }

    function key()
    {
        return SimpleAuth::KEY;
    }
}
