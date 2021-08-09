<?php
namespace Luracast\Restler\Utils;


use JsonSerializable;
use Luracast\Restler\StaticProperties;

class Convert
{
    /**
     * @var array key value pairs for fixing value types using functions.
     * For example
     *
     *      'id'=>'intval'      will make sure all values of the id properties
     *                          will be converted to integers intval function
     *      'password'=> null   will remove all the password entries
     */
    public static array $fix = array();
    /**
     * @var bool|callable
     */
    public static $stringEncoderFunction = false;
    /**
     * @var bool|callable
     */
    public static $numberEncoderFunction = false;

    /**
     * @var string character that is used to identify sub objects
     *
     * For example
     *
     * when Convert::$separatorChar = '.';
     *
     * array('my.object'=>true) will result in
     *
     * array(
     *    'my'=>array('object'=>true)
     * );
     */
    public static ?string $separatorChar = null;
    /**
     * @var bool set it to true when empty arrays, blank strings, null values
     * to be automatically removed from response
     */
    public static bool $removeEmpty = false;
    /**
     * @var bool set it to true to remove all null values from the result
     */
    public static bool $removeNull = false;
    private \Luracast\Restler\StaticProperties $convert;

    public function __construct(StaticProperties $convert)
    {
        $this->convert = $convert;
    }


    public function toArray($object, bool $forceObjectTypeWhenEmpty = false)
    {
        $nested = false;
        if (is_object($object)) {
            $nested = true;
            if ($object instanceof JsonSerializable) {
                $object = $object->jsonSerialize();
            } elseif (method_exists($object, '__sleep')) {
                $properties = $object->__sleep();
                $array = [];
                foreach ($properties as $key) {
                    $value = $this->toArray(
                        $object->{$key},
                        $forceObjectTypeWhenEmpty
                    );
                    if ($this->convert->stringEncoderFunction && is_string($value)) {
                        $value = $this->convert->stringEncoderFunction($value);
                    } elseif ($this->convert->numberEncoderFunction && is_numeric($value)) {
                        $value = $this->convert->numberEncoderFunction($value);
                    }
                    $array [$key] = $value;
                }
                return $array;
            }
        } elseif (is_array($object)) {
            $nested = true;
        }
        if ($nested) {
            $count = 0;
            $array = [];
            foreach ($object as $key => $value) {
                if (
                    is_string($this->convert->separatorChar) &&
                    false !== strpos($key, $this->convert->separatorChar)
                ) {
                    list($key, $obj) = explode($this->convert->separatorChar, $key, 2);
                    $object[$key][$obj] = $value;
                    $value = $object[$key];
                }
                if ($this->convert->removeEmpty && empty($value) && !is_numeric($value) && !is_bool($value)) {
                    continue;
                } elseif ($this->convert->removeNull && is_null($value)) {
                    continue;
                }
                if (array_key_exists($key, $this->convert->fix)) {
                    if (isset($this->convert->fix[$key])) {
                        $value = call_user_func($this->convert->fix[$key], $value);
                    } else {
                        continue;
                    }
                }
                $value = $this->toArray($value, $forceObjectTypeWhenEmpty);
                if ($this->convert->stringEncoderFunction && is_string($value)) {
                    $value = $this->convert->encoderFunctionName($value);
                } elseif ($this->convert->numberEncoderFunction && is_numeric($value)) {
                    $value = $this->convert->numberEncoderFunction($value);
                }
                $array [$key] = $value;
                $count++;
            }
            return $forceObjectTypeWhenEmpty && $count == 0 ? $object : $array;
        }

        return $object;
    }

    /**
     * @param $value
     * @return float|int
     */
    public static function toNumber($value)
    {
        return ( int )$value == $value ? (int)$value : floatval($value);
    }

    public static function toBool($value): bool
    {
        return is_bool($value) ? $value : $value !== 'false';
    }
}
