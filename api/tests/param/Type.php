<?php

class Type
{
    /**
     * @param int $selected {@select one,two,three}{@choice 1,2,3}
     * @return int
     */
    function postEnumerator(int $selected)
    {
        return $selected;
    }

    /**
     * UUID validation
     *
     * @param string $uuid {@from body}{@type uuid}
     *
     * @return string {@type uuid}
     */
    function postUUID($uuid)
    {
        return $uuid;
    }

    /**
     * Email validation
     *
     * @param string $email {@from body}{@type email}
     *
     * @return string {@type email}
     */
    function postEmail($email)
    {
        return $email;
    }

    /**
     * Date validation
     *
     * @param string $date {@from body}{@type date}
     *
     * @return string {@type date}
     */
    function postDate($date)
    {
        return $date;
    }

    /**
     * Array of dates
     *
     * @param array $dates Dates array{@from body}{@type date}
     *
     * @return array {@type date}
     */
    function postDates(array $dates)
    {
        return $dates;
    }

    /**
     * DateTime validation
     *
     * @param string $datetime {@from body}{@type datetime}
     *
     * @return string {@type datetime}
     */
    function postDatetime($datetime)
    {
        return $datetime;
    }

    /**
     * time validation
     *
     * @param string $time {@from body}{@type time}
     *
     * @return string {@type time}
     */
    function postTime($time)
    {
        return $time;
    }

    /**
     * time validation in 12 hour format
     *
     * @param string $time12 {@from body}{@type time12}
     *
     * @return string {@type time12}
     */
    function postTime12($time12)
    {
        return $time12;
    }

    /**
     * Timestamp validation
     *
     * @param string $timestamp {@from body}{@type timestamp}
     *
     * @return string {@type timestamp}
     */
    function postTimestamp($timestamp)
    {
        return $timestamp;
    }

    /**
     * Integer validation
     *
     * @param array $integers {@type int}
     *
     * @return array {@type int}
     */
    function postIntegers(array $integers)
    {
        return $integers;
    }

    /**
     * Array of numbers
     *
     * @param array $numbers {@type float}
     *
     * @return array {@type float}
     */
    function postNumbers(array $numbers)
    {
        return $numbers;
    }

    /**
     * Array of time strings
     *
     * @param array $times {@from body}{@type time}
     *
     * @return array {@type time}
     */
    function postTimes(array $times)
    {
        return $times;
    }

    /**
     * Array of timestamps
     *
     * @param array $timestamps {@from body}{@type timestamp}
     *
     * @return array {@type timestamp}
     */
    function postTimestamps(array $timestamps)
    {
        return $timestamps;
    }

    /**
     * Custom class parameter
     *
     * @param Person $author
     *
     * @return Person
     */
    function postAuthor(Person $author)
    {
        return $author;
    }

    /**
     * Array of authors
     *
     * @param array $authors {@type Person}
     *
     * @return array {@type Person}
     */
    function postAuthors(array $authors)
    {
        return $authors;
    }

    /**
     * An associative array
     *
     * @param array $object {@type associative}
     *
     * @return array {@type associative}
     */
    function postObject(array $object)
    {
        return $object;
    }

    /**
     * An indexed array
     *
     * @param array $array {@type indexed}
     *
     * @return array {@type indexed}
     */
    function postArray(array $array)
    {
        return $array;
    }

    /**
     * An array indexed or associative
     *
     * @param array $array
     *
     * @return array
     */
    function postArrayOrObject(array $array)
    {
        return $array;
    }
}

class Person
{
    /**
     * @var string {@from body} {@min 3}{@max 100}
     * name of the Author {@required true}
     */
    public $name = 'Name';
    /**
     * @var string {@type email} {@from body} {@required false}
     * email id of the Author
     */
    public $email = 'name@domain.com';
}
