<?php
class AccessControl implements iAuthenticate
{

    public $requiredRole = 'admin';

    function __isAuthenticated()
    {

        //hardcoded password=>role for brevity
        $roles = array('anyone' => 'user', 'secretPassword' => 'admin');

        if (!isset($_GET['password'])
            || !array_key_exists($_GET['password'], $roles))
            return FALSE;
        switch ($roles[$_GET['password']]) {
            case 'admin':
                return TRUE;
            case 'user':
                if ($this->requiredRole == 'user') return TRUE;
        }
        throw new RestException(401, 'Insufficient Access Rights');
    }

}