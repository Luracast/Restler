<?php
namespace Luracast\Restler\UI;

use Luracast\Restler\CommentParser;
use Luracast\Restler\Routes;
use Luracast\Restler\Util;


/**
 * Utility class for automatically creating data to build an navigation interface
 * based on available routes that are accessible by the current user
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc4
 */
class Nav
{
    /**
     * @var null|callable if the api methods are under access control mechanism
     * you can attach a function here that returns true or false to determine
     * visibility of a protected api method. this function will receive method
     * info as the only parameter.
     */
    public static $accessControlFunction = null;
    /**
     * @var array all paths beginning with any of the following will be excluded
     * from documentation
     */
    public static $excludedPaths = array();
    /**
     * @var array add additional menu items with $url => $text here
     */
    public static $includePaths = array();

    public static function get($activeUrl = null)
    {
        if (is_null($activeUrl)) {
            $activeUrl = Util::$restler->url;
        }
        $tree = array();
        foreach (static::$includePaths as $url => $text) {
            static::build($tree, $url, $text, $activeUrl);
        }
        $routes = Routes::toArray();
        foreach ($routes as $value) {
            foreach ($value as $httpMethod => $route) {
                if ($httpMethod != 'GET') {
                    continue;
                }
                $url = $route['url'];
                if (false !== strpos($url, '{'))
                    continue;
                $v = 'v' . Util::$restler->getRequestedApiVersion();
                if (0 !== strpos($url, $v))
                    continue;
                if ($route['accessLevel'] && !Util::$restler->_authenticated)
                    continue;
                $url = ltrim(str_replace($v, '', $url), '/');
                foreach (static::$excludedPaths as $exclude) {
                    if (0 === strpos($url, $exclude)) {
                        continue 2;
                    }
                }
                if (Util::$restler->_authenticated
                    && static::$accessControlFunction
                    && (!call_user_func(
                        static::$accessControlFunction, $route['metadata']))
                ) {
                    continue;
                }
                $text = Util::nestedValue(
                    $route,
                    'metadata',
                    CommentParser::$embeddedDataName,
                    'label'
                );
                static::build($tree, $url, $text, $activeUrl);
            }
        }
        return $tree;
    }

    protected function build(&$tree, $url, $text = null, $activeUrl = null)
    {
        $parts = explode('/', $url);
        $p = & $tree;
        $end = end($parts);
        foreach ($parts as $part) {
            if (!isset($p[$part])) {
                $p[$part] = array(
                    'href' => '#',
                    'text' => static::title($part)
                );
                if ($part == $end) {
                    if ($text)
                        $p[$part]['text'] = $text;
                    $p[$part]['href'] = $url;
                    if ($url == $activeUrl) {
                        $p[$part]['active'] = true;
                    }
                }
                $p[$part]['children'] = array();

            }
            $p = & $p[$part]['children'];
        }

    }

    protected static function title($name)
    {
        return ucfirst(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name));
    }

} 