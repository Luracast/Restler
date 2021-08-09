<?php

use DB\Task;
use DB\TasksInterface;
use DB\TasksStore;
use Luracast\Restler\StaticProperties;

/**
 * Class Tasks
 * @response-format Html,Json
 */
class Tasks
{
    /**
     * @var TasksInterface
     */
    protected $db;

    function __construct(StaticProperties $html)
    {
        if (!$this->db) {
            $this->setDB(new TasksStore());
        }
        $html->data['title'] = 'What\'s Next on Restler 3?';
        $html->data['description'] = 'What should we focus on as the next?';
    }

    private function setDB(TasksInterface $db)
    {
        $this->db = $db;
    }

    /**
     * @return Task[]
     * @view todo/index
     */
    function index()
    {
        return $this->db->getAll();
    }

    /**
     * Get task by id
     *
     * @param int $id
     *
     * @return Task
     *
     * @view todo/list  {@value response}
     */
    function get($id)
    {
        return $this->db->get($id);
    }

    /**
     * Create new task
     *
     * @param string $text {@from body}
     *
     * @return \DB\Task
     *
     * @view todo/list  {@value response}
     */
    function post($text)
    {
        return $this->db->insert(compact('text'));
    }

    /**
     * @param int $id
     * @param string $text {@from body}
     * @param int $position {@from body}
     *
     * @return \DB\Task
     *
     * @view todo/list  {@value response}
     */
    function patch($id, $text = null, $position = null)
    {
        return $this->db->update($id, compact('text', 'position'));
    }

    /**
     * delete a task by id
     *
     * @param int $id
     *
     * @return \DB\Task
     *
     * @view todo/list  {@value response}
     */
    function delete($id)
    {
        return $this->db->delete($id);
    }

}
