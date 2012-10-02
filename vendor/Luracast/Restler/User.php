<?php
namespace Luracast\Restler;
/**
 * Information gathered about the api user is kept here using static methods
 * and properties for other classes to make use of them.
 * Typically Authentication classes populate them
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc3
 */
class User
{
    private static $initialized = false;
    public static $id = null;
    public static $ip;
    public static $browser = '';
    public static $platform = '';

    public static function init()
    {
        static::$initialized = true;
        static::$ip = static::getIpAddress();
    }

    public static function getUniqueId($includePlatform = false)
    {
        if (!static::$initialized) static::init();
        return static::$id ? : base64_encode('ip:' . ($includePlatform
            ? static::$ip . '-' . static::$platform
            : static::$ip
        ));
    }

    public static function getIpAddress($ignoreProxies = false)
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR',
                     'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
                     'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
                     'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4
                        | FILTER_FLAG_NO_PRIV_RANGE
                        | FILTER_FLAG_NO_RES_RANGE) !== false
                    ) {
                        return $ip;
                    }
                }
            }
        }
    }
}
