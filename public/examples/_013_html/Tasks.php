<?php

use DB\iTasks;
use DB\TasksInSession;
use Luracast\Restler\Format\HtmlFormat;

class Tasks
{
    /**
     * @var iTasks
     */
    protected $db;

    function __construct()
    {
        if (!$this->db) {
            $this->setDB(new TasksInSession());
        }
        HtmlFormat::$data['title'] = 'What\'s Next on Restler 3?';
        HtmlFormat::$data['description'] = 'What should we focus on as the next?';
    }

    private function setDB(iTasks $db)
    {
        $this->db = $db;
    }

    /**
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
     * @return \DB\Task
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
     * @param int    $id
     * @param string $text      {@from body}
     * @param int    $position  {@from body}
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