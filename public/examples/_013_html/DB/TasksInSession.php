<?php
namespace DB;
use Luracast\Restler\RestException;
use Luracast\Restler\Util;

/**
 * Fake Database. All records are stored in $_SESSION
 */
class TasksInSession implements iTasks
{

    public function __construct()
    {
        @session_start();
        if (!isset($_SESSION['id_gen'])) {
            $this->install();
        }
    }

    /**
     * Install the initial data
     *
     * @return null
     */
    public function install()
    {
        $_SESSION['id_gen'] = 1;
        $_SESSION['tasks'] = array();
        $this->insert(array('text' => 'OAuth 2'));
        $this->insert(array('text' => 'If-Modified-Since'));
        $this->insert(array('text' => 'E-Tag'));
        $this->insert(array('text' => 'ORM Examples'));
    }

    /**
     * Add a new task
     *
     * @param array $data
     *
     * @return Task
     */
    public function insert(array $data)
    {
        $task = new Task();
        $task->id = $this->pk();
        $task->text = $data['text'];
        $_SESSION['tasks'][] = $task;
        $task->position = count($_SESSION['tasks']) - 1;
        return $task;
    }

    private function pk()
    {
        return $_SESSION['id_gen']++;
    }

    /**
     * Get task by id
     *
     * @param int $id
     *
     * @return Task
     */
    public function get($id)
    {
        $position = $this->find($id);
        if ($position === FALSE)
            throw new RestException(404, 'no such task');
        /**
         * @var Task;
         */
        $task = $_SESSION['tasks'][$position];
        $task->position = $position;
        return $task;
    }

    private function find($id)
    {
        foreach ($_SESSION['tasks'] as $position => $task) {
            if ($task->id == $id) {
                return $position;
            }
        }
        return FALSE;
    }

    /**
     * Get list of Tasks
     *
     * @return array {@type Task)
     */
    public function getAll()
    {
        $r = array();
        foreach ($_SESSION['tasks'] as $position => $task) {
            $task->position = $position;
            $r[] = $task;
        }
        return $r;
    }

    /**
     * Update a task
     *
     * @param int   $id
     * @param array $data
     *
     * @throws \Luracast\Restler\RestException 404
     * @return Task
     */
    public function update($id, array $data)
    {
        $position = $this->find($id);
        if ($position === FALSE)
            throw new RestException(404, 'no such task');
        /**
         * @var Task;
         */
        $task = $_SESSION['tasks'][$position];
        if (isset($data['text']))
            $task->text = $data['text'];
        if (isset($data['position'])) {
            $new_pos = min(count($_SESSION['tasks']), $data['position']);
            $new_pos = max(0, $new_pos);
            if ($position != $new_pos) {
                //if position has changed re-arrange
                array_splice($_SESSION['tasks'], $position, 1);
                array_splice($_SESSION['tasks'], $new_pos, 0, array($task));
                $task->position = $new_pos;
            }
        }
        return $task;
    }

    /**
     * Delete a task
     *
     * @param int $id
     *
     * @throws \Luracast\Restler\RestException 404
     * @return Task
     */
    public function delete($id)
    {
        $position = $this->find($id);
        if ($position === FALSE)
            throw new RestException(404, 'no such task');
        /**
         * @var Task;
         */
        $task = $_SESSION['tasks'][$position];
        array_splice($_SESSION['tasks'], $position, 1);
        $task->position = -1;
        return $task;
    }
}