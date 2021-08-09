<?php

namespace DB;

use JsonSerializable;
use Luracast\Restler\Contracts\ValueObjectInterface;

/**
 * Class Task
 *
 * used for model definition
 *
 * @package DB
 */
class Task implements ValueObjectInterface
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
     * @return ValueObjectInterface
     */
    public static function __set_state(array $properties): ValueObjectInterface
    {
        $task = new static();
        $task->id = $properties['id'] ?? null;
        $task->position = $properties['position'] ?? null;
        $task->text = $properties['text'] ?? null;
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

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'text' => $this->text,
        ];
    }
}