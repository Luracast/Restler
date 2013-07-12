<?php
namespace Luracast\Restler;

/**
 * Interface to identify the user
 *
 * When the user is known we will be able to monitor, rate limit and do more
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
interface iUser
{
    /**
     * A way to uniquely identify the current api consumer
     *
     * When his user id is known it should be used otherwise ip address
     * can be used
     *
     * @param bool $includePlatform Should we consider user alone or should
     *                              consider the application/platform/device
     *                              as well for generating unique id
     *
     * @return string
     */
    public static function getUniqueId($includePlatform = false);

    /**
     * Authentication classes should call this method
     *
     * @param string $id user id as identified by the authentication classes
     *
     * @return void
     */
    public static function setUserId($id);
}