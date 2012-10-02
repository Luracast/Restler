<?php
namespace Luracast\Restler\Filter;

use Luracast\Restler\iFilter;
use Luracast\Restler\iUseAuthentication;
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
 * @version    3.0.0sp1
 */
class RateLimit implements iFilter, iUseAuthentication
{
    /**
     * @var \Luracast\Restler\Restler;
     */
    public $restler;
    public static $unauthenticatedUsagePerHour = 1200;
    public static $authenticatedUsagePerHour = 5000;

    public function __isAllowed()
    {
        if (static::$authenticatedUsagePerHour
            == static::$unauthenticatedUsagePerHour
        ) return $this->check();
        return null;
    }

    public function __setAuthenticationStatus($isAuthenticated = false)
    {
        header('X-Auth-Status: '.($isAuthenticated ? 'true' : 'false'));
        $this->check($isAuthenticated);
    }

    private function check($isAuthenticated = false)
    {
        $timeUnit = 3600; // hours
        $maxPerUnit = $isAuthenticated
            ? static::$authenticatedUsagePerHour
            : static::$unauthenticatedUsagePerHour;
        $id = User::getUniqueId();
        $lastRequest = $this->restler->cache->get($id, true)
            ? : array('time' => 0, 'fill' => 0);
        $timeDifference = time() - $lastRequest['time']; # in seconds
        $fill = 0;
        if (!is_null($maxPerUnit)) {
            $fill = max($lastRequest['fill'] - $timeDifference, 0);
            $fill += $timeUnit / $maxPerUnit;
        }
        header("X-RateLimit-Limit: $maxPerUnit");
        if ($fill > $timeUnit) {
            header("X-RateLimit-Remaining: 0");
            $wait = ceil($fill - $timeUnit);
            sleep(1);
            throw new RestException(429,
                'Rate limit of ' . $maxPerUnit . ' requests per hour exceeded.'
                    . ' Please wait for ' . gmdate("i:s", $wait) . ' minutes.'
            );
        }
        $remainingPerUnit = max(floor(($timeUnit - $fill)
            * $maxPerUnit / $timeUnit), 0);
        header("X-RateLimit-Remaining: $remainingPerUnit");
        $this->restler->cache->set($id,
            array('time' => time(), 'fill' => $fill));
        return true;
    }
}
