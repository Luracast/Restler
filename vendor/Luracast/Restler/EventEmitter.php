<?php
namespace Luracast\Restler;
/**
 * Static event broadcasting system for Restler
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc3
 */
use Closure;

class EventEmitter
{
    private $listeners = array();

    public static $self;

    public function __construct() {
        static::$self = $this;
    }

    public static function __callStatic($eventName, $params)
    {
        return call_user_func_array(array(static::$self, $eventName), $params);
    }

    public function __call($eventName, $params)
    {
        if (!@is_array($this->listeners[$eventName]))
            $this->listeners[$eventName] = array();
        $this->listeners[$eventName][] = $params[0];
        return $this;
    }

    public static function listen($eventName, Closure $callback)
    {
        return static::$eventName($callback);
    }

    public function on(array $eventHandlers)
    {
        for (
            $count = count($eventHandlers),
                $events = array_map(
                    'ucfirst',
                    $keys = array_keys(
                        $eventHandlers = array_change_key_case(
                            $eventHandlers,
                            CASE_LOWER
                        )
                    )
                ),
                $i = 0;
            $i < $count;
            call_user_func(
                array($this, "on{$events[$i]}"),
                $eventHandlers[$keys[$i++]]
            )
        );
    }

    /** The attributes are just place holders we need a $sevenName and 0..N parameters */
    protected function trigger($eventName, $params = null)
    {
        $params = func_get_args();
        $eventName = array_shift($params);
        if (isset($this->listeners[$eventName]))
            foreach ($this->listeners[$eventName] as $callback)
                call_user_func_array($callback, $params);
    }

}

