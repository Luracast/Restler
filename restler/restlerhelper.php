<?php
/**
 * Conveniance class that converts the given object
 * in to associative array
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class RestlerHelper {

    /**
     * Conveniance function that converts the given object
     * in to associative array
     *
     * @param object $object
     *            that needs to be converted
     */
    public static function objectToArray($object, $encoderFunctionName = false)
    {
        if($object instanceof JsonSerializable){
            $object = $object->jsonSerialize();
        }
        if (is_array ( $object ) || is_object ( $object )) {
            $array = array ();
            foreach ($object as $key => $value) {
                $value = self::objectToArray ( $value, $encoderFunctionName );
                if ($encoderFunctionName && is_string ( $value )) {
                    $value = $$encoderFunctionName ( $value );
                }
                $array [$key] = $value;
            }
            return $array;
        }
        return $object;
    }
}