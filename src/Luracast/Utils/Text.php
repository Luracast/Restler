<?php
namespace Luracast\Restler\Utils;

/**
 * Convenience class for String manipulation
 */
class Text
{
    /**
     * Given haystack contains the needle or not?
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $caseSensitive
     *
     * @return bool
     */
    public static function contains(string $haystack, string $needle, bool $caseSensitive = true): bool
    {
        if (empty($needle)) {
            return true;
        }
        return $caseSensitive
            ? strpos($haystack, $needle) !== false
            : stripos($haystack, $needle) !== false;
    }

    /**
     * Given haystack begins with the needle or not?
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function beginsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Given haystack ends with the needle or not?
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }


    /**
     * Convert camelCased or underscored string in to a title
     *
     * @param string $name
     *
     * @return string
     */
    public static function title(string $name): string
    {
        return
            ucwords(
                preg_replace(
                    array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/', '/([_-])/', '/[^a-zA-Z0-9\s]|\s\s+/'),
                    array(' $0', ' $0', ' ', ' '),
                    $name
                )
            );
    }

    /**
     * Convert given string to be used as a slug or css class
     *
     * @param string $name
     * @param $replacement
     * @return string
     */
    public static function slug(string $name, string $replacement = '-'): string
    {
        return preg_replace('/[^a-zA-Z]+/', $replacement, strtolower(strip_tags($name)));
    }

    public static function removeCommon(string $fromPath, string $usingPath, string $separator = '/'): string
    {
        if (empty($fromPath)) {
            return '';
        }
        if (empty($usingPath)) {
            return $fromPath;
        }
        $fromPath = explode($separator, $fromPath);
        $usingPath = explode($separator, $usingPath);
        while (count($usingPath)) {
            if (count($fromPath) && $fromPath[0] == $usingPath[0]) {
                array_shift($fromPath);
            } else {
                break;
            }
            array_shift($usingPath);
        }
        return implode($separator, $fromPath);
    }

    public static function common(string $fromPath, string $usingPath, string $separator = '/'): string
    {
        if (empty($fromPath)) {
            return '';
        }
        if (empty($usingPath)) {
            return $fromPath;
        }
        $fromPath = explode($separator, $fromPath);
        $usingPath = explode($separator, $usingPath);
        foreach ($fromPath as $i => $value) {
            if ($usingPath[$i] !== $value) {
                $fromPath = array_slice($fromPath, 0, $i);
                break;
            }
        }
        return implode($separator, $fromPath);
    }
}
