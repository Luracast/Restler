<?php
error_reporting(-1);
ini_set('display_errors','On'); 
require_once ('Mustache.php'); // Ensure feature/higher-order-sections branch !!
class MyMustache extends Mustache
{
    function __isset ($name)
    {
        echo "isset verified on $name ";
        return TRUE;
    }
    function hasMethod($name){
        return strpos($name, ' ');
        echo "method verified on $name ";
        return $name=='lambda';
    }
    function __call($method, $args)
    {
        echo "Method $method called ";
        $view = $this;
        return function ($block) use( $view)
        {
            return '<strong>' . $view->render($block) . '</strong>';
        };
    }
    function __get ($name)
    {
        return "Hello $name";
    }
    function Kesal ()
    {
        return 'Nahi';
    }
    public function name ()
    {
        return "Luke";
    }
    
    public function lambdas ()
    {
        $view = $this;
        return function  ($block) use( $view)
        {
            return '<strong>' . $view->render($block) . '</strong>';
        };
    }
}
$m = new MyMustache();
echo $m->render("{{#lambda}}
 - one
 - two
 - three
{{welcome}} {{name}}{{/lambda}}");