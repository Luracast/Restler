<?php
use Luracast\Restler\iAuthenticate;

class SimpleAuth implements iAuthenticate
{
    const KEY = 'rEsTlEr2';

    function __isAllowed()
    {
        return isset($_GET['key']) && $_GET['key'] == SimpleAuth::KEY ? TRUE : FALSE;
    }

    function key()
    {
        return SimpleAuth::KEY;
    }
}