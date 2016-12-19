<?php
use \Luracast\Restler\iAuthenticate;
use \Luracast\Restler\Resources;
use \Luracast\Restler\Defaults;

class AccessControl implements iAuthenticate
{
    public static $requires = 'user';
    public static $role = 'user';

    public function __isAllowed()
    {
        //hardcoded api_key=>role for brevity
        $roles = array('12345' => 'user', '67890' => 'admin');
        $userClass = Defaults::$userIdentifierClass;

        if (isset($_GET['api_key'])) {
            if (!array_key_exists($_GET['api_key'], $roles)) {
                $userClass::setCacheIdentifier($_GET['api_key']);
                return false;
            }
        } else {
            return false;
        }
        static::$role = $roles[$_GET['api_key']];
        $userClass::setCacheIdentifier(static::$role);
        Defaults::$accessControlFunction = 'AccessControl::verifyAccess';
        return static::$requires == static::$role || static::$role == 'admin';
    }

    public function __getWWWAuthenticateString()
    {
        return 'Query name="api_key"';
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
        return $requires
            ? static::$role == 'admin' || static::$role == $requires
            : true;
    }
}
