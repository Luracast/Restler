<?php
namespace DB;
use Luracast\Restler\Data\iValueObject;
use Luracast\Restler\Util;

/**
 * Class Task
 *
 * used for model definition
 *
 * @package DB
 */
class Task implements iValueObject
{
    public $id = 0;
    public $position = 0;
    public $text = '';

    /**
     * This static method is called for creating an instance of the class by
     * passing the initiation values as an array.
     *
     * @static
     *
     * @param array $properties
     *
     * @return iValueObject
     */
    public static function __set_state(array $properties)
    {
        $task = new Task();
        $task->id = Util::nestedValue($properties, 'id');
        $task->position = Util::nestedValue($properties, 'position');
        $task->text = Util::nestedValue($properties, 'text');
        return $task;
    }

    /**
     * This method provides a string representation for the instance
     *
     * @return string
     */
    public function __toString()
    {
        return "Task(id = $this->id, position = $this->position, text = $this->text)";
    }
}