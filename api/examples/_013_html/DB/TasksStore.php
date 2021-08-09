<?php

namespace DB;

use Luracast\Restler\Defaults;
use Luracast\Restler\Exceptions\HttpException;

/**
 * All records are stored in a serialized file
 */
class TasksStore implements TasksInterface
{
    private $arr;
    private $modified = false;
    private static $folder;
    private $file;

    function __construct(string $name = 'tasks')
    {
        if (empty(static::$folder)) {
            static::$folder = Defaults::$cacheDirectory . DIRECTORY_SEPARATOR;
        }
        $this->file = $file = static::$folder . $name . '.serialized.php';
        /** load data from file **/
        if (file_exists($file)) {
            $this->arr = require $file;
        } else {
            $this->install();
        }
    }

    private function save()
    {
        if ($this->modified) {
            /** save data **/
            $content = "<?php\n";
            $content .= 'return ' . var_export($this->arr, true) . ';';
            file_put_contents($this->file, $content);
        }
    }

    private function pk()
    {
        return $this->arr['pk']++;
    }

    private function find($id)
    {
        foreach ($this->arr['rs'] as $index => $rec) {
            if ($rec->id == $id) {
                return $index;
            }
        }
        return false;
    }

    function get($id)
    {
        $position = $this->find($id);
        if ($position === false) {
            throw new HttpException(404, 'no such task');
        }
        /** @var Task $task */
        $task = $this->arr['rs'][$position];
        $task->position = $position;
        return $task;
    }

    function getAll()
    {
        $r = array();
        foreach ($this->arr['rs'] as $position => $task) {
            $task->position = $position;
            $r[] = $task;
        }
        return $r;
    }

    function insert(array $data)
    {
        $data['id'] = $this->pk();
        $this->modified = true;
        $data['position'] = count($this->arr['rs']);
        $task = Task::__set_state($data);
        array_push($this->arr['rs'], $task);
        $this->save();
        return $task;
    }

    function update($id, array $data, $create = true)
    {
        $position = $this->find($id);
        if (!$create && $position === false) {
            throw new HttpException(404, 'no such task');
        }
        /** @var Task $task */
        $task = $this->arr['rs'][$position];
        if (isset($data['text'])) {
            $this->modified = true;
            $task->text = $data['text'];
        }
        if (isset($data['position'])) {
            $new_pos = min(count($this->arr['rs']), $data['position']);
            $new_pos = max(0, $new_pos);
            //if position has changed re-arrange
            if ($position != $new_pos) {
                $this->modified = true;
                array_splice($this->arr['rs'], $position, 1);
                array_splice($this->arr['rs'], $new_pos, 0, array($task));
                $task->position = $new_pos;
            }
        }
        $this->save();
        return $task;
    }

    function delete($id)
    {
        $position = $this->find($id);
        if ($position === false) {
            throw new HttpException(404, 'no such task');
        }
        /** @var Task $task */
        $task = $this->arr['rs'][$position];
        $this->modified = true;
        array_splice($this->arr['rs'], $position, 1);
        $this->save();
        $task->position = -1;
        return $task;
    }

    /**
     * Install the initial data
     *
     * @return null
     */
    public function install()
    {
        /** install initial data **/
        $this->arr = array('rs' => [], 'pk' => 1);
        $this->insert(array('text' => 'OAuth 2'));
        $this->insert(array('text' => 'If-Modified-Since'));
        $this->insert(array('text' => 'E-Tag'));
        $this->insert(array('text' => 'ORM Examples'));
    }
}
