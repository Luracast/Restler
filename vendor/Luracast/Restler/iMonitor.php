<?php
/**
 * Interface for creating monitoring classes.
 *
 * Monitoring can be used for user tracking, throttling,
 * access restriction etc.,
 *
 * @category   Framework
 * @package    restler
 * @subpackage auth
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
interface iMonitor
{
    /**
     * Access verification method.
     *
     * API access will be denied when this method returns false
     *
     * @abstract
     * @return boolean true when api access is allowed false otherwise
     */
    public function isAllowed();
}
