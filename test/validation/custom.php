<?php
class Custom extends Luracast\Restler\Data\ValueObject {
    public $name = 'Unknown';
    public $age = 20;
    public $killer= true;

    public function __sleep(){
        return array('name','age');
    }

    /*
    public function __get($name){
        $method = 'get'.ucfirst($name);
        if(method_exists($this,$method)){
            return $method();
        }
    }
    */
    /**
     *
     * @return int the $age
    public function getAge()
{
return $this->age;
}
     */

    /**
     *
     * @param int $age            
    public function setAge($age)
{
$this->age = ( int ) $age;
}
     */

    public function getUnderAge()
    {
        return $this->age < 26;
    }
}