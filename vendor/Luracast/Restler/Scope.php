<?php

namespace Luracast\Restler;


use Luracast\Restler\Proxy;

class Scope
{
    /**
     * @var Restler instance injected at runtime
     */
    public static $restler;
    public static $classAliases = array(

        //Core
        'Restler' => 'Luracast\Restler\Restler',

        //Format classes
        'AmfFormat' => 'Luracast\Restler\Format\AmfFormat',
        'JsFormat' => 'Luracast\Restler\Format\JsFormat',
        'JsonFormat' => 'Luracast\Restler\Format\JsonFormat',
        'HtmlFormat' => 'Luracast\Restler\Format\HtmlFormat',
        'PlistFormat' => 'Luracast\Restler\Format\PlistFormat',
        'UploadFormat' => 'Luracast\Restler\Format\UploadFormat',
        'UrlEncodedFormat' => 'Luracast\Restler\Format\UrlEncodedFormat',
        'XmlFormat' => 'Luracast\Restler\Format\XmlFormat',
        'YamlFormat' => 'Luracast\Restler\Format\YamlFormat',

        //Filter classes
        'RateLimit' => 'Luracast\Restler\Filter\RateLimit',

        //API classes
        'Resources' => 'Luracast\Restler\Resources',

        //Cache classes
        'HumanReadableCache' => 'Luracast\Restler\HumanReadableCache',

        //Utility classes
        'Object' => 'Luracast\Restler\Data\Object',

        //Exception
        'RestException' => 'Luracast\Restler\RestException'
    );
    private static $instances = array();
    private static $filteredInstances = array();

    public static function override($className, $method, callable $replacement)
    {
        $instance = static::get($className);
        static::$filteredInstances[$className] = $instance;
    }

    /**
     * Get instance of a class
     *
     * Apply static and non-static properties for the instance of the given
     * class name using the api method metadata creating new instance when the
     * given instance is null
     *
     * @static
     *
     * @param string $className      name of the class
     *
     * @throws RestException
     * @internal param null|object $instance new instance is crated if set to null
     *
     * @return object instance of the specified class with properties applied
     */
    public static function get($className)
    {
        $fullName = $className = ltrim($className, '\\');
        if (isset(static::$classAliases[$className])) {
            $fullName = static::$classAliases[$className];
        }
        if (isset(self::$instances[$className])) {
            $instance = self::$instances[$className]['instance'];
        } else {
            if (!class_exists($fullName)) {
                throw new RestException(500, "Class '$fullName' not found");
            }
            $instance = new $fullName();
            $instance->restler = self::$restler;
            self::$instances[$className] = array(
                'instance' => $instance = new Proxy($instance)
            );

        }
        if (
            !isset(self::$instances[$className]['metadata']) &&
            isset(static::$restler->apiMethodInfo)
        ) {
            $m = static::$restler->apiMethodInfo;
            self::$instances[$className]['metadata'] = true;
            if (
                ($properties = Util::nestedValue(
                    $m, 'class', $className, CommentParser::$embeddedDataName
                )) ||
                ($properties = Util::nestedValue(
                    $m, 'class', $fullName, CommentParser::$embeddedDataName
                ))
            ) {
                $objectVars = get_object_vars($instance);
                foreach ($properties as $property => $value) {
                    if (property_exists($className, $property)) {
                        //if not a static property
                        array_key_exists($property, $objectVars)
                            ? $instance->{$property} = $value
                            : $instance::$$property = $value;
                    }
                }
            }
        }
        if (
            !isset(self::$instances[$className]['authVerified']) &&
            $instance instanceof iUseAuthentication &&
            self::$restler->_authVerified
        ) {
            self::$instances[$className]['authVerified'] = true;
            $instance->__setAuthenticationStatus
                (self::$restler->_authenticated);
        }
        return $instance;
    }

    public static function preCall($className, $method, callable $observer)
    {

    }

    public static function postCall($className, $method, callable $observer)
    {

    }

    public static function filter($className, $method, callable $filter)
    {

    }
}