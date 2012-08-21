<?php
namespace Luracast\Restler;
/**
 * Static event broadcasting system for Restler
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0
 */
class Events
{
    protected static $_listeners = array();

    public static function listen($eventName, \Closure $callback,
                                  $eventSourceClassName = null)
    {
        self::$_listeners[$eventName][] = array('callback' => $callback,
            'source' => $eventSourceClassName);
    }

    public static function trigger($eventName, array $arguments, $fromClassName='')
    {
        if (isset(self::$_listeners[$eventName])) {
            foreach (self::$_listeners[$eventName] as $listener) {
                $source = $listener['source'];
                $callback = $listener['callback'];
                if (!isset($source)
                    || strpos($fromClassName, $source)
                        == strlen($fromClassName) -strlen($source)
                ) {
                    call_user_func_array($callback, $arguments);
                }
            }
        }
    }

    public static function observe(iObserve $observer, $subjectClassName='')
    {
        return class_implements($observer);
    }

}
