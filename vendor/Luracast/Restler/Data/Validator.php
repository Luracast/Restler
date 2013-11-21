<?php
namespace Luracast\Restler\Data;

use Luracast\Restler\RestException;
use Luracast\Restler\Util;

/**
 * Default Validator class used by Restler. It can be replaced by any
 * iValidate implementing class by setting Defaults::$validatorClass
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
class Validator implements iValidate
{
    /**
     * Validate Email
     *
     * Check if the given string is a valid email
     *
     * @param String         $input
     * @param ValidationInfo $info
     *
     * @return string
     * @throws Invalid
     */
    public static function email($input, ValidationInfo $info = null)
    {
        $r = filter_var($input, FILTER_VALIDATE_EMAIL);
        if ($r) {
            return $r;
        }
        throw new Invalid('Expecting email in `name@example.com` format');
    }

    /**
     * MySQL Date
     *
     * Check if the given string is a valid date in YYYY-MM-DD format
     *
     * @param String         $input
     * @param ValidationInfo $info
     *
     * @return string
     * @throws Invalid
     */
    public static function date($input, ValidationInfo $info = null)
    {
        if (
            preg_match(
                '#^(?P<year>\d{2}|\d{4})-(?P<month>\d{1,2})-(?P<day>\d{1,2})$#',
                $input,
                $date
            )
            && checkdate($date['month'], $date['day'], $date['year'])
        ) {
            return $input;
        }
        throw new Invalid(
            'Expecting date in `YYYY-MM-DD` format, such as `'
            . date("Y-m-d") . '`'
        );
    }

    /**
     * MySQL DateTime
     *
     * Check if the given string is a valid date and time in YYY-MM-DD HH:MM:SS format
     *
     * @param String         $input
     * @param ValidationInfo $info
     *
     * @return string
     * @throws Invalid
     */
    public static function datetime($input, ValidationInfo $info = null)
    {
        if (
            preg_match('/^(?P<year>19\d\d|20\d\d)\-(?P<month>0[1-9]|1[0-2])\-' .
                '(?P<day>0\d|[1-2]\d|3[0-1]) (?P<h>0\d|1\d|2[0-3]' .
                ')\:(?P<i>[0-5][0-9])\:(?P<s>[0-5][0-9])$/',
                $input, $date)
            && checkdate($date['month'], $date['day'], $date['year'])
        ) {
            return $input;
        }
        throw new Invalid(
            'Expecting date and time in `YYYY-MM-DD HH:MM:SS` format, such as `'
            . date("Y-m-d H:i:s") . '`'
        );
    }

    /**
     * Alias for Time
     *
     * Check if the given string is a valid time in HH:MM:SS format
     *
     * @param String         $input
     * @param ValidationInfo $info
     *
     * @return string
     * @throws Invalid
     */
    public static function time24($input, ValidationInfo $info = null)
    {
        return static::time($input, $info);
    }

    /**
     * Time
     *
     * Check if the given string is a valid time in HH:MM:SS format
     *
     * @param String         $input
     * @param ValidationInfo $info
     *
     * @return string
     * @throws Invalid
     */
    public static function time($input, ValidationInfo $info = null)
    {
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $input)) {
            return $input;
        }
        throw new Invalid(
            'Expecting time in `HH:MM:SS` format, such as `'
            . date("H:i:s") . '`'
        );
    }

    /**
     * Time in 12 hour format
     *
     * Check if the given string is a valid time 12 hour format
     *
     * @param String         $input
     * @param ValidationInfo $info
     *
     * @return string
     * @throws Invalid
     */
    public static function time12($input, ValidationInfo $info = null)
    {
        if (preg_match(
            '/^([1-9]|1[0-2]|0[1-9]){1}(:[0-5][0-9])?\s?([aApP][mM]{1})?$/',
            $input)
        ) {
            return $input;
        }
        throw new Invalid(
            'Expecting time in 12 hour format, such as `08:00AM` and `10:05:11`'
        );
    }

    /**
     * Unix Timestamp
     *
     * Check if the given value is a valid timestamp
     *
     * @param String         $input
     * @param ValidationInfo $info
     *
     * @return int
     * @throws Invalid
     */
    public static function timestamp($input, ValidationInfo $info = null)
    {
        if ((string)(int)$input == $input
            && ($input <= PHP_INT_MAX)
            && ($input >= ~PHP_INT_MAX)
        ) {
            return (int)$input;
        }
        throw new Invalid('Expecting unix timestamp, such as ' . time());
    }

    /**
     * Validate the given input
     *
     * Validates the input and attempts to fix it when fix is requested
     *
     * @param mixed          $input
     * @param ValidationInfo $info
     * @param null           $full
     *
     * @return array|bool|float|int|mixed|null|number|string
     * @throws \Luracast\Restler\RestException
     */
    public static function validate($input, ValidationInfo $info, $full = null)
    {
        if (is_null($input)) {
            if ($info->required) {
                throw new RestException (400,
                    "`$info->name` is required but missing.");
            }
            return null;
        }
        $error = isset ($info->rules ['message'])
            ? $info->rules ['message']
            : "invalid value specified for `$info->name`";

        //if a validation method is specified
        if (!empty($info->method)) {
            $method = $info->method;
            $info->method = '';
            $r = self::validate($input, $info);
            return $info->apiClassInstance->{$method} ($r);
        }

        // when type is an array check if it passes for any type
        if (is_array($info->type)) {
            //trace("types are ".print_r($info->type, true));
            $types = $info->type;
            foreach ($types as $type) {
                $info->type = $type;
                try {
                    $r = self::validate($input, $info);
                    if ($r !== false) {
                        return $r;
                    }
                } catch (RestException $e) {
                    // just continue
                }
            }
            throw new RestException (400, $error);
        }

        //patterns are supported only for non numeric types
        if (isset ($info->pattern)
            && $info->type != 'int'
            && $info->type != 'float'
            && $info->type != 'number'
        ) {
            if (!preg_match($info->pattern, $input)) {
                throw new RestException (400, $error);
            }
        }

        if (isset ($info->choice)) {
            if (!in_array($input, $info->choice)) {
                throw new RestException (400, $error);
            }
        }

        if (method_exists(__CLASS__, $info->type) && $info->type != 'validate') {
            try {
                return call_user_func(__CLASS__ . '::' . $info->type, $input, $info);
            } catch (Invalid $e) {
                throw new RestException(400, $error . '. ' . $e->getMessage());
            }
        }

        switch ($info->type) {
            case 'int' :
            case 'float' :
            case 'number' :
                if (!is_numeric($input)) {
                    $error .= '. Expecting '
                        . ($info->type == 'int' ? 'integer' : 'numeric')
                        . ' value';
                    break;
                }
                if ($info->type == 'int' && (int)$input != $input) {
                    if ($info->fix) {
                        $r = (int)$input;
                    } else {
                        $error .= '. Expecting integer value';
                        break;
                    }
                } else {
                    $r = $info->numericValue($input);
                }
                if (isset ($info->min) && $r < $info->min) {
                    if ($info->fix) {
                        $r = $info->min;
                    } else {
                        $error .= '. Given value is too low';
                        break;
                    }
                }
                if (isset ($info->max) && $r > $info->max) {
                    if ($info->fix) {
                        $r = $info->max;
                    } else {
                        $error .= '. Given value is too high';
                        break;
                    }
                }
                return $r;

            case 'string' :
                $r = strlen($input);
                if (isset ($info->min) && $r < $info->min) {
                    if ($info->fix) {
                        $input = str_pad($input, $info->min, $input);
                    } else {
                        $error .= '. Given string is too short';
                        break;
                    }
                }
                if (isset ($info->max) && $r > $info->max) {
                    if ($info->fix) {
                        $input = substr($input, 0, $info->max);
                    } else {
                        $error .= '. Given string is too long';
                        break;
                    }
                }
                return $input;

            case 'bool':
            case 'boolean':
                if ($input == 'true') return true;
                if (is_numeric($input)) return $input > 0;
                return false;

            case 'array':
                if (is_array($input)) {
                    $contentType =
                        Util::nestedValue($info, 'contentType') ? : null;
                    if ($info->fix) {
                        if ($contentType == 'indexed') {
                            $input = $info->filterArray($input, true);
                        } elseif ($contentType == 'associative') {
                            $input = $info->filterArray($input, true);
                        }
                    } elseif (
                        $contentType == 'indexed' &&
                        array_values($input) != $input
                    ) {
                        $error .= '. Expecting an array but an object is given';
                        break;
                    } elseif (
                        $contentType == 'associative' &&
                        array_values($input) == $input &&
                        count($input)
                    ) {
                        $error .= '. Expecting an object but an array is given';
                        break;
                    }
                    $r = count($input);
                    if (isset ($info->min) && $r < $info->min) {
                        $error .= '. Given array is too small';
                        break;
                    }
                    if (isset ($info->max) && $r > $info->max) {
                        if ($info->fix) {
                            $input = array_slice($input, 0, $info->max);
                        } else {
                            $error .= '. Given array is too big';
                            break;
                        }
                    }
                    if (
                        isset($contentType) &&
                        $contentType != 'associative' &&
                        $contentType != 'indexed'
                    ) {
                        $name = $info->name;
                        $info->type = $contentType;
                        unset($info->contentType);
                        foreach ($input as $key => $chinput) {
                            $info->name = "{$name}[$key]";
                            $input[$key] = static::validate($chinput, $info);
                        }
                    }
                    return $input;
                } elseif (isset($contentType)) {
                    $error .= ". Expecting an array with contents of type `$contentType`";
                    break;
                } elseif ($info->fix && is_string($input)) {
                    return array($input);
                }
                break;
            case 'mixed':
            case 'unknown_type':
            case 'unknown':
            case null: //treat as unknown
                return $input;
            default :
                if (!is_array($input)) {
                    break;
                }
                //do type conversion
                if (class_exists($info->type)) {
                    $input = $info->filterArray($input, false);
                    $implements = class_implements($info->type);
                    if (
                        is_array($implements) &&
                        in_array('Luracast\\Restler\\Data\\iValueObject', $implements)
                    ) {
                        return call_user_func(
                            "{$info->type}::__set_state", $input
                        );
                    }
                    $class = $info->type;
                    $instance = new $class();
                    if (is_array($info->children)) {
                        if (
                            empty($input) ||
                            !is_array($input) ||
                            $input === array_values($input)
                        ) {
                            $error .= ". Expecting an object of type `$info->type`";
                            break;
                        }
                        foreach ($info->children as $key => $value) {
                            $instance->{$key} = static::validate(
                                Util::nestedValue($input, $key),
                                new ValidationInfo($value)
                            );
                        }
                    }
                    return $instance;
                }
        }
        throw new RestException (400, $error);
    }
}

