<?php
class Author {
	public $dp;

	static $FIELDS = array('name', 'email');

	function __construct(){
	    /**
		* $this->dp = new DB_PDO_Sqlite();
		* $this->dp = new DB_PDO_MySQL();
		* $this->dp = new DB_Serialized_File();
		*/
		$this->dp = new DB_Session();
	}

	function get($id=NULL) {
		return is_null($id) ? $this->dp->getAll() : $this->dp->get($id);
	}
	function post($request_data=NULL) {
		return $this->dp->insert($this->_validate($request_data));
	}
	function put($id=NULL, $request_data=NULL) {
		return $this->dp->update($id, $this->_validate($request_data));
	}
	function delete($id=NULL) {
		return $this->dp->delete($id);
	}

	private function _validate($data){
		$author=array();
		foreach (Author::$FIELDS as $field) {
//you may also vaildate the data here
			if(!isset($data[$field]))throw new RestException(417,"$field field missing");
			$author[$field]=$data[$field];
		}
		return $author;
	}
}