<?php
/**
 * SQLite DB. All data is stored in data_pdo_sqlite.sq3 file
 * This file will be automatically created when missing
 * Make sure this folder has sufficient write permission 
 * for this page to create the file.
 */
class DB_PDO_Sqlite
{
    private $db;
    function __construct ()
    {
        $file = dirname(__FILE__) . '/data_pdo_sqlite.sq3';
        $db_found = file_exists($file);
        $this->db = new PDO('sqlite:' . $file);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        if (! $db_found)
            $this->install();
    }
    function get ($id)
    {
        $sql = 'SELECT * FROM authors WHERE id = ' . mysql_escape_string($id);
        return $this->id2int($this->db->query($sql)
            ->fetch());
    }
    function getAll ()
    {
        $stmt = $this->db->query('SELECT * FROM authors');
        return $this->id2int($stmt->fetchAll());
    }
    function insert ($rec)
    {
        $name = mysql_escape_string($rec['name']);
        $email = mysql_escape_string($rec['email']);
        $sql = "INSERT INTO authors (name, email) VALUES ('$name', '$email')";
        if (! $this->db->query($sql))
            return FALSE;
        return $this->get($this->db->lastInsertId());
    }
    function update ($id, $rec)
    {
        $id = mysql_escape_string($id);
        $name = mysql_escape_string($rec['name']);
        $email = mysql_escape_string($rec['email']);
        $sql = "UPDATE authors SET name = '$name', email ='$email' WHERE id = $id";
        if (! $this->db->query($sql))
            return FALSE;
        return $this->get($id);
    }
    function delete ($id)
    {
        $r = $this->get($id);
        if (! $r ||
         ! $this->db->query(
        'DELETE FROM authors WHERE id = ' . mysql_escape_string($id)))
            return FALSE;
        return $r;
    }
    private function id2int ($r)
    {
        if (is_array($r)) {
            if (isset($r['id'])) {
                $r['id'] = intval($r['id']);
            } else {
                foreach ($r as &$r0) {
                    $r0['id'] = intval($r0['id']);
                }
            }
        }
        return $r;
    }
    private function install ()
    {
        $this->db->exec(
        "CREATE TABLE authors(
            'id' INTEGER PRIMARY KEY AUTOINCREMENT,
            'name' TEXT,
            'email' TEXT
        )");
        $this->db->exec(
        "INSERT INTO authors (name, email) VALUES ('Jac Wright', 'jacwright@gmail.com');
         INSERT INTO authors (name, email) VALUES ('Arul Kumaran', 'arul@luracast.com');
        ");
    }
}