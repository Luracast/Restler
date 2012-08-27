<?php
/**
 * API to list, modify Authors of the Restler
 *
 * @author arulkumaran
 */
use Luracast\Restler\RestException;

class Authors
{
    public $dp;
    static $FIELDS = array('name', 'email');

    function __construct()
    {
        /**
         * $this->dp = new DB_Session();
         * $this->dp = new DB_PDO_MySQL();
         * $this->dp = new DB_Serialized_File();
         */
        $this->dp = new DBPDOSqlite();
    }

    function index()
    {
        return $this->dp->getAll();
    }

    /**
     * Get Author for the specified Author ID
     *
     * Passing a valid author id, will result in getting
     * details (name, email, author id) of the specific
     * Author.
     *
     * @param int $id Author ID
     *              ``` min=1&max=2&error=AuthorID+is+out+of+range```
     *
     * @throws Luracast\Restler\RestException
     *              417 one or more of required fields missing
     *
     * @throws Luracast\Restler\RestException
     *              404 Author not found
     *
     * @return Author author instance for the given id;
     */
    function get($id)
    {
        return is_null($id) ? $this->dp->getAll() : $this->dp->get($id);
    }

    /**
     * Create new Author
     *
     * @param array $request_data
     *
     * @throws Luracast\Restler\RestException
     *              417 one or more of required fields missing
     *
     * @return array|bool|mixed
     */
    function post($request_data)
    {
        return $this->dp->insert($this->_validate($request_data));
    }

    /**
     * Update Author information
     *
     * @param int   $id Author ID
     *          ``` min=1&max=2&error=AuthorID+is+out+of+range```
     * @param array $request_data
     */
    function put($id, $request_data)
    {
        return $this->dp->update($id, $this->_validate($request_data));
    }

    /**
     * Delete Author by ID
     *
     * @param int $id Author ID
     *          ``` min=1&max=2&error=AuthorID+is+out+of+range```
     */
    function deleteSomething($id)
    {
        return $this->dp->delete($id);
    }

    private function _validate($data)
    {
        $author = array();
        foreach (self::$FIELDS as $field) {
            //you may also validate the data here
            if (!isset($data[$field]))
                throw new RestException(417, "$field field missing");
            $author[$field] = $data[$field];
        }
        return $author;
    }
}