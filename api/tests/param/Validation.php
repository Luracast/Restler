<?php

/**
 * Class Validation provides api to check validation
 */
class Validation
{
    /**
     * Make sure string is converted properly to bool
     * @param bool $value {@from query}
     * @return bool
     */
    function getBoolean($value)
    {
        return $value;
    }

    /**
     * Make sure string is converted properly to bool
     * @param bool $value {@from query}{@fix true}
     * @return bool
     */
    function getBoolfix($value)
    {
        return $value;
    }

    /**
     * Validate with regex
     *
     * @param string $password  {@pattern /^(?:[0-9]+[a-z]|[a-z]+[0-9])[a-z0-9]*$/i}
     *                          {@message Strong password with at least
     *                          one alpha and one numeric character is required}
     *
     * @return string
     */
    function postPattern($password)
    {
        return $password;
    }

} 