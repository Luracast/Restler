<?php
use \Luracast\Restler\iAuthenticate;
use \Luracast\Restler\Resources;

class AccessControl implements iAuthenticate
{
    public static $requires = 'user';
    public static $role = 'user';

    public function __isAllowed()
    {
        //hardcoded api_key=>role for brevity
        $roles = array('12345' => 'user', '67890' => 'admin');

        if (!isset($_GET['api_key'])
            || !array_key_exists($_GET['api_key'], $roles)
        ) {
            return false;
        }
        static::$role = $roles[$_GET['api_key']];
        Resources::$accessControlFunction = 'AccessControl::verifyAccess';
        return static::$requires == static::$role || static::$role == 'admin';
    }

    /**
     * @access private
     */
    public static function verifyAccess(array $m)
    {
        $requires =
            isset($m['class']['AccessControl']['properties']['requires'])
                ? $m['class']['AccessControl']['properties']['requires']
                : false;
        //print_r($m);
        //echo static::$role.' '.$requires.' : ';
        return $requires
            ? static::$role == 'admin' || static::$role == $requires
            : true;
    }
}
