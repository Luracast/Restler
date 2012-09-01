<?php
namespace Luracast\Restler {

/**
 * Class that implements spl_autoload facilities and multiple
 * conventions support.
 * Supports composer libraries and 100% PSR-0 compliant.
 * In addition we enable namespace prefixing and class aliases.
 *
 * @category   Framework
 * @package    restler
 * @subpackage helper
 * @author     Nick Lombard <github@jigsoft.co.za>
 * @copyright  2012 Luracast
 */
class AutoLoader
{
    protected static $instance, // the singleton instance reference
                     $perfectLoaders, // used to keep the ideal list of loaders
                     $rogueLoaders = array(), // other auto loaders now unregistered
                     $classMap = array(), // the class to include file mayying
                     $aliases = array( // aliases and prefixes instead of null list aliases
                         'Luracast\\Restler' => null,
                         'Luracast\\Restler\\Format' => null,
                         'Luracast\\Restler\\Data' => null,
                     );

    /**
     * Singleton instance facility.
     *
     * @static
     * @return AutoLoader the current instance or new instance if none exists.
     */
    public static function instance()
    {
        static::$instance = static::$instance ?: new static();
        return static::thereCanBeOnlyOne();
    }

    /**
     * Other autoLoaders interfere and cause duplicate class loading.
     * AutoLoader is capable enough to handle all standards so no need
     * for others stumbling about.
     *
     * @return callable the one true auto loader.
     */
    public static function thereCanBeOnlyOne() {
        if (static::$perfectLoaders === spl_autoload_functions())
            return static::$instance;

        if (0 < $count = count($loaders = spl_autoload_functions()))
            for ($i = 0, static::$rogueLoaders += $loaders;
                 $i < $count && false != ($loader = $loaders[$i]);
                 $i++)
                if ($loader !== static::$perfectLoaders[0])
                    spl_autoload_unregister($loader);

        return static::$instance;
    }

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
        static::$perfectLoaders = array($this);

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
     * Called from a static context which will not expose the AutoLoader
     * instance itself.
     *
     * @param $path string location of php file on the include path
     *
     * @return bool|mixed returns reference obtained from the include or false
     */
    private static function loadFile($path)
    {
        return \Luracast_Restler_autoloaderInclude($path);
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
                , $aliases = empty(static::$aliases[$parts[1]]) ? : array()
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
     * Load from rogueLoaders as last resort.
     * It may happen that a custom auto loader may load classes in a unique way,
     * these classes cannot be seen otherwise nor should we attempt to cover every
     * possible deviation. If we still can't find a class, as a last resort, we will
     * run through the list of rogue loaders and verify if we succeeded.
     *
     * @param      $className string className that can't be found
     * @param null $loader callable loader optional when the loader is known
     *
     * @return bool false unless className now exists
     */
    private function loadLastResort($className, $loader = null) {
        $loaders = array_unique(static::$rogueLoaders);
        if (isset($loader)) {
            if (false === array_search($loader, $loaders))
                static::$rogueLoaders[] = $loader;
            return $this->loadThisLoader($className, $loader);
        }
        foreach ($loaders as $loader)
            if (false !== $file = $this->loadThisLoader($className, $loader))
                return $file;

        return false;
    }

    /**
     * Helper for loadLastResort.
     * Use loader with $className and see if className exists.
     *
     * @param $className string   name of a class to load
     * @param $loader    callable autoLoader method
     *
     * @return bool false unless className exists
     */
    private function loadThisLoader($className, $loader) {
        if (is_callable($loader)
            && false !== $file = $loader($className)
            && $this->exists($className, $loader))
                return $file;
        return false;
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
                if (is_callable($file))
                    $file = $this->loadLastResort($className, $file);
                elseif($file = stream_resolve_include_path($file))
                    $file = static::loadFile($file);

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

        static::thereCanBeOnlyOne();

        if (false !== $includeReference = $this->loadAliases($className))
            return $includeReference;

        if (false !== $includeReference = $this->loadPrefixes($className))
            return $includeReference;

        if (false !== $includeReference = $this->loadLastResort($className))
            return $includeReference;

        $this->seen($className, true);
    }
}
}

namespace {
    /**
     * Include function in the root namespace to include files optimized
     * for the global context.
     *
     * @param $path string path of php file to include into the global context.
     *
     * @return mixed|bool false if the file could not be included.
     */
    function Luracast_Restler_autoloaderInclude($path) {
        return include $path;
    }
}
