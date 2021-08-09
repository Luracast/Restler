<?php

/**
 * MySQL DB. All data is stored in data_pdo_mysql database
 * Create an empty MySQL database and set the dbname, username
 * and password below
 *
 * This class will create the table with sample data
 * automatically on first `get` or `get($id)` request
 */

use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\RestException;

class MySQLDataProvider implements DataProviderInterface
{
    private $db;
    private $name;

    /**
     * @return PDO
     * @throws HttpException
     */
    private static function db()
    {
        try {
            //Make sure you are using UTF-8
            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

            //Update the dbname username and password to suit your server
            $db = new PDO(
                'mysql:host=localhost;dbname=data_pdo_mysql',
                'username',
                'password',
                $options
            );
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC);

            //If you are using older version of PHP and having issues with Unicode
            //uncomment the following line
            //$this->db->exec("SET NAMES utf8");
            return $db;
        } catch (PDOException $e) {
            throw new HttpException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    /**
     * MySQLDataProvider constructor.
     * @throws HttpException
     */
    function __construct(string $name)
    {
        $this->name = $name;
        $this->db = static::db();
    }

    /**
     * @param $id
     * @param bool $installTableOnFailure
     * @return array
     * @throws HttpException
     */
    function get($id, $installTableOnFailure = false)
    {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->db->prepare('SELECT * FROM ' . $this->name . ' WHERE id = :id');
            $sql->execute(array(':id' => $id));
            return $this->id2int($sql->fetch());
        } catch (PDOException $e) {
            if (!$installTableOnFailure && $e->getCode() == '42S02') {
                //SQLSTATE[42S02]: Base table or view not found: 1146 Table 'authors' doesn't exist
                $this->install();
                return $this->get($id, true);
            }
            throw new HttpException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function getAll($installTableOnFailure = false)
    {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $stmt = $this->db->query('SELECT * FROM ' . $this->name);
            return $this->id2int($stmt->fetchAll());
        } catch (PDOException $e) {
            if (!$installTableOnFailure && $e->getCode() == '42S02') {
                //SQLSTATE[42S02]: Base table or view not found: 1146 Table 'authors' doesn't exist
                $this->install();
                return $this->getAll(true);
            }
            throw new HttpException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    /**
     * @param $rec
     * @return array|bool
     * @throws HttpException
     */
    function insert($rec)
    {
        $sql = $this->db->prepare("INSERT INTO $this->name (name, email) VALUES (:name, :email)");
        if (!$sql->execute(array(':name' => $rec['name'], ':email' => $rec['email']))) {
            return false;
        }
        return $this->get($this->db->lastInsertId());
    }

    /**
     * @param $id
     * @param $rec
     * @param bool $create
     * @return array|bool
     * @throws HttpException
     */
    function update($id, $rec, $create = true)
    {
        if($create && !$this->get($id)){
            $sql = $this->db->prepare("INSERT INTO $this->name (id, name, email) VALUES (:id, :name, :email)");
            if (!$sql->execute(array(
                ':id' => $id,
                ':name' => $rec['name'],
                ':email' => $rec['email']
            ))) {
                return false;
            }
        } else {
            $sql = $this->db->prepare("UPDATE $this->name SET name = :name, email = :email WHERE id = :id");
            if (!$sql->execute(array(
                ':id' => $id,
                ':name' => $rec['name'],
                ':email' => $rec['email']
            ))) {
                if (!$create) {
                    return false;
                }

            }
        }
        return $this->get($id);
    }

    /**
     * @param $id
     * @return array|bool
     * @throws HttpException
     */
    function delete($id)
    {
        $r = $this->get($id);
        if (!$r || !$this->db->prepare('DELETE FROM ' . $this->name . ' WHERE id = :id')->execute(array(
                ':id' => $id
            ))) {
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
            "CREATE TABLE $this->name (
                id INT AUTO_INCREMENT PRIMARY KEY ,
                name TEXT NOT NULL ,
                email TEXT NOT NULL
            ) DEFAULT CHARSET=utf8;"
        );
        $this->db->exec(
            "INSERT INTO $this->name (name, email) VALUES ('Jac  Wright', 'jacwright@gmail.com');
             INSERT INTO $this->name (name, email) VALUES ('Arul Kumaran', 'arul@luracast.com');"
        );
    }

    /**
     * @throws HttpException
     */
    static function reset()
    {
        $db = static::db();
        $db->exec(
            "
            SELECT concat('DROP TABLE IF EXISTS ', table_name, ';')
            FROM information_schema.tables
            WHERE table_schema = 'MyDatabaseName';
            "
        );
    }
}

