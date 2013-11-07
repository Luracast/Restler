<?php
namespace Luracast\Restler;

/**
 * Scope resolution class, manages instantiation and acts as a dependency
 * injection container
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 */
class Scope
{
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
    public static $properties = array();
    protected static $instances = array();
    protected static $registry = array();

    public static function register($name, Callable $function, $singleton = true)
    {
        static::$registry[$name] = (object)compact('function', 'singleton');
    }

    public static function set($name, $instance)
    {
        static::$instances[$name] = (object)array('instance' => $instance);
    }

    public static function get($name)
    {
        $r = null;
        $initialized = false;
        $properties = array();
        if (array_key_exists($name, static::$instances)) {
            $initialized = true;
            $r = static::$instances[$name]->instance;
        } elseif (!empty(static::$registry[$name])) {
            $function = static::$registry[$name]->function;
            $r = $function();
            if (static::$registry[$name]->singleton)
                static::$instances[$name] = (object)array('instance' => $r);
        } else {
            $fullName = $name;
            if (isset(static::$classAliases[$name])) {
                $fullName = static::$classAliases[$name];
            }
            if (class_exists($fullName)) {
                $r = new $fullName();
                static::$instances[$name] = (object)array('instance' => $r);
                if ($name != 'Restler') {
                    $r->restler = static::get('Restler');
                    if ($m = $r->restler->apiMethodInfo) {
                        $properties = Util::nestedValue(
                            $m, 'class', $name,
                            CommentParser::$embeddedDataName
                        ) ? : array();
                    } else {
                        static::$instances[$name]->initPending = true;
                    }
                }
            }
        }
        if (
            $r instanceof iUseAuthentication &&
            static::get('Restler')->_authVerified &&
            !isset(static::$instances[$name]->authVerified)
        ) {
            static::$instances[$name]->authVerified = true;
            $r->__setAuthenticationStatus
                (static::get('Restler')->_authenticated);
        }
        if (isset(static::$instances[$name]->initPending) &&
            $m = static::get('Restler')->apiMethodInfo
        ) {
            $properties = Util::nestedValue(
                $m, 'class', $name,
                CommentParser::$embeddedDataName
            ) ? : array();
            unset(static::$instances[$name]->initPending);
            $initialized = false;
        }
        if (!$initialized && is_object($r)) {
            $properties += static::$properties;
            $objectVars = get_object_vars($r);
            $className = get_class($r);
            foreach ($properties as $property => $value) {
                if (property_exists($className, $property)) {
                    //if not a static property
                    array_key_exists($property, $objectVars)
                        ? $r->{$property} = $value
                        : $r::$$property = $value;
                }
            }
        }
        return $r;
    }
}
