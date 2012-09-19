<?php
class Authors
{
    public $dp;

    static $FIELDS = array('name', 'email');

    function __construct()
    {
        /**
         * $this->dp = new DB_PDO_Sqlite();
         * $this->dp = new DB_PDO_MySQL();
         * $this->dp = new DB_Serialized_File();
         */
        $this->dp = new DB_Session();
    }

    function index()
    {
        return $this->dp->getAll();
    }

    function get($id)
    {
        return $this->dp->get($id);
    }

    function post($request_data = NULL)
    {
        return $this->dp->insert($this->_validate($request_data));
    }

    function put($id, $request_data = NULL)
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
            if (!isset($data[$field]))
                throw new RestException(400, "$field field missing");
            $author[$field] = $data[$field];
        }
        return $author;
    }
}

