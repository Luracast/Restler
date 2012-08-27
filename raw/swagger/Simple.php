<?php
class Simple
{
    /**
     * Use PHPDoc description to provide meaningful text here.
     * Long Description becomes the Implementation notes :)
     *
     * @param string $name use \@param type $name description for describing
     * parameters like this
     *  /**
     *  * some comment
     *  {@*}
     *
     * @return string
     */
    public function get($name){
        return $this->restler->apiMethodInfo;
        return "Worm welcome to $name!";
    }

}
