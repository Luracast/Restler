<?php
use Luracast\Restler\iAuthenticate;

class KeyAuth implements iAuthenticate
{
    public function isAllowed()
    {
        return isset($_GET['api_key']) && $_GET['api_key'] == 'r3rocks';
    }

    public function getWWWAuthenticateString()
    {
        return 'Query name="api_key"';
    }
}
