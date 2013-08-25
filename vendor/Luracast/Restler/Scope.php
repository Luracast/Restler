<?php

namespace Luracast\Restler;


class Scope
{
    /**
     * @var Restler instance injected at runtime
     */
    public static $restler;
    public static $classAliases = array(

        //Format classes
        'AmfFormat' => 'Luracast\\Restler\\Format\\AmfFormat',
        'JsFormat' => 'Luracast\\Restler\\Format\\JsFormat',
        'JsonFormat' => 'Luracast\\Restler\\Format\\JsonFormat',
        'HtmlFormat' => 'Luracast\\Restler\\Format\\HtmlFormat',
        'PlistFormat' => 'Luracast\\Restler\\Format\\PlistFormat',
        'UploadFormat' => 'Luracast\\Restler\\Format\\UploadFormat',
        'UrlEncodedFormat' => 'Luracast\\Restler\\Format\\UrlEncodedFormat',
        'XmlFormat' => 'Luracast\\Restler\\Format\\XmlFormat',
        'YamlFormat' => 'Luracast\\Restler\\Format\\YamlFormat',

        //Filter classes
        'RateLimit' => 'Luracast\\Restler\\Filter\\RateLimit',

        //API classes
        'Resources' => 'Luracast\\Restler\\Resources',

        //Cache classes
        'HumanReadableCache' => 'Luracast\\Restler\\HumanReadableCache',

        //Utility classes
        'Object' => 'Luracast\\Restler\\Data\\Object',

        //Exception
        'RestException' => 'Luracast\\Restler\\RestException'
    );
    private static $instances = array();
    private static $filteredInstances = array();

    /**
     * Apply static and non-static properties for the instance of the given
     * class name using the method information metadata annotation provided,
     * creating new instance when the given instance is null
     *
     * @static
     *
     * @param string $classNameOrInstance      name or instance of the class
     *                                         to apply properties to
     * @param array  $metadata                 properties as key value pairs
     *
     * @throws RestException
     * @internal param null|object $instance new instance is crated if set to null
     *
     * @return object instance of the specified class with properties applied
     */
    public static function findOne($classNameOrInstance, array $metadata = null)
    {
        if (is_object($classNameOrInstance)) {
            $instance = $classNameOrInstance;
            $instance->restler = self::$restler;
            $className = get_class($instance);
        } else {
            $className = $classNameOrInstance = ltrim($classNameOrInstance, '\\');
            if (isset(self::$classAliases[$classNameOrInstance])) {
                $classNameOrInstance = self::$classAliases[$classNameOrInstance];
            }
            if (!class_exists($classNameOrInstance)) {
                throw new RestException(500, "Class '$classNameOrInstance' not found");
            }
            if (isset(self::$instances[$classNameOrInstance])) {
                $instance = self::$instances[$classNameOrInstance];
            } else {
                $instance = new $classNameOrInstance();
                $instance->restler = self::$restler;
                self::$instances[$classNameOrInstance] = $instance;
            }
        }
        $shortName = Util::getShortName($className);
        $properties = null;
        if (isset($metadata['class'][$className][CommentParser::$embeddedDataName])) {
            $properties = $metadata['class'][$className][CommentParser::$embeddedDataName];

        } elseif (isset($metadata['class'][$shortName][CommentParser::$embeddedDataName])) {
            $properties = $metadata['class'][$shortName][CommentParser::$embeddedDataName];
        }
        if (isset($properties)) {

            $objectVars = get_object_vars($instance);

            if (is_array($properties)) {
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
        if ($instance instanceof iUseAuthentication && self::$restler->_authVerified) {
            $instance->__setAuthenticationStatus
                (self::$restler->_authenticated);
        }

        return $instance;
    }

    public static function get($className)
    {
        $fullName = $className = ltrim($className, '\\');
        if (isset(static::$classAliases)) {
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
            self::$instances[$className] = compact('instance');
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
                    $m, 'class', $fullName, CommentParser::$embeddedDataName)
                )
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
        return $instance;
    }
}