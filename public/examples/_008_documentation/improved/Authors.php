<?php
namespace improved;
use Luracast\Restler\RestException;
use DB_Session;

class Authors
{
    public $dp;

    function __construct()
    {
        $this->dp = new DB_Session();
    }

    function index()
    {
        return $this->dp->getAll();
    }

    /**
     * @param int $id
     *
     * @return array
     */
    function get($id)
    {
        $r = $this->dp->get($id);
        if ($r === false)
            throw new RestException(404);
        return $r;
    }

    /**
     * @status 201
     *
     * @param string $name  {@from body}
     * @param string $email {@type email} {@from body}
     *
     * @return mixed
     */
    function post($name, $email)
    {
        return $this->dp->insert(compact('name', 'email'));
    }

    /**
     * @param int    $id
     * @param string $name  {@from body}
     * @param string $email {@type email} {@from body}
     *
     * @return mixed
     */
    function put($id, $name, $email)
    {
        $r = $this->dp->update($id, compact('name', 'email'));
        if ($r === false)
            throw new RestException(404);
        return $r;
    }

    /**
     * @param int    $id
     * @param string $name  {@from body}
     * @param string $email {@type email} {@from body}
     *
     * @return mixed
     */
    function patch($id, $name = null, $email = null)
    {
        $patch = $this->dp->get($id);
        if ($patch === false)
            throw new RestException(404);
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
            throw new RestException(304); //not modified
        }
        $r = $this->dp->update($id, $patch);
        if ($r === false)
            throw new RestException(404);
        return $r;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    function delete($id)
    {
        return $this->dp->delete($id);
    }
}

