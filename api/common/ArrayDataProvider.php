<?php


class ArrayDataProvider implements DataProviderInterface
{
    private $name;
    public static $data = [];

    function __construct(string $name)
    {
        $this->name = $name;
        if (empty(static::$data[$this->name])) {
            $this->install();
        }
    }

    private function pk()
    {
        return static::$data[$this->name]['pk']++;
    }

    private function find($id)
    {
        foreach (static::$data[$this->name]['rs'] as $index => $rec) {
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
        return static::$data[$this->name]['rs'][$index];
    }

    function getAll()
    {
        return static::$data[$this->name]['rs'];
    }

    function insert($rec)
    {
        $rec['id'] = $this->pk();
        array_push(static::$data[$this->name]['rs'], $rec);
        return $rec;
    }

    function update($id, $rec, $create = true)
    {
        $index = $this->find($id);
        if (!$create && $index === false) {
            return false;
        }
        $rec['id'] = $id;
        static::$data[$this->name]['rs'][$index] = $rec;
        return $rec;
    }

    function delete($id)
    {
        $index = $this->find($id);
        if ($index === false) {
            return false;
        }
        $record = array_splice(static::$data[$this->name]['rs'], $index, 1);
        return array_shift($record);
    }

    private function install()
    {
        /** install initial data **/
        static::$data[$this->name] = [
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
        static::$data = [];
    }
}