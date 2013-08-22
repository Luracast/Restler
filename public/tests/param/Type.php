<?php
class Type
{
    /**
     * Email validation
     *
     * @param string $email {@from body}{@type email}
     */
    function postEmail($email)
    {
        return $email;
    }

    /**
     * Date validation
     *
     * @param string $date {@from body}{@type date}
     */
    function postDate($date)
    {
        return $date;
    }

    /**
     * DateTime validation
     *
     * @param string $datetime {@from body}{@type datetime}
     */
    function postDatetime($datetime)
    {
        return $datetime;
    }

    /**
     * Timestamp validation
     *
     * @param string $timestamp {@from body}{@type timestamp}
     */
    function postTimestamp($timestamp)
    {
        return $timestamp;
    }

    /**
     * Integer validation
     *
     * @param array $integers {@type int}
     */
    function postIntegers(array $integers)
    {
        return $integers;
    }

    /**
     * Array of numbers
     *
     * @param array $numbers {@type float}
     */
    function postNumbers(array $numbers)
    {
        return $numbers;
    }

    /**
     * Custom class parameter
     *
     * @param Author $author
     *
     * @return Author
     */
    function postAuthor(Author $author)
    {
        return $author;
    }

    /**
     * Array of authors
     *
     * @param array $authors {@type Author}
     *
     * @return mixed
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
     * @return array
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
     * @return array
     */
    function postArray(array $array)
    {
        return $array;
    }
}

class Author
{
    /**
     * @var string {@from body} {@min 3}{@max 100}
     * name of the Author {@required true}
     */
    public $name = 'Name';
    /**
     * @var string {@type email} {@from body}
     * email id of the Author
     */
    public $email = 'name@domain.com';
}