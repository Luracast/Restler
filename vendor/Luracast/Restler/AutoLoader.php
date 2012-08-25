<?php
namespace Luracast\Restler;

/**
 * Class that implements spl_autoload facilities and multiple
 * conventions support.
 * Supports composer libraries and 100% PSR-0 compliant.
 * In addition we enable namespace prefixing and class aliases.
 *
 * @category   Framework
 * @package    restler
 * @subpackage helper
 * @author     Nick Lombar <github@jigsoft.co.za>
 * @copyright  2012 Luracast
 */
class AutoLoader
{
    protected static $instance,
        $classMap = array(),
        $aliases = array(
        'Luracast\\Restler' => null,
        'Luracast\\Restler\\Format' => null,
        'Luracast\\Restler\\Data' => null,
    );

    /**
     * Seen this before cache handler.
     * Facilitates both lookup and persist operations.
     *
     * @param string $key   class name considered or a collection or classMap
     *                      entries
     * @param bool   $value - optional value to set for the supplied key
     *
     * @return bool The known value for the key or false
     */
    protected function seen($key, $value = false)
    {
        if (is_array($key)) {
            static::$classMap = $key + static::$classMap;
            return false;
        }

        if (empty(static::$classMap[$key])) {
            static::$classMap[$key] = $value;
        }

        if (is_string($ret = static::$classMap[$key])) {
            if (false === strpos($ret, '.php')
                && isset(static::$classMap[$ret])
            ) {
                return static::$classMap[$ret];
            }
        }

        return static::$classMap[$key];
    }

    /**
     * Singleton instance facility.
     *
     * @static
     * @return AutoLoader the current instance or new instance if none exists.
     */
    public static function instance()
    {
        if(!static::$instance){
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Protected constructor to enforce singleton pattern.
     */
    protected function __construct()
    {
        if (false === $this->seen('__include_path')) {

            $paths = explode(PATH_SEPARATOR, get_include_path());
            $slash = DIRECTORY_SEPARATOR;
            $dir = dirname(__DIR__);
            $source_dir = dirname($dir);
            $dir = dirname($source_dir);

            foreach (array(
                         array($source_dir),
                         array($dir, '..', '..', 'composer'),
                         array($dir, 'vendor', 'composer'),
                         array($dir, '..', '..', '..', 'php'),
                         array($dir, 'vendor', 'php'),
                     ) as $includePath)
                if (file_exists($path = implode($slash, $includePath))) {
                    if ('composer' == end($includePath)) {
                        $this->seen(
                            require "$path{$slash}autoload_classmap.php");
                        $paths = array_merge(
                            $paths,
                            array_values(
                                require "$path{$slash}autoload_namespaces.php")
                        );
                    } else
                        $paths[] = $path;
                }
            $paths = array_map(function ($path)
            {
                return realpath($path) . DIRECTORY_SEPARATOR;
            }, $paths);

            natsort($paths);
            $this->seen('__include_path',
                implode(PATH_SEPARATOR, array_unique($paths)));
        }

        set_include_path($this->seen('__include_path'));
    }

    /**
     * Attempt to include the path location.
     *
     * @param $path string location of php file on the include path
     *
     * @return bool|mixed returns reference obtained from the include or false
     */
    private function loadFile($path)
    {
        return call_user_func(function () use ($path)
        {
            //ob_start();
            $return = include($path);
            //ob_end_clean();
            return $return;
        });
    }

    /**
     * Attempt to load class with namespace prefixes.
     *
     * @param $className string class name
     *
     * @return bool|mixed reference to discovered include or false
     */
    private function loadPrefixes($className)
    {
        for ($i = 0
            , $file = false
            , $count = count(static::$aliases)
            , $prefixes = array_keys(static::$aliases)
            ; $i < $count
            && false === $file
            && false === $file = $this->discover(
                $variant = "{$prefixes[$i++]}\\$className",
                $className)
            ; $file = $this->loadAliases($variant)
        ) ;

        return $file;
    }

    /**
     * Attempt to load configured aliases based on namespace part of class name.
     *
     * @param $className string fully qualified class name.
     *
     * @return bool|mixed reference to discovered include or false
     */
    private function loadAliases($className)
    {
        $file = false;
        if (preg_match('/(.+)(\\\\\w+$)/U', $className, $parts))
            for ($i = 0
                , $aliases = static::$aliases[$parts[1]] ? : array()
                , $count = count($aliases)
                ; $i < $count
                && false === $file
                ; $file = $this->discover(
                "{$aliases[$i++]}$parts[2]",
                $className)
            ) ;
        return $file;
    }

    /**
     * Create an alias for class.
     *
     * @param $className    string the name of the alias class
     * @param $currentClass string the current class this alias references
     */
    private function alias($className, $currentClass)
    {
        if ($className !=
            $currentClass && false !== strpos($className, $currentClass))
            if (!class_exists($currentClass, false)) {
                class_alias($className, $currentClass);
                $this->seen($currentClass, $className);
            }
    }

    /**
     * Discovery process.
     *
     * @param $className    string class name to discover
     * @param $currentClass string optional name of current class when
     * looking up an alias
     *
     * @return bool|mixed resolved include reference or false
     */
    private function discover($className, $currentClass = null)
    {
        $currentClass = $currentClass ? : $className;
        if (false !== $file = $this->seen($className)) {
            if (!$this->exists($className))
                $file = $this->loadFile($file);

            $this->alias($className, $currentClass);
            return $file;
        }

        /** replace \ with / and _ in CLASS NAME with / = PSR-0 */
        $file = preg_replace("/\\\|_(?=\w+$)/",
            DIRECTORY_SEPARATOR, $className);
        if (false === $file = stream_resolve_include_path("$file.php"))
            return false;
        /*
        // this path normalization is the culprit causing the issue!
        $file = strtr($file,
            array_fill_keys(explode(PATH_SEPARATOR, get_include_path()), ''));
        */

        $counters = array(count(get_declared_interfaces()),
            count(get_declared_classes()));

        if (false !== $result = $this->loadFile($file)) {
            if ($this->exists($className, $file))
                $this->alias($className, $currentClass);
            elseif ($counters[0] < count($autoClass = get_declared_interfaces())
                || $counters[1] < count($autoClass = get_declared_classes())
            ) {
                $file = preg_replace("/\\\|_(?=\w+$)/",
                    DIRECTORY_SEPARATOR, $autoClass = end($autoClass));
                if ($this->exists($autoClass, "$file.php"))
                    $this->alias($autoClass, $currentClass);
            }

            if (!$this->exists($currentClass))
                $result = false;
        }

        return $result;
    }

    /**
     * Checks whether supplied string exists in a loaded class or interface.
     * As a convenience the supplied $mapping can be the value for seen.
     *
     * @param string $className The class or interface to verify
     * @param null   $mapping   optional value for seen
     *
     * @return bool
     */
    private function exists($className, $mapping = null)
    {
        if (class_exists($className, false)
            || interface_exists($className, false))
            if (isset($mapping))
                return $this->seen($className, $mapping);
            else
                return true;
        return false;
    }

    /**
     * Auto loader callback through __invoke object as function.
     *
     * @param $className string class name to auto load
     *
     * @return mixed|null the reference from the include or null
     */
    public function __invoke($className)
    {

        if (false !== $includeReference = $this->discover($className))
            return $includeReference;

        if (false !== $includeReference = $this->loadAliases($className))
            return $includeReference;

        if (false !== $includeReference = $this->loadPrefixes($className))
            return $includeReference;

        $this->seen($className, true);
    }
}

