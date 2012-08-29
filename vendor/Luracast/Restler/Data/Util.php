<?php
namespace Luracast\Restler\Data;

/**
 * Convenience class that converts the given object
 * in to associative array
 *
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class Util
{
    /**
     * @var bool|string|callable
     */
    public static $encoderFunctionName = false;

    /**
     * Convenience function that converts the given object
     * in to associative array
     *
     * @static
     *
     * @param mixed        $object that needs to be converted
     *
     * @return array
     */
    public static function objectToArray($object)
    {
        if ($object instanceof JsonSerializable) {
            $object = $object->jsonSerialize();
        }
        if (is_object($object) && method_exists($object, '__sleep')) {
            $properties = $object->__sleep();
            $array = array();
            foreach ($properties as $key) {
                $value = self::objectToArray($object->{$key});
                if (self::$encoderFunctionName && is_string($value)) {
                    $value = self::$encoderFunctionName ($value);
                }
                $array [$key] = $value;
            }

            return $array;

        }
        if (is_array($object) || is_object($object)) {
            $count = 0;
            $array = array();
            foreach ($object as $key => $value) {
                $value = self::objectToArray($value);
                if (self::$encoderFunctionName && is_string($value)) {
                    $value = self::$encoderFunctionName ($value);
                }
                $array [$key] = $value;
                $count++;
            }
            return $count ? $array : $object;
        }

        return $object;
    }
}
