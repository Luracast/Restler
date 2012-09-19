<?php
/**
 * Fake Database. All records are stored in $_SESSION
 */
class DB_Session
{
    function __construct ()
    {
        @session_start();
        if (! isset($_SESSION['pk'])) {
            $this->install();
        }
    }
    private function pk ()
    {
        return $_SESSION['pk'] ++;
    }
    private function find ($id)
    {
        foreach ($_SESSION['rs'] as $index => $rec) {
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
        return $_SESSION['rs'][$index];
    }
    function getAll ()
    {
        return $_SESSION['rs'];
    }
    function insert ($rec)
    {
        $rec['id'] = $this->pk();
        array_push($_SESSION['rs'], $rec);
        return $rec;
    }
    function update ($id, $rec)
    {
        $index = $this->find($id);
        if ($index === FALSE)
            return FALSE;
        $rec['id'] = $id;
        $_SESSION['rs'][$index] = $rec;
        return $rec;
    }
    function delete ($id)
    {
        $index = $this->find($id);
        if ($index === FALSE)
            return FALSE;
        return array_shift(array_splice($_SESSION['rs'], $index, 1));
    }
    private function install ()
    {
        /** install initial data **/
        $_SESSION['pk'] = 5;
        $_SESSION['rs'] = array(
        array('id' => 1, 'name' => 'Jac Wright',
        'email' => 'jacwright@gmail.com'),
        array('id' => 2, 'name' => 'Arul Kumaran',
        'email' => 'arul@luracast.com'));
    }
}

