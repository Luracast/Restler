<?php
/** 
 * @author arulkumaran
 */
class MustacheTemplate extends Mustache
{
    public $view;
    
    function __construct ($template = NULL, $view = NULL, $partials = NULL, 
    array $options = NULL)
    {
        $this->view = is_array($view) ? $view : array();
        parent::__construct($template, $this, $partials, $options);
    }
    function hasMethod($name){
        return strpos($name, ' ');
    }
    function __isset ($name)
    {
        return isset($this->view[$name]);
    }
    function __get ($name)
    {
        return $this->view[$name];
    }
}
