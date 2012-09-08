<?php
namespace Luracast\Restler;
/**
 * Describe the purpose of this class/interface/trait
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0
 */
class Util
{
    /**
     * @var Restler instance injected at runtime
     */
    public static $restler;

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
        return !(boolean)strpos('|bool|boolean|int|float|string|', $type);
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
     * Apply static and non-static properties for the instance of the given
     * class name using the method information metadata annotation provided,
     * creating new instance when the given instance is null
     *
     * @static
     *
     * @param string      $className name of the class to apply properties to
     * @param array       $metadata  which contains the properties
     * @param null|object $instance  new instance is crated if set to null
     *
     * @throws RestException
     * @return object instance of the specified class with properties applied
     */
    public static function setProperties($className, array $metadata = null,
                                         $instance = null)
    {
        if (!class_exists($className)) {
            throw new RestException(500, "Class '$className' not found");
        }
        if (!$instance) {
            $instance = new $className();
            $instance->restler = self::$restler;
        }
        if (
            isset($metadata['class'][$className]
            [CommentParser::$embeddedDataName])
        ) {
            $properties = $metadata['class'][$className]
            [CommentParser::$embeddedDataName];

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

        if ($instance instanceof iUseAuthentication) {
            $instance->__setAuthenticationStatus(self::$restler->authenticated);
        }

        return $instance;
    }
}

