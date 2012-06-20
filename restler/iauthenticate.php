<?php
/**
 * Interface for creating authentication classes
 * @category   Framework
 * @package    restler
 * @subpackage auth
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
interface iAuthenticate {

	/**
	 * Auth function that is called when a protected method is requested
	 * 
	 * @return boolean TRUE or FALSE
	 */
	public function __isAuthenticated();
}