<?php
/**
 * Sqlite database
 */
class SqliteDB {
	private $db;
	function __construct() {
		if ($db = new SQLiteDatabase(SqliteDB::NAME.'sqlite') {
			$q = @$db->query('SELECT requests FROM tablename WHERE id = 1');
			if ($q === false) {
				$db->queryExec('CREATE TABLE tablename (id int, requests int, PRIMARY KEY (id)); INSERT INTO tablename VALUES (1,1)');
				$hits = 1;
			} else {
				$result = $q->fetchSingle();
				$hits = $result+1;
			}
			$db->queryExec("UPDATE tablename SET requests = '$hits' WHERE id = 1");
		} else {
			die($err);
		}
		$this->db = new SQLite3('author.sqlite');
	}
	private function find($id){
		foreach ($_SESSION['rs'] as $index => $rec) {
			if ($rec['id'] == $id) {
				return $index;
			}
		}
		return FALSE;
	}
	function get($id) {
		$results = $this->db->query("SELECT * FROM author");
		$r=array();
		while ($row = $results->fetchArray()) {
			print_r($row);
			$r[]=$row;
		}
		return $row;
	}
	function getAll() {
		return $_SESSION['rs'];
	}
	public function insert($rec) {
		$rec['id']=$this->pk();
		array_push($_SESSION['rs'], $rec);
		return $rec;
	}
	public function update($id, $rec) {
		$index = $this->find($id);
		if($index===FALSE)return FALSE;
		$rec['id']=$id;
		$_SESSION['rs'][$index]=$rec;
		return $rec;
	}
	public function delete($id) {
		$index = $this->find($id);
		if($index===FALSE)return FALSE;
		return array_shift(array_splice($_SESSION['rs'], $index, 1));
	}
}

// Sample data.
function getData() {
	return array(
	array('id' => 1, 'name' => 'Jac Wright',   'email' => 'jacwright@gmail.com'),
	array('id' => 2, 'name' => 'Arul Kumaran', 'email' => 'arul@luracast.com'  ),
	);
}