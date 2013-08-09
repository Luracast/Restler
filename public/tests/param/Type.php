<?php
class Type
{
    /**
     * @param string $email      {@from body} {@type email}
     */
    function postEmail($email)
    {
        return $email;
    }

    /**
     * @param string $date       {@from body} {@type date}
     */
    function postDate($date)
    {
        return $date;
    }

    /**
     * @param string $datetime   {@from body} {@type datetime}
     */
    function postDatetime($datetime)
    {
        return $datetime;
    }

    /**
     * @param string $timestamp  {@from body} {@type timestamp}
     */
    function postTimestamp($timestamp)
    {
        return $timestamp;
    }

    /**
     * @param array $integers   {@type int}
     */
    function postIntegers(array $integers)
    {
        return $integers;
    }

    /**
     * @param array $numbers    {@type float}
     */
    function postNumbers(array $numbers)
    {

    }
}