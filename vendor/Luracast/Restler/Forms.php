<?php
namespace Luracast\Restler;

use Luracast\Restler\Data\ValidationInfo;
use Luracast\Restler\Tags as T;

/**
 * Utility class for automatically generating forms for the given http method
 * and api url
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 */
class Forms
{
    protected static $style;

    protected static $inputTypes = array(
        'password',
        'button',
        'image',
        'file',
        'reset',
        'submit',
        'search',
        'checkbox',
        'radio',
        'email',
        'text',
        'color',
        'date',
        'datetime',
        'datetime-local',
        'email',
        'month',
        'number',
        'range',
        'search',
        'tel',
        'time',
        'url',
        'week',
    );

    /**
     * @var ValidationInfo
     */
    private static $validationInfo = null;
    private static $presets = array();

    /**
     * Get the form
     *
     * @param string $method http method to submit the form
     * @param string $action relative path from the web root. When set to null
     *                       it uses the current api method's path
     * @param string $prefix used for adjusting the spacing in front of
     *                       form elements
     * @param string $indent used for adjusting indentation
     *
     * @throws RestException
     */
    public static function get($method = 'POST', $action = null, $prefix = '', $indent = '    ')
    {
        if (!static::$style) {
            static::$style = FormStyles::$html5;
        }
        try {
            $info = is_null($action)
                ? Util::$restler->apiMethodInfo
                : Routes::find(
                    'v' . Util::$restler->getRequestedApiVersion()
                    . (empty($action) ? '' : "/$action"),
                    $method,
                    Util::$restler->requestMethod == $method
                    && Util::$restler->url == $action
                        ? Util::$restler->getRequestData()
                        : array()
                );
        } catch (RestException $e) {
            echo $e->getErrorMessage();
            $info = false;
        }
        if (!$info)
            throw new RestException(500, 'invalid action path for form');

        $oldInitializer = T::$initializer;
        T::$initializer = __CLASS__ . '::' . 'tagInit';
        $m = $info->metadata;
        $r = static::fields($m['param'], $info->parameters);
        $s = T::input()->type('submit');
        $with = Util::nestedValue(static::$style, 'wrapper');
        if (is_array($with)) {
            $s = static::wrap($s, $with);
        }
        $r [] = $s;
        $t = T::form($r)
            ->action(Util::$restler->getBaseUrl() . '/' . rtrim($action, '/'))
            ->method($method);
        $t->prefix = $prefix;
        $t->indent = $indent;
        T::$initializer = $oldInitializer;
        return $t;
    }

    public static function fields(array $params, array $values)
    {
        $r = array();
        foreach ($params as $k => $p) {
            $p['value'] = Util::nestedValue($values, $k);
            static::$validationInfo = $v = new ValidationInfo($p);
            $r [] = static::field($v);
            static::$validationInfo = null;
        }
        return $r;
    }

    public static function field(ValidationInfo $p)
    {
        if ($p->choice) {
            if ($p->field == 'radio') {
                $a = array();
                $with = Util::nestedValue(static::$style, 'radio')
                    ? : array('label');
                $wrapFirst = $with[0] == 'label';
                foreach ($p->choice as $option) {
                    if ($option == $p->value) {
                        static::$presets = array('checked' => true);
                    }
                    $t = T::input()->type('radio')->value($option);

                    $a[] = static::wrap($t, $with, $option, false, $wrapFirst);
                }
                $t = $a;
            } else {
                $options = array();
                foreach ($p->choice as $option) {
                    if ($option == $p->value) {
                        static::$presets = array('selected' => true);
                    }
                    $options[] = T::option($option);
                }
                $t = T::select($options);
            }
        } elseif ($p->field == 'textarea') {
            $t = T::textarea($value ? $value : "\r");
        } elseif (in_array($p->field, static::$inputTypes)) {
            $t = T::input();
            $t->type($p->field);
            if ($p->field == 'checkbox') {
                $t->value('true');
                if ($p->value) {
                    $t->checked(true);
                }
            } elseif ($p->field == 'number') {
                $t->step($p->type == 'float' || $p->type == 'number' ? 0.1 : 1);
            } elseif ($p->field == 'radio') {
                $a = array();
                $with = Util::nestedValue(static::$style, 'radio')
                    ? : array('label');
                $wrapFirst = $with[0] == 'label';
                if ($p->type == 'bool' || $p->type == 'boolean') {
                    $t->value('true');
                    if ($p->value)
                        $t->checked(true);
                    $a[] = static::wrap($t, $with, 'Yes', false, $wrapFirst);
                    $t = T::input()->type('radio')->value('false');
                    if (!$p->value)
                        $t->checked(true);
                    $a[] = static::wrap($t, $with, 'No', false, $wrapFirst);
                }
                $t = $a;
            }
        } elseif ($p->field) {
            $t = call_user_func('Luracast\Restler\Tags::' . $p->field);
        } else {
            $t = T::input();
            if (in_array($p->type, static::$inputTypes)) {
                $t->type($p->type);
            } elseif ($t->name == 'password') {
                $t->type('password');
            } elseif ($p->type == 'bool' || $p->type == 'boolean') {
                $t->type('checkbox');
                $t->value('true');
                if ($p->value) {
                    $t->checked(true);
                }
            } elseif ($p->type == 'int' || $p->type == 'integer') {
                $t->type('number');
                $t->step(1);
            } elseif ($p->type == 'float' || $p->type == 'number') {
                $t->type('number');
                $t->step(0.1);
            } else {
                $t->type('text');
            }
        }
        if (isset(static::$style['wrapper'])) {
            $text = static::$validationInfo->label
                ? : static::title(static::$validationInfo->name);
            $t = static::wrap($t, static::$style['wrapper'], $text);
        }
        return $t;
    }

    /**
     * @param Tags|array $t
     * @param array      $with       an array of strings
     * @param string     $text       label text
     * @param bool       $prefixText text to prefix or suffix fields
     * @param bool       $wrapFirst
     *
     * @return array|Tags
     */
    public static function wrap($t, array $with, $text = '', $prefixText = true, $wrapFirst = false)
    {
        foreach ($with as $i => $wrapper) {
            if ($i == 0) {
                if ($wrapFirst) {
                    if ($prefixText) {
                        $t = call_user_func('Luracast\Restler\Tags::' . $wrapper, $text, $t);
                    } else { //text last
                        $t = call_user_func('Luracast\Restler\Tags::' . $wrapper, $t, $text);
                    }
                } else {
                    $w = call_user_func('Luracast\Restler\Tags::' . $wrapper, $text);
                    if ($prefixText) {
                        $t = is_array($t) ? array_merge(array($w), $t) : array($w, $t);
                    } else { //text last
                        $t = is_array($t) ? array_merge($t, array($w)) : array($t, $w);
                    }
                }
            } else {
                $t = call_user_func(
                    'Luracast\Restler\Tags::' . $wrapper,
                    $t
                );
            }
        }
        return $t;
    }

    public static function tagInit(T & $t)
    {
        $presets = static::$style['*']
            + static::$presets
            + (Util::nestedValue(static::$style, $t->tag) ? : array());
        foreach ($presets as $k => $v) {
            if ($v{0} == '$') {
                //variable substitution
                $v = Util::nestedValue(static::$validationInfo, substr($v, 1));
            }
            if (!is_null($v))
                $t->{$k}($v);
        }
        //reset custom presets
        static::$presets = array();
        return $t;
    }

    protected static function title($name)
    {
        return ucfirst(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name));
    }

} 