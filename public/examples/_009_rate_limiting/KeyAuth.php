<?php
use Luracast\Restler\iAuthenticate;

class KeyAuth implements iAuthenticate
{
    public function __isAllowed()
    {
        return isset($_GET['api_key']) && $_GET['api_key'] == 'r3rocks';
    }
}
