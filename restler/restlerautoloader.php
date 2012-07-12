<?php
/**
 * Class that holds autoloader static functions for all Restler needs
 * @category   Framework
 * @package    restler
 * @subpackage helper
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 */
class RestlerAutoLoader {

    /**
     * Helper function to spl_autoload_register all needed methods
     */
    public static function register()
    {
        spl_autoload_register ( array (
                'RestlerAutoLoader',
                'formats' 
        ) );
    }

    /**
     * A static function for loading format classes
     *
     * @param String $className
     *            class name of a class that implements iFormat
     */
    public static function formats($className)
    {
        $className = strtolower ( $className );
        $file = RESTLER_PATH . "/$className/$className.php";
        if (file_exists ( $file )) {
            require_once ($file);
        } else {
            $file = RESTLER_PATH . "/$className.php";
            if (file_exists ( $file )) {
                require_once ($file);
            } else {
                if (file_exists ( "$className.php" )) {
                    require_once ("$className.php");
                }
            }
        }
    }
}