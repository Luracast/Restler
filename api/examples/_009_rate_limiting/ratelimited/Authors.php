<?php

namespace ratelimited;

use DataProviderInterface;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Filters\RateLimiter;
use Author;
use Luracast\Restler\Utils\ClassName;

class Authors
{
    /**
     * @var DataProviderInterface
     */
    public $dp;

    function __construct()
    {
        /**
         * $this->dp = new SqliteDB('db3');
         * $this->dp = new MySQLDataProvider('db3');
         * $this->dp = new SerializedFileDB('db3');
         * $this->dp = new SessionDataProvider('db3');
         * $this->dp = new ArrayDB('db3');
         */
        $class = ClassName::get(DataProviderInterface::class);
        $this->dp = new $class('db3');
    }

    /**
     * Retrieve all Authors
     *
     * Get all Authors and their details
     * @cache max-age={expires}, max-stale=3000, must-revalidate
     * @expires 30
     * @throttle 200
     * @class RateLimiter {@unit second} {@usagePerUnit 1}
     * @return array {@type Author}
     */
    function index()
    {
        return $this->dp->getAll();
    }

    /**
     * Retrieve Author by id
     *
     * Specify Author id in the url to retrieve specific Author. If there is no
     * Author with the specified id, HTTP 404 not found will be returned.
     *
     * @param int $id AuthorID
     *
     * @throws 404 Author not found
     * @return Author
     */
    function get($id)
    {
        $r = $this->dp->get($id);
        if ($r === false) {
            throw new HttpException(404);
        }
        return $r;
    }

    /**
     * Create new Author
     *
     * Create new Author by passing valid name and email id. HTTP 201 Created on
     * Success.
     *
     * @status 201
     *
     * @param string $name {@from body} {@max 100} name of the Author
     * not exceeding 100 characters
     * @param string $email {@type email} {@from body} email id of the Author
     *
     * @throws HttpException
     * @return Author
     */
    function post($name, $email)
    {
        $r = $this->dp->insert(compact('name', 'email'));
        if ($r == false) {
            throw new HttpException(304); //not modified
        }
        return $r;
    }

    /**
     * Update Author
     *
     * Replace Author details for a specific Author id
     *
     * @access protected
     *
     * @param int $id AuthorID
     * @param string $name {@from body} {@max 100} name of the Author
     *                      not exceeding 100 characters
     * @param string $email {@type email} {@from body} email id of the Author
     *
     * @throws HttpException
     * @return Author
     */
    function put($id, $name, $email)
    {
        $r = $this->dp->update($id, compact('name', 'email'));
        if ($r === false) {
            throw new HttpException(404);
        }
        return $r;
    }

    /**
     * Update Author partially
     *
     * Modify name and / or email details of a specific Author
     *
     * @access protected
     *
     * @param int $id AuthorID
     * @param string $name {@from body} {@max 100} name of the Author
     *                      not exceeding 100 characters
     * @param string $email {@type email} {@from body} email id of the Author
     *
     * @throws HttpException
     * @return Author
     */
    function patch($id, $name = null, $email = null)
    {
        $patch = $this->dp->get($id);
        if ($patch === false) {
            throw new HttpException(404);
        }
        $modified = false;
        if (isset($name)) {
            $patch['name'] = $name;
            $modified = true;
        }
        if (isset($email)) {
            $patch['email'] = $email;
            $modified = true;
        }
        if (!$modified) {
            throw new HttpException(304); //not modified
        }
        $r = $this->dp->update($id, $patch);
        if ($r === false) {
            throw new HttpException(404);
        }
        return $r;
    }

    /**
     * Delete an Author
     *
     * Remove an Author by id
     *
     * @access protected
     *
     * @param int $id AuthorID
     *
     * @throws HttpException
     * @return Author
     */
    function delete($id)
    {
        $r = $this->dp->delete($id);
        if ($r === false) {
            throw new HttpException(404);
        }
        return $r;
    }
}

