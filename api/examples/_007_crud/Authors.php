<?php

use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Utils\ClassName;

class Authors
{
    /**
     * @var DataProviderInterface
     */
    public $dp;

    static $FIELDS = array('name', 'email');

    function __construct()
    {
        /**
         * $this->dp = new SqliteDB('db1');
         * $this->dp = new MySQLDataProvider('db1');
         * $this->dp = new SerializedFileDB('db1');
         * $this->dp = new SessionDataProvider('db1');
         * $this->dp = new ArrayDB('db1');
         */
        $class = ClassName::get(DataProviderInterface::class);
        $this->dp = new $class('db1');
    }

    function index()
    {
        return $this->dp->getAll();
    }

    function get($id)
    {
        return $this->dp->get($id);
    }

    function post($request_data = null)
    {
        return $this->dp->insert($this->_validate($request_data));
    }

    function put($id, $request_data = null)
    {
        return $this->dp->update($id, $this->_validate($request_data));
    }

    function delete($id)
    {
        return $this->dp->delete($id);
    }

    private function _validate($data)
    {
        $author = array();
        foreach (authors::$FIELDS as $field) {
            //you may also validate the data here
            if (!isset($data[$field])) {
                throw new HttpException(400, "$field field missing");
            }
            $author[$field] = $data[$field];
        }
        return $author;
    }
}

