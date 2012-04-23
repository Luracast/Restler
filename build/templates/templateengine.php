<?php
class TemplateEngine
{
    public static $scope;
    public static function render (
    $template_name, 
    $scope = array(), 
    $path = NULL, 
    $file_name = NULL)
    {
        $scope['_'] = 'forward_to_method';
        TemplateEngine::$scope = $scope;
        if (! function_exists('forward_to_method')) {
            function forward_to_method ()
            {
                extract(TemplateEngine::$scope);
                $args = func_get_args();
                #echo '<pre>';
                #print_r($args);
                #echo '</pre>';
                $methodName = array_shift(
                $args);
                switch ($methodName) {
                    case 'require':
                        $file_info = explode('.', $args[0]);
                        if(count($file_info)==3)
                            return include $args[0];
                        //eval with wrapper
                        $template_text = file_get_contents($args[0], TRUE);
                        return eval("return <<<TEMPLATE\n$template_text\n\nTEMPLATE;\n");
                        break;
                    case 'if':
                        if (count($args) < 2)
                            $args[1] = '';
                        if (count($args) < 3)
                            $args[2] = '';
                        return $args[0] ? $args[1] : $args[2];
                        break;
                    default:
                        return call_user_func_array($methodName, $args);
                }
            }
            function pad ($pre, $string, $post = NULL)
            {
                if (is_null($post))
                    $post = $pre;
                return ! empty($string) ? $pre . $string . $post : '';
            }
            function space ($length)
            {
                # echo "SPACE($length) ";
                return str_repeat(' ', $length);
            }
            function dash ($string, $char = '-')
            {
                return str_repeat($char, strlen($string));
            }
            function repeat_foreach ($arr, $string)
            {
                $_ = 'forward_to_method';
                $r = '';
                if (empty($arr))
                    return $r;
                extract(TemplateEngine::$scope);
                $index = 0;
                $count = 1;
                foreach ($arr as $key => $value) {
                    if (is_array($value))
                        $value = (object) $value;
                         #echo PHP_EOL.'return ("'.$string.'");';
                    $r .= eval(
                    'return ("' . addslashes($string) . '");');
                    $index ++;
                    $count ++;
                }
                return $r;
            }
        }
        extract($scope);
        $file_info = explode('.', $template_name);
        switch (count($file_info)) {
            case 2:
                //eval with wrapper
                $template_text = file_get_contents(
                $template_name, 
                TRUE);
                $template_text = eval(
                "return <<<TEMPLATE\n$template_text\n\nTEMPLATE;\n");
                break;
            case 3:
                $extension = array_pop($file_info);
                $template_text = include $template_name;
                break;
            default:
                throw new Exception("invalid template file '$template_name'");
        }
        $template_name = implode('.', $file_info);
        $template_name = eval("return <<<TEMPLATE\n$template_name\nTEMPLATE;\n");
        if (is_null($path)) {
            return $template_text;
        } else {
            if (is_null($file_name)) {
                $file_name = pathinfo(
                $template_name, 
                PATHINFO_FILENAME) . '.' .
                 pathinfo($template_name, PATHINFO_EXTENSION);
            }
            $path = eval("return <<<TEMPLATE\n$path\nTEMPLATE;\n");
            #echo $path . PHP_EOL;
            mkdir_recursive($path, 0777);
            file_put_contents(
            $path . DIRECTORY_SEPARATOR . $file_name, 
            $template_text);
        }
    }
}
function mkdir_recursive ($pathname, $mode)
{
    is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
    return is_dir($pathname) || @mkdir($pathname, $mode);
}
/*
 $o = new stdClass();
 $o->styles=array('../markdown/markdown.css');
 $o->title="Hello World";
 $o->summary='First step to know more about Restler2';
 $o->description='Shows the bare minimum code needed to get your RESTful api server up and running';
 $o->local_files=array('index.php', 'math.php');
 $o->restler_files=array('restler.php');
 $o->routes_max=20;
 $o->routes=array (
 'say/hello' =>
 array (
 'httpMethod' => 'GET',
 'className' => 'Say',
 'methodName' => 'hello',
 'arguments' =>
 array (
 'to' => 0,
 ),
 'defaults' =>
 array (
 0 => 'world',
 ),
 'metadata' =>
 array (
 ),
 'methodFlag' => 0,
 ),
 'say/hello/:to' =>
 array (
 'httpMethod' => 'GET',
 'className' => 'Say',
 'methodName' => 'hello',
 'arguments' =>
 array (
 'to' => 0,
 ),
 'defaults' =>
 array (
 0 => 'world',
 ),
 'metadata' =>
 array (
 ),
 'methodFlag' => 0,
 ),
 );
 $o->examples = $o->routes;
 #TemplateEngine::render('readme.htm.php', (array)$o, '.');
 TemplateEngine::render('readme.htm.php', (array)$o);
 */