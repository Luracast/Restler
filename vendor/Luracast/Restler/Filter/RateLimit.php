<?php
namespace Luracast\Restler\Filter;

use Luracast\Restler\iFilter;
use Luracast\Restler\iUseAuthentication;
use Luracast\Restler\iUser;
use Luracast\Restler\User;
use Luracast\Restler\RestException;

/**
 * Describe the purpose of this class/interface/trait
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
class RateLimit implements iFilter, iUseAuthentication
{
    /**
     * @var \Luracast\Restler\Restler;
     */
    public $restler;
    /**
     * @var int
     */
    public static $usagePerUnit = 1200;
    /**
     * @var int
     */
    public static $authenticatedUsagePerUnit = 5000;
    /**
     * @var string
     */
    public static $unit = 'hour';
    /**
     * @var string group the current api belongs to
     */
    public static $group = 'common';
    /**
     * @var string name of the class that implements iUser interface
     */
    public static $userClass = 'Luracast\\Restler\\User';

    protected static $units = array(
        'second' => 1,
        'minute' => 60,
        'hour' => 3600, // 60*60 seconds
        'day' => 86400, // 60*60*24 seconds
        'week' => 604800, // 60*60*24*7 seconds
        'month' => 2592000, // 60*60*24*30 seconds
    );


    /**
     * @param string $unit
     * @param int    $usagePerUnit
     * @param int    $authenticatedUsagePerUnit
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public static function setLimit(
        $unit, $usagePerUnit, $authenticatedUsagePerUnit = null
    )
    {
        static::$unit = $unit;
        static::$usagePerUnit = $usagePerUnit;
        static::$authenticatedUsagePerUnit =
            $authenticatedUsagePerUnit ? : $usagePerUnit;
    }

    public function __isAllowed()
    {
        if (static::$authenticatedUsagePerUnit
            == static::$usagePerUnit
        ) return $this->check();
        return null;
    }

    public function __setAuthenticationStatus($isAuthenticated = false)
    {
        header('X-Auth-Status: ' . ($isAuthenticated ? 'true' : 'false'));
        $this->check($isAuthenticated);
    }

    private static function validate($unit)
    {
        if (!isset(static::$units[$unit]))
            throw new \InvalidArgumentException(
                'Rate Limit time unit should be '
                . implode('|', array_keys(static::$units)) . '.'
            );
    }

    private function check($isAuthenticated = false)
    {
        static::validate(static::$unit);
        $timeUnit = static::$units[static::$unit];
        $maxPerUnit = $isAuthenticated
            ? static::$authenticatedUsagePerUnit
            : static::$usagePerUnit;
        $user = static::$userClass;
        if(!is_subclass_of($user, 'Luracast\\Restler\\iUser')){
            throw new \UnexpectedValueException('`Ratelimit::$userClass` must implement iUser interface');
        }
        $id = "RateLimit_" . $maxPerUnit . '_per_' . static::$unit
            . '_for_' . static::$group
            . '_' . $user::getUniqueId();
        $lastRequest = $this->restler->cache->get($id, true)
            ? : array('time' => 0, 'used' => 0);
        $time = $lastRequest['time'];
        $diff = time() - $time; # in seconds
        $used = $lastRequest['used'];

        header("X-RateLimit-Limit: $maxPerUnit per ".static::$unit);
        if ($diff >= $timeUnit) {
            $used = 1;
            $time = time();
        } elseif ($used >= $maxPerUnit) {
            header("X-RateLimit-Remaining: 0");
            $wait = $timeUnit - $diff;
            sleep(1);
            throw new RestException(429,
                'Rate limit of ' . $maxPerUnit . ' request' .
                ($maxPerUnit > 1 ? 's' : '') . ' per '
                . static::$unit . ' exceeded. Please wait for '
                . static::duration($wait) . '.'
            );
        } else {
            $used++;
        }
        $remainingPerUnit = $maxPerUnit - $used;
        header("X-RateLimit-Remaining: $remainingPerUnit");
        $this->restler->cache->set($id,
            array('time' => $time, 'used' => $used));
        return true;
    }

    private function duration($secs)
    {
        $units = array(
            'week' => (int)($secs / 86400 / 7),
            'day' => $secs / 86400 % 7,
            'hour' => $secs / 3600 % 24,
            'minute' => $secs / 60 % 60,
            'second' => $secs % 60);

        $ret = array();

        //$unit = 'days';
        foreach ($units as $k => $v) {
            if ($v > 0) {
                $ret[] = $v > 1 ? "$v {$k}s" : "$v $k";
                //$unit = $k;
            }
        }
        $i = count($ret) - 1;
        if ($i) {
            $ret[$i] = 'and ' . $ret[$i];
        }
        return implode(' ', $ret); //." $unit.";
    }
}