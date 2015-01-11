<?php

/**
 * Class Validation provides api to check validation
 */
class Validation
{

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