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
        return $numbers;
    }

    /**
     * @param Author $author
     *
     * @return Author
     */
    function postAuthor(Author $author)
    {
        return $author;
    }

    /**
     * @param array $authors {@type Author}
     *
     * @return mixed
     */
    function postAuthors(array $authors)
    {
        return $authors;
    }
}

class Author
{
    /**
     * @var string {@from body} {@min 3}{@max 100} name of the Author {@required true}
     */
    public $name='Name';
    /**
     * @var string {@type email} {@from body} email id of the Author
     */
    public $email='name@domain.com';
}