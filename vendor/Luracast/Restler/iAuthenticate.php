<?php
namespace Luracast\Restler;

/**
 * Interface for creating authentication classes
 * @category   Framework
 * @package    Restler
 * @subpackage auth
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc1
 */
interface iAuthenticate extends iFilter
{
    /**
     * Auth function that is called when a protected method is requested
     *
     * @return boolean true when authenticated, false otherwise
     */
    public function __isAllowed();
}

