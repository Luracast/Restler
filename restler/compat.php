<?php
if (version_compare('5.0.0', PHP_VERSION) > 0) {
    die('Restler requires PHP 5.x.x');
}
#requires 5.3.2
if (! method_exists('ReflectionMethod', 'setAccessible')) {
    #echo'RESTLER_METHOD_UNPROTECTION_MODE';
    function isRestlerCompatibilityModeEnabled ()
    {
        return TRUE;
    }
    function unprotect ($methodInfo)
    {
        $className = $methodInfo->className;
        $method = $methodInfo->methodName;
        $params = $methodInfo->arguments;
        $unique = uniqid('Dynamic') . "_";
        $classCode = "class $unique$className extends $className {";
        $p = array();
        for ($i = 0; $i < count($params); $i ++) {
            $p[] = '$' . "P$i";
        }
        $p = implode(',', $p);
        $classCode .= "function $unique$method($p)
        {return parent::$method($p);}";
        $classCode .= "}";
        #echo $classCode;
        eval($classCode);
        $methodInfo->className = "$unique$className";
        $methodInfo->methodName = "$unique$method";
        return $methodInfo;
    }
    function call_protected_user_method_array ($className, $method, $params)
    {
        if (is_object($className))
            $className = get_class($className);
        $unique = uniqid('Dynamic') . "_";
        $classCode = 
        "class $unique$className  extends  $className {";
        $p = array();
        for ($i = 0; $i < count($params); $i ++) {
            $p[] = '$' . "P$i";
        }
        $p = implode(',', $p);
        $classCode .= "function $unique$method($p)
        {return parent::$method($p);}";
        $classCode .= "}";
        #echo $classCode;
        eval($classCode);
        $obj = $unique . $className;
        $obj = new $obj();
        return call_user_func_array(array($obj, $unique . $method), $params);
    }
}

#requires 5.2.3
#if(!method_exists('ReflectionParameter', 'getPosition')){
#found fix! not using getPosition in restler 2.0
#}

#requires 5.3.0
#if(!defined('__DIR__')){
# avoided using it in Restler and replaced it with dirname(__FILE__)
#}
