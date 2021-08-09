<?php

namespace Luracast\Restler\Utils;

use Exception;
use Luracast\Restler\Contracts\ValidationInterface;
use Luracast\Restler\Contracts\ValueObjectInterface;
use Luracast\Restler\Data\Param;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Exceptions\Invalid;
use Psr\Http\Message\UploadedFileInterface;

class Validator implements ValidationInterface
{
    public static array $preFilters = [
        //'*'             => 'some_global_filter', //applied to all parameters
        'string' => 'trim', //apply filter function by type (string)
        //'string'       => 'strip_tags',
        //'string'       => 'htmlspecialchars',
        //'int'          => 'abs',
        //'float'        => 'abs',
        //'CustomClass'  => 'MyFilterClass::custom',
        //                  please note that you wont get an instance
        //                  of CustomClass. you will get an array instead
    ];
    public static bool $holdException = false;
    public static array $exceptions = [];

    /**
     * @throws HttpException
     */
    public static function validate($input, ?Param $param)
    {
        if ('mixed' == $param->type) {
            return $input;
        }
        if ($param->multiple) {
            $error = $param->message ?? "Invalid value specified for $param->name";
            $func = function ($input, $index) use ($param) {
                $clone = clone $param;
                $clone->multiple = false;
                $clone->name = $param->name . '[' . $index . ']';
                return static::validate($input, $clone);
            };
            if (!is_array($input)) {
                if ($param->fix) {
                    $input = [$input];
                } else {
                    $error .= ". Expecting items of type `$param->type`";
                    throw new HttpException(400, $error);
                }
            }
            $r = count($input);
            if ($param->minCount && $r < $param->minCount) {
                $item = $param->minCount > 1 ? 'items' : 'item';
                $error .= ". Minimum $param->minCount $item required.";
                throw new HttpException(400, $error);
            }
            if ($param->maxCount && $r > $param->maxCount) {
                if ($param->fix) {
                    $input = array_slice($input, 0, $param->maxCount);
                } else {
                    $item = $param->maxCount > 1 ? 'items' : 'item';
                    $error .= ". Maximum $param->maxCount $item allowed.";
                    throw new HttpException(400, $error);
                }
            }
            return array_map($func, $input, array_keys($input));
        }
        $name = "`$param->name`";
        $format = '';
        if (
            isset(static::$preFilters['*']) &&
            is_scalar($input) &&
            is_callable($func = static::$preFilters['*'])
        ) {
            $input = $func($input);
        }
        if (
            isset(static::$preFilters[$param->type]) &&
            (is_scalar($input) || !empty($param->properties)) &&
            is_callable($func = static::$preFilters[$param->type])
        ) {
            $input = $func($input);
        }
        try {
            if (is_null($input)) {
                if ($param->required) {
                    throw new HttpException(
                        400,
                        "$name is required."
                    );
                }
                return null;
            }
            $error = $param->message ?? "Invalid value specified for $name";

            //if a validation method is specified
            if (!empty($param->method)) {
                $method = $param->method;
                $param->method = '';
                $r = self::validate($input, $param);
                return $param->apiClassInstance->{$method} ($r);
            }

            // when type is an array check if it passes for any type
            if (is_array($param->type)) {
                //trace("types are ".print_r($info->type, true));
                $types = $param->type;
                foreach ($types as $type) {
                    $param->type = $type;
                    try {
                        $r = self::validate($input, $param);
                        if ($r !== false) {
                            return $r;
                        }
                    } catch (HttpException $e) {
                        // just continue
                    }
                }
                throw new HttpException(400, $error);
            }

            //patterns are supported only for non numeric types
            if (isset ($param->pattern)
                && $param->type != 'int'
                && $param->type != 'float'
                && $param->type != 'number'
            ) {
                if (!$param->multiple && !preg_match($param->pattern, $input)) {
                    throw new HttpException(400, $error);
                }
                if ($param->multiple && !empty($input)) {
                    foreach ($input as $value) {
                        if (!preg_match($param->pattern, $value)) {
                            throw new HttpException(400, $error);
                        }
                    }
                }
            }

            if (isset ($param->choice)) {
                if (!$param->required && empty($input)) {
                    //since its optional, and empty let it pass.
                    $input = null;
                } elseif (is_array($input)) {
                    foreach ($input as $i) {
                        if (!in_array($i, $param->choice)) {
                            $error .= ". Expected one of (" . implode(',', $param->choice) . ").";
                            throw new HttpException(400, $error);
                        }
                    }
                } elseif (!in_array($input, $param->choice)) {
                    $error .= ". Expected one of (" . implode(',', $param->choice) . ").";
                    throw new HttpException(400, $error);
                }
            }

            if ('string' == $param->type && method_exists(
                    $class = static::class,
                    $param->format
                ) && $param->format != 'validate') {
                if (!$param->required && empty($input)) {
                    //optional parameter with a empty value assume null
                    return null;
                }
                try {
                    return call_user_func("$class::$param->format", $input, $param);
                } catch (Invalid $e) {
                    throw new HttpException(400, $error . '. ' . $e->getMessage());
                }
            }

            switch ($param->type) {
                case UploadedFileInterface::class:
                    if ($input instanceof UploadedFileInterface) {
                        return $input;
                    }
                case 'int' :
                case 'float' :
                case 'number' :
                    if (!is_numeric($input)) {
                        $error .= '. Expecting '
                            . ($param->type == 'int' ? 'integer' : 'numeric')
                            . ' value';
                        break;
                    }
                    if ($param->type == 'int' && (int)$input != $input) {
                        if ($param->fix) {
                            $r = (int)$input;
                        } else {
                            $error .= '. Expecting integer value';
                            break;
                        }
                    } else {
                        $r = Type::numericValue($input);
                    }
                    if (isset ($param->min) && $r < $param->min) {
                        if ($param->fix) {
                            $r = $param->min;
                        } else {
                            $error .= ". Minimum required value is $param->min.";
                            break;
                        }
                    }
                    if (isset ($param->max) && $r > $param->max) {
                        if ($param->fix) {
                            $r = $param->max;
                        } else {
                            $error .= ". Maximum allowed value is $param->max.";
                            break;
                        }
                    }
                    return $r;

                case 'string' :
                    if (is_bool($input)) {
                        $input = $input ? 'true' : 'false';
                    }
                    if (!is_string($input)) {
                        $error .= '. Expecting alpha numeric value';
                        break;
                    }
                    if ($param->required && $input === '') {
                        $error = "$name is required.";
                        break;
                    }
                    $r = strlen($input);
                    if (isset ($param->min) && $r < $param->min) {
                        if ($param->fix) {
                            $input = str_pad($input, $param->min, $input);
                        } else {
                            $char = $param->min > 1 ? 'characters' : 'character';
                            $error .= ". Minimum $param->min $char required.";
                            break;
                        }
                    }
                    if (isset ($param->max) && $r > $param->max) {
                        if ($param->fix) {
                            $input = substr($input, 0, $param->max);
                        } else {
                            $char = $param->max > 1 ? 'characters' : 'character';
                            $error .= ". Maximum $param->max $char allowed.";
                            break;
                        }
                    }
                    return $input;

                case 'bool':
                case 'boolean':
                    if (is_bool($input)) {
                        return $input;
                    }
                    if (is_numeric($input)) {
                        if ($input == 1) {
                            return true;
                        }
                        if ($input == 0) {
                            return false;
                        }
                    } elseif (is_string($input)) {
                        switch (strtolower($input)) {
                            case 'true':
                                return true;
                            case 'false':
                                return false;
                        }
                    }
                    if ($param->fix) {
                        return $input ? true : false;
                    }
                    $error .= '. Expecting boolean value';
                    break;
                case 'array':
                    if ($param->fix && is_string($input)) {
                        $input = explode(CommentParser::$arrayDelimiter, $input);
                    }
                    if (is_array($input)) {
                        $format = $param->format ?? '';
                        if ($param->fix) {
                            if ($format == 'indexed') {
                                $input = Param::filterArray($input, Param::KEEP_NUMERIC);
                            } elseif ($format == 'associative') {
                                $input = Param::filterArray($input, Param::KEEP_NON_NUMERIC);
                            }
                        } elseif (
                            $format == 'indexed' &&
                            array_values($input) != $input
                        ) {
                            $error .= '. Expecting a list of items but an item is given';
                            break;
                        } elseif (
                            $format == 'associative' &&
                            array_values($input) == $input &&
                            count($input)
                        ) {
                            $error .= '. Expecting an item but a list is given';
                            break;
                        }
                        $r = count($input);
                        if (isset ($param->min) && $r < $param->min) {
                            $item = $param->min > 1 ? 'items' : 'item';
                            $error .= ". Minimum $param->min $item required.";
                            break;
                        }
                        if (isset ($param->max) && $r > $param->max) {
                            if ($param->fix) {
                                $input = array_slice($input, 0, $param->max);
                            } else {
                                $item = $param->max > 1 ? 'items' : 'item';
                                $error .= ". Maximum $param->max $item allowed.";
                                break;
                            }
                        }
                        return $input;
                    } elseif ($format) {
                        $error .= ". Expecting items of type `$format`";
                        break;
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
                    if (class_exists($param->type)) {
                        $input = $param->filterArray($input, Param::KEEP_NON_NUMERIC);
                        if (Type::implements($param->type, ValueObjectInterface::class)) {
                            return call_user_func(
                                "{$param->type}::__set_state",
                                $input
                            );
                        }
                        $class = $param->type;
                        $instance = new $class();
                        if (is_array($param->properties)) {
                            if (
                                empty($input) ||
                                !is_array($input) ||
                                $input === array_values($input)
                            ) {
                                $error .= ". Expecting an item of type `$param->type`";
                                break;
                            }
                            foreach ($param->properties as $key => $child) {
                                $child = clone $child;
                                $child->name = "{$param->name}[$key]";
                                if (array_key_exists($key, $input) || $child->required) {
                                    $instance->{$key} = static::validate(($input[$key] ?? null), $child);
                                }
                            }
                        }
                        return $instance;
                    }
            }
            throw new HttpException(400, $error);
        } catch (Exception $e) {
            self::$exceptions[$param->name] = $e;
            if (self::$holdException) {
                return null;
            }
            throw $e;
        }
    }

    /**
     * Validate alphabetic characters.
     *
     * Check that given value contains only alphabetic characters.
     *
     * @param            $input
     * @param Param|null $param
     *
     * @return string
     *
     * @throws Invalid
     */
    public static function alpha($input, ?Param $param = null): string
    {
        if (ctype_alpha($input)) {
            return $input;
        }
        if ($param && $param->fix) {
            //remove non alpha characters
            return preg_replace("/[^a-z]/i", "", $input);
        }
        throw new Invalid('Expecting only alphabetic characters.');
    }

    /**
     * Validate numeric characters.
     *
     * Check that given value contains only digits.
     *
     * @param            $input
     * @param Param|null $param
     *
     * @return string
     *
     * @throws Invalid
     */
    public static function numeric($input, ?Param $param = null): string
    {
        if (ctype_digit($input)) {
            return $input;
        }
        if ($param && $param->fix) {
            //remove non numeric characters
            return preg_replace("/[^0-9]/i", "", $input);
        }
        throw new Invalid('Expecting only numeric characters.');
    }

    /**
     * Validate UUID strings.
     *
     * Check that given value contains only alpha numeric characters and the length is 36 chars.
     *
     * @param            $input
     * @param Param|null $param
     *
     * @return string
     *
     * @throws Invalid
     */
    public static function uuid($input, ?Param $param = null): string
    {
        if (is_string($input) && preg_match(
                '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i',
                $input
            )) {
            return strtolower($input);
        }
        throw new Invalid('Expecting a Universally Unique IDentifier (UUID) string.');
    }

    /**
     * Validate alpha numeric characters.
     *
     * Check that given value contains only alpha numeric characters.
     *
     * @param            $input
     * @param Param|null $param
     *
     * @return string
     *
     * @throws Invalid
     */
    public static function alphanumeric($input, ?Param $param = null): string
    {
        if (ctype_alnum($input)) {
            return $input;
        }
        if ($param && $param->fix) {
            //remove non alpha numeric and space characters
            return preg_replace("/[^a-z0-9 ]/i", "", $input);
        }
        throw new Invalid('Expecting only alpha numeric characters.');
    }

    /**
     * Validate printable characters.
     *
     * Check that given value contains only printable characters.
     *
     * @param            $input
     * @param Param|null $param
     *
     * @return string
     *
     * @throws Invalid
     */
    public static function printable($input, ?Param $param = null): string
    {
        if (ctype_print($input)) {
            return $input;
        }
        if ($param && $param->fix) {
            //remove non printable characters
            return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $input);
        }
        throw new Invalid('Expecting only printable characters.');
    }

