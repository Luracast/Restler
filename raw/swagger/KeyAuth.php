<?php

use Luracast\Restler\iAuthenticate;
/**
 * Describe the purpose of this class/interface/trait
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0
 */
class KeyAuth implements iAuthenticate
{

    /**
     * Auth function that is called when a protected method is requested
     *
     * @return boolean true when authenticated, false otherwise
     */
    public function __isAllowed()
    {
        return isset($_GET['api_key']) && $_GET['api_key']=='r3rocks';
    }
}

