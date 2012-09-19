<?php
/**
 * Flat File DB. All data is serialized and stored in data_serialized.php
 * This file will be automatically created when missing
 * Make sure this folder has sufficient write permission
 * for this script to create the file.
 */
class DB_Serialized_File
{
    private $arr;
    private $modifed = FALSE;
    private $file;
    function __construct ()
    {
        $this->file = $file = dirname(__FILE__) . DIRECTORY_SEPARATOR .
         'data_serialized.php';
        /** load data from file **/
        if (file_exists($file)) {
            $this->arr = require_once $file;
        } else {
            $this->install();
        }
    }
    function __destruct ()
    {
        if ($this->modifed) {
            /** save data **/
            $content = "<?php\n";
            $content .= 'return ' . var_export($this->arr, TRUE) . ';';
            file_put_contents($this->file, $content);
        }
    }
    private function pk ()
    {
        return $this->arr['pk'] ++;
    }
    private function find ($id)
    {
        foreach ($this->arr['rs'] as $index => $rec) {
            if ($rec['id'] == $id) {
                return $index;
            }
        }
        return FALSE;
    }
    function get ($id)
    {
        $index = $this->find($id);
        if ($index === FALSE)
            return FALSE;
        return $this->arr['rs'][$index];
    }
    function getAll ()
    {
        return $this->arr['rs'];
    }
    function insert ($rec)
    {
        $rec['id'] = $this->pk();
        $this->modifed = TRUE;
        array_push($this->arr['rs'], $rec);
        return $rec;
    }
    function update ($id, $rec)
    {
        $index = $this->find($id);
        if ($index === FALSE)
            return FALSE;
        $rec['id'] = $id;
        $this->modifed = TRUE;
        $this->arr['rs'][$index] = $rec;
        return $rec;
    }
    function delete ($id)
    {
        $index = $this->find($id);
        if ($index === FALSE)
            return FALSE;
        $this->modifed = TRUE;
        return array_shift(array_splice($this->arr['rs'], $index, 1));
    }
    private function install ()
    {
        /** install initial data **/
        $this->arr = array();
        $this->arr['rs'] = array(
        array('id' => 1,
        'name' => 'Jac Wright',
        'email' => 'jacwright@gmail.com'),
        array('id' => 2,
        'name' => 'Arul Kumaran',
        'email' => 'arul@luracast.com'));
        $this->arr['pk'] = 5;
        $this->modifed = TRUE;
    }
}

