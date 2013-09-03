<?php
namespace Luracast\Restler;
/**
 * Describe the purpose of this class/interface/trait
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
class Util
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

    /**
     * verify if the given data type string is scalar or not
     *
     * @static
     *
     * @param string $type data type as string
     *
     * @return bool true or false
     */
    public static function isObjectOrArray($type)
    {
        if (is_array($type)) {
            foreach ($type as $t) {
                if (static::isObjectOrArray($t)) {
                    return true;
                }
            }
            return false;
        }
        return !(boolean)strpos('|bool|boolean|int|float|string|', $type);
    }

    /**
     * Get the value deeply nested inside an array / object
     *
     * Using isset() to test the presence of nested value can give a false positive
     *
     * This method serves that need
     *
     * When the deeply nested property is found its value is returned, otherwise
     * false is returned.
     *
     * @param array         $from   array to extract the value from
     * @param string|array  $key... pass more to go deeply inside the array
     *                              alternatively you can pass a single array
     *
     * @return null|mixed null when not found, value otherwise
     */
    public static function nestedValue($from, $key /**, $key2 ... $key`n` */)
    {
        if(is_array($key)){
            $keys =  $key;
        } else {
            $keys = func_get_args();
            array_shift($keys);
        }
        foreach ($keys as $key) {
            if (is_array($from) && isset($from[$key])) {
                $from = $from[$key];
                continue;
            } elseif (is_object($from) && isset($from->{$key})) {
                $from = $from->{$key};
                continue;
            }
            return null;
        }
        return $from;
    }

    public static function getResourcePath($className,
                                           $resourcePath = null,
                                           $prefix = '')
    {
        if (is_null($resourcePath)) {
            if (Defaults::$autoRoutingEnabled) {
                $resourcePath = strtolower($className);
                if (false !== ($index = strrpos($className, '\\')))
                    $resourcePath = substr($resourcePath, $index + 1);
                if (false !== ($index = strrpos($resourcePath, '_')))
                    $resourcePath = substr($resourcePath, $index + 1);
            } else {
                $resourcePath = '';
            }
        } else
            $resourcePath = trim($resourcePath, '/');
        if (strlen($resourcePath) > 0)
            $resourcePath .= '/';
        return $prefix . $resourcePath;
    }

    /**
     * Compare two strings and remove the common
     * sub string from the first string and return it
     *
     * @static
     *
     * @param string $fromPath
     * @param string $usingPath
     * @param string $char
     *            optional, set it as
     *            blank string for char by char comparison
     *
     * @return string
     */
    public static function removeCommonPath($fromPath, $usingPath, $char = '/')
    {
        if (empty($fromPath))
            return '';
        $fromPath = explode($char, $fromPath);
        $usingPath = explode($char, $usingPath);
        while (count($usingPath)) {
            if ($fromPath[0] == $usingPath[0]) {
                array_shift($fromPath);
            } else {
                break;
            }
            array_shift($usingPath);
        }
        return implode($char, $fromPath);
    }

    /**
     * Parses the request to figure out the http request type
     *
     * @static
     *
     * @return string which will be one of the following
     *        [GET, POST, PUT, PATCH, DELETE]
     * @example GET
     */
    public static function getRequestMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($_GET['method'])) {
            // support for exceptional clients who can't set the header
            $m = strtoupper($_GET['method']);
            if ($m == 'PUT' || $m == 'DELETE' ||
                $m == 'POST' || $m == 'PATCH'
            ) {
                $method = $m;
            }
        }
        // support for HEAD request
        if ($method == 'HEAD') {
            $method = 'GET';
        }
        return $method;
    }

    /**
     * Pass any content negotiation header such as Accept,
     * Accept-Language to break it up and sort the resulting array by
     * the order of negotiation.
     *
     * @static
     *
     * @param string $accept header value
     *
     * @return array sorted by the priority
     */
    public static function sortByPriority($accept)
    {
        $acceptList = array();
        $accepts = explode(',', strtolower($accept));
        if (!is_array($accepts)) {
            $accepts = array($accepts);
        }
        foreach ($accepts as $pos => $accept) {
            $parts = explode(';q=', trim($accept));
            $type = array_shift($parts);
            $quality = count($parts) ?
                floatval(array_shift($parts)) :
                (1000 - $pos) / 1000;
            $acceptList[$type] = $quality;
        }
        arsort($acceptList);
        return $acceptList;
    }

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
    public static function initialize($classNameOrInstance, array $metadata = null)
    {
        if (is_object($classNameOrInstance)) {
            $instance = $classNameOrInstance;
            $instance->restler = self::$restler;
            $className = get_class($instance);
        } else {
            $className = ltrim($classNameOrInstance, '\\');
            if (isset(self::$classAliases[$className])) {
                $className = self::$classAliases[$className];
            }
            if (!class_exists($className)) {
                throw new RestException(500, "Class '$className' not found");
            }
            $instance = new $className();
            $instance->restler = self::$restler;
        }
        $shortName = static::getShortName($className);
        $properties =
            Util::nestedValue(
                $metadata, 'class', $className, CommentParser::$embeddedDataName
            ) ? :
                Util::nestedValue(
                    $metadata, 'class', $shortName, CommentParser::$embeddedDataName
                );

        if (is_array($properties)) {

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
        if ($instance instanceof iUseAuthentication && self::$restler->_authVerified) {
            $instance->__setAuthenticationStatus
                (self::$restler->_authenticated);
        }
        return $instance;
    }

    public static function getShortName($className)
    {
        $className = explode('\\', $className);
        return end($className);
    }
}

