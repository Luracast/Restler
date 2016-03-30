<?php
namespace Luracast\Restler;

/**
 * Class MemcachedCache provides a memcached based cache for Restler
 *
 * @category   Framework
 * @package    Restler
 * @author     Dave Drager <ddrager@gmail.com>
 * @copyright  2014 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 */
class MemcachedCache implements iCache
{
    /**
     * The namespace that all of the cached entries will be stored under.  This allows multiple APIs to run concurrently.
     *
     * @var string
     */
    static public $namespace;
    
    /**
     * @var string the memcache server hostname / IP address. For the memcache 
     * cache method.
     */
    static public $memcachedServer = '127.0.0.1';
    
    /**
     * @var int the memcache server port. For the memcache cache method. 
     */
    static public $memcachedPort = 11211;

    
    private $memcached;

    /**
     * @param string $namespace
     */
    function __construct($namespace = 'restler')
    {
        self::$namespace = $namespace;
        if (extension_loaded('Memcached')) {
            $this->memcache = new \Memcached();
            $this->memcache->addServer(self::$memcachedServer, self::$memcachedPort);
        } else {
            $this->memcachedNotAvailable('Memcached is not available for use as Restler Cache. Please make sure the the memcached php extension is installed.');
        }
    }

    /**
     * store data in the cache
     *
     *
     * @param string $name
     * @param mixed $data
     *
     * @return boolean true if successful
     */
    public function set($name, $data)
    {
        extension_loaded('Memcached') || $this->memcachedNotAvailable();

        try {
            return $this->memcache->set(self::$namespace . "-" . $name, $data);
        } catch
        (\Exception $exception) {
            return false;
        }
    }

    private function memcachedNotAvailable($message = 'Memcached is not available.')
    {
        throw new \Exception($message);
    }

    /**
     * retrieve data from the cache
     *
     *
     * @param string $name
     * @param bool $ignoreErrors
     *
     * @throws \Exception
     * @return mixed
     */
    public function get($name, $ignoreErrors = false)
    {
        extension_loaded('Memcached') || $this->memcachedNotAvailable();

        try {
            return $this->memcache->get(self::$namespace . "-" . $name);
        } catch (\Exception $exception) {
            if (!$ignoreErrors) {
                throw $exception;
            }
            return null;
        }
    }

    /**
     * delete data from the cache
     *
     *
     * @param string $name
     * @param bool $ignoreErrors
     *
     * @throws \Exception
     * @return boolean true if successful
     */
    public function clear($name, $ignoreErrors = false)
    {
        extension_loaded('Memcached') || $this->memcachedNotAvailable();

        try {
            $this->memcache->delete(self::$namespace . "-" . $name);
        } catch (\Exception $exception) {
            if (!$ignoreErrors) {
                throw $exception;
            }
        }
    }

    /**
     * check if the given name is cached
     *
     *
     * @param string $name
     *
     * @return boolean true if cached
     */
    public function isCached($name)
    {
        extension_loaded('Memcached') || $this->memcachedNotAvailable();
        $data = $this->memcache->get(self::$namespace . "-" . $name);
        return !empty($data);
    }

}
