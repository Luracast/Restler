<?php
require_once '../../../vendor/restler.php';

$r = new Restler();
$r->cacheDir = dirname($_SERVER['SCRIPT_FILENAME'].DIRECTORY_SEPARATOR.'cache');
HumanReadableCache::$cacheDir = dirname($_SERVER['SCRIPT_FILENAME']).DIRECTORY_SEPARATOR.'cache';
$r->setCompatibilityMode(2);
$r->addAPIClass('Resources');

$r->addAPIClass('Simple','');
$r->addAuthenticationClass('AccessControl');
//$r->setSupportedFormats('DebugFormat');
$r->handle();

class UserDetails{
    static $name='defaultUser';
    static $role='user';
}
//with in the Access Control method
UserDetails::$name='Arul';
UserDetails::$role='admin';
/*

    private static $objstore = array();

    public static function AddObject($name,$object) {
         if(self::__CheckObject($name)==false) {
              self::$objstore[$name] = $object;
         } else {
               // Trow some error //
         }
    }

    public static function GetObject($name) {
        if(self::__CheckObject($name)) {
              return(self::$objstore[$name]);
        } else {
            // Trow some error //
        }
    }

    public static function DeleteObject($name) {
        unset(self::$objstore[$name]);
        return(true);
    }

    private static function __CheckObject($name) {
        return(isset(self::$objstore[$name]));
    }
    */
