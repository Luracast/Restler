<?php
namespace Luracast\Restler\Data;

/**
 * Convenience class that converts the given object
 * in to associative array
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc3
 */
class Util
{
    /**
     * @var bool|string|callable
     */
    public static $stringEncoderFunction = false;

    /**
     * @var bool|string|callable
     */
    public static $numberEncoderFunction = false;

    /**
     * Convenience function that converts the given object
     * in to associative array
     *
     * @static
     *
     * @param mixed        $object                   that needs to be converted
     *
     * @param bool         $forceObjectTypeWhenEmpty when set to true outputs
     *                                               actual type  (array or
     *                                               object) rather than
     *                                               always an array when the
     *                                               array/object is empty
     *
     * @return array
     */
    public static function objectToArray($object,
                                         $forceObjectTypeWhenEmpty = false)
    {
        if ($object instanceof JsonSerializable) {
            $object = $object->jsonSerialize();
        }
        if (is_object($object) && method_exists($object, '__sleep')) {
            $properties = $object->__sleep();
            $array = array();
            foreach ($properties as $key) {
                $value = self::objectToArray($object->{$key},
                    $forceObjectTypeWhenEmpty);
                if (self::$stringEncoderFunction && is_string($value)) {
                    $value = self::$stringEncoderFunction ($value);
                } elseif (self::$numberEncoderFunction && is_numeric($value)) {
                    $value = self::$numberEncoderFunction ($value);
                }
                $array [$key] = $value;
            }
            return $array;

        }
        if (is_array($object) || is_object($object)) {
            $count = 0;
            $array = array();
            foreach ($object as $key => $value) {
                $value = self::objectToArray($value, $forceObjectTypeWhenEmpty);
                if (self::$stringEncoderFunction && is_string($value)) {
                    $value = self::$encoderFunctionName ($value);
                } elseif (self::$numberEncoderFunction && is_numeric($value)) {
                    $value = self::$numberEncoderFunction ($value);
                }
                $array [$key] = $value;
                $count++;
            }
            return $forceObjectTypeWhenEmpty && $count == 0 ? $object : $array;
        }

        return $object;
    }
}

