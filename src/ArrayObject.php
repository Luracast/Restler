<?php

namespace Luracast\Restler;

use ArrayObject as Base;
use BadMethodCallException;

/*
 * @method array chunk(int $size, bool $preserve_keys = false) Split an array into chunks
 * @method array column(mixed $column_key, mixed $index_key = null) Return the values from a single
 * column in the input array
 * @method array slice(int $offset, int $length = null, bool $preserve_keys = false) Extract a slice as an array
 *
 * @method array changeKeyCase(int $case = CASE_LOWER) Changes the case of all keys in an array
 * @method array splice(int $offset, int $length = count($input), mixed $replacement = array()) Remove a portion of the arrayObject and replace it with something else
 * @method mixed shift() Shift an element off the beginning of arrayObject
 * @method int unshift(mixed ...$elements) Prepend one or more elements to the beginning of an array
 * @method mixed pop() Pop the element off the end of arrayObject
 *
 */

class ArrayObject extends Base
{
    final public function __construct($input = array())
    {
        parent::__construct($input, self::ARRAY_AS_PROPS);
    }


    public function __call($name, $argv)
    {
        $found = false;
        $modifier = false;
        $func = null;
        switch ($name) {
            //MOD functions
            case 'shift':
            case 'unshift':
            case 'pop':
            case 'splice':
                $found = true;
                $modifier = true;
                break;
            // NO MOD functions
            case 'changeKeyCase':
                $func = 'change_key_case';
            //don't break;
            case 'chunk':
            case 'column':
            case 'slice':
                $found = true;
                break;
        }
        //TODO: add more array methods and document them
        if ($found) {
            if (!$func) {
                $func = "array_$name";
            }
            $copy = $this->getArrayCopy();
            $params = array_merge([&$copy], $argv);
            $result = call_user_func_array($func, $params);
            if (!$modifier) {
                return $result;
            }
            $this->exchangeArray($copy);
            return $result;
        }
        throw new BadMethodCallException(__CLASS__ . '->' . $name);
    }

    public function merge(ArrayObject $arrayObject, bool $overwrite = false)
    {
        foreach ($arrayObject as $key => $val) {
            if ($overwrite || !$this->offsetExists($key)) {
                $this[$key] = $val;
            }
        }
        return $this;
    }

    public static function fromArray(array $input)
    {
        $instance = new static($input);
        foreach ($input as $k => $v) {
            if (is_array($v)) {
                $instance[$k] = static::fromArray($v); //RECURSION
            }
        }
        return $instance;
    }

    public function nested(...$keys)
    {
        if (count($keys) == 1) {
            $keys = explode('.', $keys[0]);
        }
        $from = $this;
        foreach ($keys as $key) {
            if (isset($from[$key])) {
                $from = $from[$key];
                continue;
            }
            return null;
        }
        return $from;
    }

    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}
