<?php
/**
 * Class MinMax provides an api to check min and max
 * attributes for array, int, and string
 */
class MinMaxFix
{

    /**
     * Restrict the number of items in an array
     *
     * @param array $array {@min 2}{@max 5}{@fix true}
     *
     * @return array
     */
    function postArray(array $array)
    {
        return $array;
    }

    /**
     * Restrict the value
     *
     * @param int $int {@min 2}{@max 5}{@fix true}{@from path}
     *
     * @return int
     */
    function getInt($int)
    {
        return $int;
    }

    /**
     * Restrict the length of the string
     *
     * @param string $string {@min 2}{@max 5}{@fix true}{@from body}
     *
     * @return string
     */
    function postString($string)
    {
        return $string;
    }
}