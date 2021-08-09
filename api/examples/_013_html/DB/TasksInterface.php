<?php
namespace DB;

interface TasksInterface
{
    /**
     * Get task by id
     *
     * @param int $id
     *
     * @return Task
     */
    public function get($id);

    /**
     * Get list of Tasks
     *
     * @return array {@type Task)
     */
    public function getAll();

    /**
     * Add a new task
     *
     * @param array $data
     *
     * @return Task
     */
    public function insert(array $data);

    /**
     * Update a task
     *
     * @param int $id
     * @param array $data
     * @param bool $create
     *
     * @return Task
     */
    public function update($id, array $data, $create = true);

    /**
     * Delete a task
     *
     * @param int $id
     *
     * @return Task
     */
    public function delete($id);

    /**
     * Install the initial data
     *
     * @return null
     */
    public function install();
}