<?php

class Simple
{
    public function pub()
    {
        return "im the only api when not authenticated";
    }

    /**
     * Use PHPDoc description to provide meaningful text here.
     * Long Description becomes the Implementation notes :)
     *
     * @access protected
     *
     * @param string $name use \@param type $name description for describing
     *                     parameters like this
     *  /**
     *  * some comment
     *  {@*}
     *
     * @return string
     */
    public function get($name)
    {
        return $this->restler->apiMethodInfo;
        return "Worm welcome to $name!";
    }


}
