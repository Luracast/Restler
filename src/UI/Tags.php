<?php

namespace Luracast\Restler\UI;

use ArrayAccess;
use Countable;

/**
 * Utility class for generating html tags in an object oriented way
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
class Tags implements ArrayAccess, Countable
{
    public static bool $humanReadable = true;
    public static $initializer = null;
    protected static array $instances = [];
    public string $prefix = '';
    public string $indent = '    ';
    public $tag;
    protected array $attributes = [];
    protected $children = [];
    protected ?self $_parent = null;

    final public function __construct($name = null, array $children = [])
    {
        $this->tag = $name;
        $c = [];
        foreach ($children as $child) {
            is_array($child)
                ? $c = array_merge($c, $child)
                : $c [] = $child;
        }
        $this->markAsChildren($c);
        $this->children = $c;
        if (static::$initializer) {
            call_user_func_array(static::$initializer, [& $this]);
        }
    }

    private function markAsChildren(&$children): void
    {
        foreach ($children as $i => $child) {
            if (is_string($child)) {
                continue;
            }
            if (!is_object($child)) {
                unset($children[$i]);
                continue;
            }
            //echo $child;
            if (isset($child->_parent) && $child->_parent != $this) {
                //remove from current parent
                unset($child->_parent[array_search($child, $child->_parent->children)]);
            }
            $child->_parent = $this;
        }
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
    public static function byId(string $id): ?Tags
    {
        return static::$instances[$id] ?? null;
    }

    /**
     * @param       $name
     * @param array $children
     *
     * @return Tags
     */
    public static function __callStatic($name, array $children): Tags
    {
        return new static($name, $children);
    }

    public function toString(string $prefix = '', string $indent = '    '): string
    {
        $this->prefix = $prefix;
        $this->indent = $indent;
        return $this->__toString();
    }

    public function __toString(): string
    {
        $children = '';
        if (static::$humanReadable) {
            $lineBreak = false;
            foreach ($this->children as $key => $child) {
                $prefix = $this->prefix;
                if (!is_null($this->tag)) {
                    $prefix .= $this->indent;
                }
                if ($child instanceof $this) {
                    $child->prefix = $prefix;
                    $child->indent = $this->indent;
                    $children .= PHP_EOL . $child;
                    $lineBreak = true;
                } else {
                    $children .= $child;
                }
            }
            if ($lineBreak) {
                $children .= PHP_EOL . $this->prefix;
            }
        } else {
            $children = implode('', $this->children);
        }
        if (is_null($this->tag)) {
            return $children;
        }
        $attributes = '';
        foreach ($this->attributes as $attribute => &$value) {
            $attributes .= " $attribute=\"$value\"";
        }

        if (count($this->children)) {
            return static::$humanReadable
                ? "$this->prefix<{$this->tag}{$attributes}>"
                . "$children"
                . "</{$this->tag}>"
                : "<{$this->tag}{$attributes}>$children</{$this->tag}>";
        }

        return "$this->prefix<{$this->tag}{$attributes}/>";
    }

    public function toArray(): array
    {
        $r = [];
        $r['attributes'] = $this->attributes;
        $r['tag'] = $this->tag;
        $children = [];
        foreach ($this->children as $key => $child) {
            $children[$key] = $child instanceof $this
                ? $child->toArray()
                : $child;
        }
        $r['children'] = $children;
        return $r;
    }

    /**
     * Set the id attribute of the current tag
     *
     * @param string|null $value
     *
     * @return string
     */
    public function id(?string $value): string
    {
        if (!empty($value) && is_string($value)) {
            $this->attributes['id'] = $value;
            static::$instances[$value] = $this;
        }
        return $this;
    }

    public function __get($name)
    {
        if ('parent' == $name) {
            return $this->_parent;
        }
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return;
    }

    public function __set($name, $value)
    {
        if ('parent' == $name) {
            if ($this->_parent) {
                unset($this->_parent[array_search($this, $this->_parent->children)]);
            }
            if (!empty($value)) {
                $value[] = $this;
            }
        }
    }

    public function __isset($name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param $attribute
     * @param $value
     *
     * @return Tags|null
     */
    public function __call($attribute, $value): ?Tags
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

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->children[$offset];
        }
        return false;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->children[$offset]);
    }

    public function offsetSet($offset, $value): bool
    {
        if ($offset) {
            $this->children[$offset] = $value;
        } elseif (is_array($value)) {
            $c = [];
            foreach ($value as $child) {
                is_array($child)
                    ? $c = array_merge($c, $child)
                    : $c [] = $child;
            }
            $this->markAsChildren($c);
            $this->children += $c;
        } else {
            $c = [$value];
            $this->markAsChildren($c);
            $this->children[] = $value;
        }
        return true;
    }

    public function offsetUnset($offset): bool
    {
        $this->children[$offset]->_parent = null;
        unset($this->children[$offset]);
        return true;
    }

    public function getContents(): array
    {
        return $this->children;
    }

    public function count(): int
    {
        return count($this->children);
    }
}
