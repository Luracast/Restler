<?php
class Custom extends ValueObject {
    public $name = 'Unknown';
    private $age = 0;

    /**
     *
     * @return the $age
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     *
     * @param int $age            
     */
    public function setAge($age)
    {
        $this->age = ( int ) $age;
    }

    public function getUnderAge()
    {
        return $this->age < 26;
    }
}