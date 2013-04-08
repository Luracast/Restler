<?php
namespace Luracast\Restler;

use ReflectionClass;
use ReflectionMethod;

/**
 * Router class that routes the urls to api methods along with parameters
 *
 * @category   Framework
 * @package    Restler
 * @subpackage result
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class Routes
{
    const PATH = '→';
    protected static $routes = array();

    public static function addPath($path, $httpMethod = 'GET', array $call)
    {
        $p =& static::$routes;
        $parts = explode('/', $path);
        foreach ($parts as $part) {
            if (!isset($p[static::PATH][$part])) {
                $p[static::PATH][$part] = array();
            }
            $p = & $p[static::PATH][$part];
        }
        $p[$httpMethod] = $call;
    }

    public static function addAPIClass($className, $resourcePath = '')
    {

        /*
         * Mapping Rules - Optional parameters should not be mapped to URL - if
         * a required parameter is of primitive type - Map them to URL - Do not
         * create routes with out it - if a required parameter is not primitive
         * type - Do not include it in URL
         */
        $reflection = new ReflectionClass($className);
        $classMetadata = CommentParser::parse($reflection->getDocComment());
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC +
            ReflectionMethod::IS_PROTECTED);
        foreach ($methods as $method) {
            $methodUrl = strtolower($method->getName());
            //method name should not begin with _
            if ($methodUrl{0} == '_') {
                continue;
            }
            $doc = $method->getDocComment();
            $metadata = CommentParser::parse($doc) + $classMetadata;
            //@access should not be private
            if (isset($metadata['access'])
                && $metadata['access'] == 'private'
            ) {
                continue;
            }
            $arguments = array();
            $defaults = array();
            $params = $method->getParameters();
            $position = 0;
            $ignorePathTill = false;
            $allowAmbiguity
                = (isset($metadata['smart-auto-routing'])
                && $metadata['smart-auto-routing'] != 'true')
                || !Defaults::$smartAutoRouting;
            $metadata['resourcePath'] = $resourcePath;
            if (isset($classMetadata['description'])) {
                $metadata['classDescription'] = $classMetadata['description'];
            }
            if (isset($classMetadata['classLongDescription'])) {
                $metadata['classLongDescription']
                    = $classMetadata['longDescription'];
            }
            if (!isset($metadata['param'])) {
                $metadata['param'] = array();
            }
            foreach ($params as $param) {
                $type =
                    $param->isArray() ? 'array' : $param->getClass();
                if ($type instanceof ReflectionClass) {
                    $type = $type->getName();
                }
                $arguments[$param->getName()] = $position;
                $defaults[$position] = $param->isDefaultValueAvailable() ?
                    $param->getDefaultValue() : null;
                if (!isset($metadata['param'][$position])) {
                    $metadata['param'][$position] = array();
                }
                $m = & $metadata ['param'] [$position];
                if (isset($type)) {
                    $m['type'] = $type;
                }
                $m ['name'] = trim($param->getName(), '$ ');
                $m ['default'] = $defaults [$position];
                $m ['required'] = !$param->isOptional();

                if (isset($m[CommentParser::$embeddedDataName]['from'])) {
                    $from = $m[CommentParser::$embeddedDataName]['from'];
                } else {
                    if ((isset($type) && Util::isObjectOrArray($type))
                        || $param->getName() == Defaults::$fullRequestDataName
                    ) {
                        $from = 'body';
                    } elseif ($m['required']) {
                        $from = 'path';
                    } else {
                        $from = 'query';
                    }
                }
                $m['from'] = $from;

                if (!$allowAmbiguity && $from == 'path') {
                    $ignorePathTill = $position + 1;
                }
                $position++;
            }
            $accessLevel = 0;
            if ($method->isProtected()) {
                $accessLevel = 3;
            } elseif (isset($metadata['access'])) {
                if ($metadata['access'] == 'protected') {
                    $accessLevel = 2;
                } elseif ($metadata['access'] == 'hybrid') {
                    $accessLevel = 1;
                }
            } elseif (isset($metadata['protected'])) {
                $accessLevel = 2;
            }
            /*
            echo " access level $accessLevel for $className::"
            .$method->getName().$method->isProtected().PHP_EOL;
            */

            // take note of the order
            $call = array(
                'className' => $className,
                'path' => rtrim($resourcePath, '/'),
                'methodName' => $method->getName(),
                'arguments' => $arguments,
                'defaults' => $defaults,
                'metadata' => $metadata,
                'accessLevel' => $accessLevel,
            );
            // if manual route
            if (preg_match_all(
                '/@url\s+(GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS)'
                    . '[ \t]*\/?(\S*)/s',
                $doc, $matches, PREG_SET_ORDER
            )
            ) {
                foreach ($matches as $match) {
                    $httpMethod = $match[1];
                    $url = rtrim($resourcePath . $match[2], '/');
                    static::addPath($url, $httpMethod, $call);
                }
                //if auto route enabled, do so
            } elseif (Defaults::$autoRoutingEnabled) {
                // no configuration found so use convention
                if (preg_match_all(
                    '/^(GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS)/i',
                    $methodUrl, $matches)
                ) {
                    $httpMethod = strtoupper($matches[0][0]);
                    $methodUrl = substr($methodUrl, strlen($httpMethod));
                } else {
                    $httpMethod = 'GET';
                }
                if ($methodUrl == 'index') {
                    $methodUrl = '';
                }
                $url = empty($methodUrl) ? rtrim($resourcePath, '/')
                    : $resourcePath . $methodUrl;
                if (!$ignorePathTill) {
                    static::addPath($url, $httpMethod, $call);
                }
                $position = 1;
                foreach ($params as $param) {
                    $from = $metadata ['param'] [$position - 1] ['from'];

                    if ($from == 'body' && ($httpMethod == 'GET' ||
                        $httpMethod == 'DELETE')
                    ) {
                        $from = $metadata ['param'] [$position - 1] ['from']
                            = 'query';
                    }

                    if (!$allowAmbiguity && $from != 'path') {
                        break;
                    }
                    if (!empty($url)) {
                        $url .= '/';
                    }

                    $url .= '{' .
                        $call['metadata']['param'][$position - 1]['type']{0} .
                        ($position - 1) . '}';
                    if ($allowAmbiguity || $position == $ignorePathTill) {
                        static::addPath($url, $httpMethod, $call);
                    }
                    $position++;
                }
            }
        }
        Util::$restler->cache->set('new_routes', static::$routes);
    }

    public static function find($path, $httpMethod)
    {
        $p =& static::$routes;
        $parts = explode('/', $path);
        $params = array();
        $found = false;
        foreach ($parts as $part) {
            if (isset($p[static::PATH][$part])) {
                //static path found
                $p = & $p[static::PATH][$part];
                $found = true;
            } else {
                //try dynamic path
                $found = false;
                foreach ($p[static::PATH] as $key => $value) {
                    if (isset($value[$httpMethod])) {
                        $call = & $value[$httpMethod];
                    } else {
                        continue;
                    }
                    echo $key . ' ' . $part . PHP_EOL;
                    $regex = str_replace(array('{', '}'),
                        array('(?P<', '>[^/]+)'), $key);
                    if (preg_match(":^$regex$:i", $part, $matches)) {
                        foreach ($matches as $arg => $match) {
                            if (isset($call['arguments'][$arg])) {
                                $params[$arg] = $match;
                            }
                        }
                        $p = & $value;
                        $found = true;
                        break;
                    }
                }
                break;
            }
        }
        if ($found) {
            $call = (object)$p[$httpMethod];
            $d = $call->defaults;
            foreach ($call->arguments as $key => $value) {
                if (isset($params[$key])) {
                    $d[$value] = $params[$key];
                }
            }
            $call->arguments = $d;
            return $call;
        }
    }
}