    /**
     * Validate hexadecimal digits.
     *
     * Check that given value contains only hexadecimal digits.
     *
     * @param            $input
     * @param Param|null $param
     *
     * @return string
     *
     * @throws Invalid
     */
    public static function hex($input, ?Param $param = null): string
    {
        if (ctype_xdigit($input)) {
            return $input;
        }
        throw new Invalid('Expecting only hexadecimal digits.');
    }

    /**
     * Color specified as hexadecimals
     *
     * Check that given value contains only color.
     *
     * @param            $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function color($input, ?Param $param = null): string
    {
        if (preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $input)) {
            return $input;
        }
        throw new Invalid('Expecting color as hexadecimal digits.');
    }

    /**
     * Validate Telephone number
     *
     * Check if the given value is numeric with or without a `+` prefix
     *
     * @param            $input
     * @param Param|null $param
     *
     * @return string
     *
     * @throws Invalid
     */
    public static function tel($input, ?Param $param = null)
    {
        if (is_numeric($input) && '-' != substr($input, 0, 1)) {
            return $input;
        }
        throw new Invalid(
            'Expecting phone number, a numeric value ' .
            'with optional `+` prefix'
        );
    }

    /**
     * Validate Email
     *
     * Check if the given string is a valid email
     *
     * @param mixed $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function email($input, ?Param $param = null): string
    {
        $r = filter_var($input, FILTER_VALIDATE_EMAIL);
        if ($r) {
            return $r;
        } elseif ($param && $param->fix) {
            $r = filter_var($input, FILTER_SANITIZE_EMAIL);
            return static::email($r);
        }
        throw new Invalid('Expecting email in `name@example.com` format');
    }

    /**
     * Validate IP Address
     *
     * Check if the given string is a valid ip address
     *
     * @param mixed $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function ip($input, ?Param $param = null): string
    {
        $r = filter_var($input, FILTER_VALIDATE_IP);
        if ($r) {
            return $r;
        }

        throw new Invalid('Expecting IP address in IPV6 or IPV4 format');
    }

    /**
     * Validate Url
     *
     * Check if the given string is a valid url
     *
     * @param mixed $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function url($input, ?Param $param = null): string
    {
        $r = filter_var($input, FILTER_VALIDATE_URL);
        if ($r) {
            return $r;
        } elseif ($param && $param->fix) {
            $r = filter_var($input, FILTER_SANITIZE_URL);
            return static::url($r);
        }
        throw new Invalid('Expecting url in `http://example.com` format');
    }

    /**
     * MySQL Date
     *
     * Check if the given string is a valid date in YYYY-MM-DD format
     *
     * @param mixed $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function date($input, ?Param $param = null): string
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
     * @param mixed $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function datetime($input, ?Param $param = null): string
    {
        if (
            preg_match(
                '/^(?P<year>19\d\d|20\d\d)\-(?P<month>0[1-9]|1[0-2])\-' .
                '(?P<day>0\d|[1-2]\d|3[0-1]) (?P<h>0\d|1\d|2[0-3]' .
                ')\:(?P<i>[0-5][0-9])\:(?P<s>[0-5][0-9])$/',
                $input,
                $date
            )
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
     * @param mixed $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function time24($input, ?Param $param = null): string
    {
        return static::time($input, $param);
    }

    /**
     * Time
     *
     * Check if the given string is a valid time in HH:MM:SS format
     *
     * @param mixed $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function time($input, ?Param $param = null): string
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
     * @param mixed $input
     * @param Param|null $param
     *
     * @return string
     * @throws Invalid
     */
    public static function time12($input, ?Param $param = null): string
    {
        if (preg_match(
            '/^([1-9]|1[0-2]|0[1-9]){1}(:[0-5][0-9])?\s?([aApP][mM]{1})?$/',
            $input
        )
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
     * @param mixed $input
     * @param Param|null $param
     *
     * @return int
     * @throws Invalid
     */
    public static function timestamp($input, ?Param $param = null): int
    {
        if ((string)(int)$input == $input
            && ($input <= PHP_INT_MAX)
            && ($input >= ~PHP_INT_MAX)
        ) {
            return (int)$input;
        }
        throw new Invalid('Expecting unix timestamp, such as ' . time());
    }
}
