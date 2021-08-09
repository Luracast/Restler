<?php


class Header
{
    /**
     * @param string $api_key {@from header}
     * @return string
     */
    function get(string $api_key): string
    {
        return $api_key;
    }

    /**
     * @param string $api_key {@from header}
     * @return string
     */
    function post(string $api_key): string
    {
        return $api_key;
    }
}
