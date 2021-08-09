<?php
/**
 * SQLite DB. All data is stored in store folder
 * This file will be automatically created when missing
 * Make sure this folder has sufficient write permission
 * for this page to create the file.
 */

class SqliteDataProvider implements DataProviderInterface
{
    private $db;
    private static $folder;

    function __construct(string $name)
    {
        if (empty(static::$folder)) {
            static::$folder = __DIR__ . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR;
        }
        $file = static::$folder . $name . '.sqlite.sq3';
        $db_found = file_exists($file);
        $this->db = new PDO('sqlite:' . $file);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        if (!$db_found) {
            $this->install();
        }
    }

    function get($id)
    {
        $sql = $this->db->prepare('SELECT * FROM authors WHERE id = :id');
        $sql->execute(array(':id' => $id));
        return $this->id2int($sql->fetch());
    }

    function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM authors');
        return $this->id2int($stmt->fetchAll());
    }

    function insert($rec)
    {
        $sql = $this->db->prepare("INSERT INTO authors (name, email) VALUES (:name, :email)");
        if (!$sql->execute(array(':name' => $rec['name'], ':email' => $rec['email']))) {
            return false;
        }
        return $this->get($this->db->lastInsertId());
    }

    function update($id, $rec, $create = true)
    {
        if (!$create) {
            if (!$this->get($id)) {
                return false;
            }
        }
        $sql = $this->db->prepare("UPDATE authors SET name = :name, email = :email WHERE id = :id");
        if (!$sql->execute(array(':id' => $id, ':name' => $rec['name'], ':email' => $rec['email']))) {
            return false;
        }
        return $this->get($id);
    }

    function delete($id)
    {
        $r = $this->get($id);
        if (!$r || !$this->db->prepare('DELETE FROM authors WHERE id = ?')->execute(array($id))) {
            return false;
        }
        return $r;
    }

    private function id2int($r)
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

    private function install()
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

    static function reset()
    {
        foreach (glob(static::$folder . "*.sqlite.sq3") as $filename) {
            unlink($filename);
        }
    }
}

