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

    public static function validate($input, ValidationInfo $info, $full=null)
    {
        if (is_null($input) && !$info->children) {
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

        switch ($info->type) {
            case 'email' :
                $r = filter_var($input, FILTER_VALIDATE_EMAIL);
                if ($r) {
                    return $r;
                }
                $error .= '. Expecting email in `name@example.com` format';
                break;
            case 'date' :
                if (
                    preg_match('#^(?P<year>\d{2}|\d{4})([- /.])(?P<month>\d{1,2})\2(?P<day>\d{1,2})$#', $input, $date)
                    && checkdate($date['month'], $date['day'], $date['year'])
                ) {
                    return $input;
                }
                $error .= '. Expecting date in `YYYY-MM-DD` format, such as `'
                    . date("Y-m-d") . '`';
                break;
            case 'datetime' :
                if (
                    preg_match('/^(?<year>19\d\d|20\d\d)\-(?<month>0[1-9]|1[0-2])\-' .
                        '(?<day>0\d|[1-2]\d|3[0-1]) (?<h>0\d|1\d|2[0-3]' .
                        ')\:(?<i>[0-5][0-9])\:(?<s>[0-5][0-9])$/',
                        $input, $date) && checkdate($date['month'], $date['day'], $date['year'])
                )
                    return $input;
                $error .= '. Expecting date and time in `YYYY-MM-DD HH:MM:SS` format, such as `'
                    . date("Y-m-d H:i:s") . '`';
                break;
            case 'timestamp' :
                if (
                    (string)(int)$input === $input &&
                    ($input <= PHP_INT_MAX) &&
                    ($input >= ~PHP_INT_MAX)
                ) {
                    return (int)$input;
                }
                $error .= '. Expecting unix timestamp, such as ' . time();
                break;
            case 'int' :
            case 'float' :
            case 'number' :
                if (!is_numeric($input)) {
                    $error .= '. Expecting numeric value';
                    break;
                }
                $r = $info->numericValue($input);
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
                return $info->type == 'int'
                    ? (int)$r
                    : ($info->type == 'float' ? floatval($r) : $r);

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
                if(is_null($input) && $info->contentType) {
                    $data = Util::$restler->getRequestData();
                    if(isset($data[0])){
                        $input = $data;
                    }
                }
                if (is_array($input)) {
                    if ($info->contentType) {
                        $name = $info->name;
                        $info->type = $info->contentType;
                        unset($info->contentType);
                        foreach ($input as $key => $chinput) {
                            if(is_string($key)){
                                unset($input[$key]);
                                continue;
                            }
                            $info->name = "{$name}[$key]";
                            $input[$key] = static::validate($chinput, $info);
                        }
                    }
                    return $input;
                } elseif ($info->contentType) {
                    $error .= ". Expecting an array with contents of type `$info->contentType`";
                    break;
                }
                return array($input);
                break;
            case 'mixed':
            case 'unknown_type':
            case 'unknown':
            case null: //treat as unknown
                return $input;
            default :
                if (!is_array($input) && !$info->children) {
                    break;
                }
                //do type conversion
                if (class_exists($info->type)) {
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
                    $instance =  new $class();
                    if (is_array($info->children)) {
                        if (is_null($input)) {
                            $input = Util::$restler->getRequestData();
                        } elseif (
                            !is_array($input) ||
                            empty($input) ||
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

