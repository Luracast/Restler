<?php

use Luracast\Restler\Utils\Text;


/**
 * Fake Database. All records are stored in $_SESSION
 */
class SessionDataProvider implements DataProviderInterface
{
    private $name;

    function __construct(string $name)
    {
        $this->name = "_sdp_$name";
        @session_start();
        if (empty($_SESSION[$this->name])) {
            $this->install();
        }
    }

    private function pk()
    {
        return $_SESSION[$this->name]['pk']++;
    }

    private function find($id)
    {
        foreach ($_SESSION[$this->name]['rs'] as $index => $rec) {
            if ($rec['id'] == $id) {
                return $index;
            }
        }
        return false;
    }

    function get($id)
    {
        $index = $this->find($id);
        if ($index === false) {
            return false;
        }
        return $_SESSION[$this->name]['rs'][$index];
    }

    function getAll()
    {
        return $_SESSION[$this->name]['rs'];
    }

    function insert($rec)
    {
        $rec['id'] = $this->pk();
        array_push($_SESSION[$this->name]['rs'], $rec);
        return $rec;
    }

    function update($id, $rec, $create = true)
    {
        $index = $this->find($id);
        if (!$create && $index === false) {
            return false;
        }
        $rec['id'] = $id;
        $_SESSION[$this->name]['rs'][$index] = $rec;
        return $rec;
    }

    function delete($id)
    {
        $index = $this->find($id);
        if ($index === false) {
            return false;
        }
        $record = array_splice($_SESSION[$this->name]['rs'], $index, 1);
        return array_shift($record);
    }

    private function install()
    {
        /** install initial data **/
        $_SESSION[$this->name] = [
            'pk' => 5,
            'rs' => [
                [
                    'id' => 1,
                    'name' => 'Jac Wright',
                    'email' => 'jacwright@gmail.com'
                ],
                [
                    'id' => 2,
                    'name' => 'Arul Kumaran',
                    'email' => 'arul@luracast.com'
                ]
            ]
        ];
    }

    static function reset()
    {
        foreach ($_SESSION as $key => $value) {
            if (Text::beginsWith($key, '_sdp_')) {
                unset($_SESSION[$key]);
            }
        }
    }
}

