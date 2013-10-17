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
    public static $root = 'home';
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
     * @var array prefix additional menu items with one of the following syntax
     *            [$path => $text]
     *            [$path]
     *            [$path => ['text' => $text, 'url' => $url]]
     */
    public static $prepends = array();
    /**
     * @var array suffix additional menu items with one of the following syntax
     *            [$path => $text]
     *            [$path]
     *            [$path => ['text' => $text, 'url' => $url]]
     */
    public static $appends = array();

    public static function get($activeUrl = null)
    {
        if (is_null($activeUrl)) {
            $activeUrl = Util::$restler->url;
        }

        $tree = array();
        foreach (static::$prepends as $path => $text) {
            $url = null;
            if (is_array($text)) {
                $url = $text['url'];
                $text = $text['text'];
            }
            if (is_numeric($path)) {
                $path = $text;
                $text = null;
            }
            static::build($tree, $path, $url, $text, $activeUrl);
        }
        $routes = Routes::toArray();
        foreach ($routes as $value) {
            foreach ($value as $httpMethod => $route) {
                if ($httpMethod != 'GET') {
                    continue;
                }
                $path = $route['url'];
                if (false !== strpos($path, '{'))
                    continue;
                $v = 'v' . Util::$restler->getRequestedApiVersion();
                if (0 !== strpos($path, $v))
                    continue;
                if ($route['accessLevel'] && !Util::$restler->_authenticated)
                    continue;
                $path = ltrim(str_replace($v, '', $path), '/');

                foreach (static::$excludedPaths as $exclude) {
                    if (0 === strpos($path, $exclude)) {
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
                static::build($tree, $path, null, $text, $activeUrl);
            }
        }
        foreach (static::$appends as $path => $text) {
            $url = null;
            if (is_array($text)) {
                $url = $text['url'];
                $text = $text['text'];
            }
            if (is_numeric($path)) {
                $path = $text;
                $text = null;
            }
            static::build($tree, $path, $url, $text, $activeUrl);
        }
        return $tree;
    }

    protected function build(&$tree, $path,
                             $url = null, $text = null, $activeUrl = null)
    {
        $parts = explode('/', $path);
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
                    if (is_null($url)) {
                        $p[$part]['href'] = Util::$restler->getBaseUrl()
                            . '/' . $path;
                    } else {
                        $p[$part]['href'] = $url;
                    }
                    if ($path == $activeUrl) {
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
        if (empty($name)) {
            $name = static::$root;
        }
        return ucfirst(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name));
    }

} 