<?php
class Custom extends ValueObject{
    public $name;
    private $age;
	/**
     * @return the $age
     */
    public function getAge()
    {
        return $this->age;
    }

	/**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

}