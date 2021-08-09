<?php

namespace improved;

use DataProviderInterface;
use Luracast\Restler\Exceptions\HttpException;
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
         * $this->dp = new SqliteDB('db2');
         * $this->dp = new MySQLDataProvider('db2');
         * $this->dp = new SerializedFileDB('db2');
         * $this->dp = new SessionDataProvider('db2');
         * $this->dp = new ArrayDB('db2');
         */
        $class = ClassName::get(DataProviderInterface::class);
        $this->dp = new $class('db2');
    }

    function index()
    {
        return $this->dp->getAll();
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws HttpException
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
     * @status 201
     *
     * @param string $name {@from body}
     * @param string $email {@type email} {@from body}
     *
     * @return mixed
     */
    function post($name, $email)
    {
        return $this->dp->insert(compact('name', 'email'));
    }

    /**
     * @param int $id
     * @param string $name {@from body}
     * @param string $email {@type email} {@from body}
     *
     * @return mixed
     * @throws HttpException
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
     * @param int $id
     * @param string $name {@from body}
     * @param string $email {@type email} {@from body}
     *
     * @return mixed
     * @throws HttpException
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
     * @param int $id
     *
     * @return array
     * @throws HttpException
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

