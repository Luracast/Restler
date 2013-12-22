<?php
namespace Luracast\Restler;

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
 */
class Tag
{
    public static $humanReadable = true;
    public $prefix = '';
    public $indent = '    ';
    protected $attributes = array();
    protected $children = array();
    protected $name;

    public function __construct($name, array $children)
    {
        $this->name = $name;
        $this->children = $children;
    }

    public function toString($prefix = '', $indent = '    ')
    {
        $this->prefix = $prefix;
        $this->indent = $indent;
        return $this->__toString();
    }

    public static function __callStatic($name, array $children)
    {
        return new static($name, $children);
    }

    public function __call($attribute, $value)
    {
        $this->attributes[$attribute] = $value[0];
        return $this;
    }

    public function __toString()
    {
        $children = '';
        if (static::$humanReadable) {
            $lineBreak = false;
            foreach ($this->children as $key => $child) {
                if ($child instanceof $this) {
                    $child->prefix = $this->prefix . $this->indent;
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
        $attributes = '';
        foreach ($this->attributes as $attribute => &$value)
            $attributes .= " $attribute=\"$value\"";

        if (count($this->children))
            return static::$humanReadable
                ? "$this->prefix<{$this->name}{$attributes}>"
                . "$children"
                . "</{$this->name}>"
                : "<{$this->name}{$attributes}>$children</{$this->name}>";

        return "<{$this->name}{$attributes} />";
    }
} 