<?php
namespace Luracast\Restler\UI;

use ArrayAccess;
use Luracast\Restler\Util;

/**
 * Utility class for generating html tags in an object oriented way
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 *
 * ============================ magic  properties ==============================
 * @property Tags parent parent tag
 * ============================== magic  methods ===============================
 * @method Tags name(string $value) name attribute
 * @method Tags action(string $value) action attribute
 * @method Tags placeholder(string $value) placeholder attribute
 * @method Tags value(string $value) value attribute
 * @method Tags required(boolean $value) required attribute
 * @method Tags class(string $value) required attribute
 *
 * =========================== static magic methods ============================
 * @method static Tags form() creates a html form
 * @method static Tags input() creates a html input element
 * @method static Tags button() creates a html button element
 *
 */
class Tags implements ArrayAccess
{
    public static $humanReadable = true;
    public static $initializer = null;
    protected static $instances = array();
    public $prefix = '';
    public $indent = '    ';
    public $tag;
    protected $attributes = array();
    protected $children = array();
    protected $_parent;

    public function __construct($name = null, array $children = array())
    {
        $this->tag = $name;
        $c = array();
        foreach ($children as $child) {
            is_array($child)
                ? $c = array_merge($c, $child)
                : $c [] = $child;
        }
        $this->children = $c;
        foreach ($this->children as $child) {
            if ($child->_parent) {
                //remove from current parent
                unset($child->_parent[array_search($child, $child->_parent->children)]);
            }
            $child->_parent = $this;
        }
        if (static::$initializer)
            call_user_func_array(static::$initializer, array(& $this));
    }

    /**
     * Get Tag by id
     *
     * Retrieve a tag by its id attribute
     *
     * @param string $id
     *
     * @return Tags|null
     */
    public static function byId($id)
    {
        return Util::nestedValue(static::$instances, $id);
    }

    /**
     * @param       $name
     * @param array $children
     *
     * @return Tags
     */
    public static function __callStatic($name, array $children)
    {
        return new static($name, $children);
    }

    public function toString($prefix = '', $indent = '    ')
    {
        $this->prefix = $prefix;
        $this->indent = $indent;
        return $this->__toString();
    }

    public function __toString()
    {
        $children = '';
        if (static::$humanReadable) {
            $lineBreak = false;
            foreach ($this->children as $key => $child) {
                $prefix = $this->prefix;
                if (!is_null($this->tag))
                    $prefix .= $this->indent;
                if ($child instanceof $this) {
                    $child->prefix = $prefix;
                    $child->indent = $this->indent;
                    $children .= PHP_EOL . $child;
                    $lineBreak = true;
                } else {
                    $children .= $child;
                }
            }
            if ($lineBreak)
                $children .= PHP_EOL . $this->prefix;
        } else {
            $children = implode('', $this->children);
        }
        if (is_null($this->tag))
            return $children;
        $attributes = '';
        foreach ($this->attributes as $attribute => &$value)
            $attributes .= " $attribute=\"$value\"";

        if (count($this->children))
            return static::$humanReadable
                ? "$this->prefix<{$this->tag}{$attributes}>"
                . "$children"
                . "</{$this->tag}>"
                : "<{$this->tag}{$attributes}>$children</{$this->tag}>";

        return "$this->prefix<{$this->tag}{$attributes}/>";
    }

    /**
     * Set the id attribute of the current tag
     *
     * @param string $value
     *
     * @return string
     */
    public function id($value)
    {
        $this->attributes['id'] = isset($value)
            ? (string)$value
            : Util::nestedValue($this->attributes, 'name');
        static::$instances[$value] = $this;
        return $this;
    }

    public function __get($name)
    {
        if ('parent' == $name)
            return $this->_parent;
        if (isset($this->attributes[$name]))
            return $this->attributes[$name];
        return;
    }

    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param $attribute
     * @param $value
     *
     * @return Tags
     */
    public function __call($attribute, $value)
    {
        if (is_null($value)) {
            return isset($this->attributes[$attribute])
                ? $this->attributes[$attribute]
                : null;
        }
        $value = $value[0];
        if (is_null($value)) {
            unset($this->attributes[$attribute]);
            return $this;
        }
        $this->attributes[$attribute] = is_bool($value)
            ? ($value ? 'true' : 'false')
            : @(string)$value;
        return $this;
    }

    public function offsetGet($index)
    {
        if ($this->offsetExists($index)) {
            return $this->children[$index];
        }
        return false;
    }

    public function offsetExists($index)
    {
        return isset($this->children[$index]);
    }

    public function offsetSet($index, $value)
    {
        if ($index) {
            $this->children[$index] = $value;
        } else {
            $this->children[] = $value;
        }
        $value->parent = $this;
        return true;

    }

    public function offsetUnset($index)
    {
        $this->children[$index]->parent = null;
        unset($this->children[$index]);
        return true;
    }

    public function getContents()
    {
        return $this->children;
    }
}