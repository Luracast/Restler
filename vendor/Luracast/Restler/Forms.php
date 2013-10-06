<?php
namespace Luracast\Restler;

use Luracast\Restler\Data\ValidationInfo;
use Luracast\Restler\Tags as T;

/**
 * Utility class to build html forms
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
    protected static $fieldPresets = array(
        '*' => array(
            'value' => '$value',
            'required' => '$required',
            'name' => '$name',
        ),
        'input' => array(
            'placeholder' => '$default',
            'pattern' => '$pattern',
            'class' => 'input-small',
            'min' => '$min',
            'max' => '$max',
        )
    );

    protected static $inputTypes = array(
        'password',
        'button',
        'image',
        'file',
        'reset',
        'submit',
        'search',
        'checkbox',
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

    public static $fieldWrapper = 'label';

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

        $m = $info->metadata;
        $r = static::fields($m['param'], $info->parameters);
        $r [] = T::input()->type('submit');

        return T::form($r)
            ->action(Util::$restler->getBaseUrl() . '/' . rtrim($action, '/'))
            ->method($method)
            ->toString($prefix, $indent);
    }

    public static function fields(array $params, array $values)
    {
        $r = array();
        foreach ($params as $k => $p) {
            $r [] = static::field(
                new ValidationInfo($p), Util::nestedValue($values, $k)
            );
        }
        return $r;
    }

    public static function field(ValidationInfo $p, $value = null)
    {
        if ($p->choice) {
            $options = array();
            foreach ($p->choice as $option) {
                $options[] = T::option($option);
            }
            $t = static::initTag(T::select($options), $p);
        } elseif ($p->min && $p->min > 50 || $p->max && $p->max > 50) {
            $t = static::initTag(T::textarea("\r"), $p);
        } else {
            $t = static::initTag(T::input(), $p);
            if (in_array($p->type, static::$inputTypes)) {
                $t->type($p->type);
            } elseif ($t->name == 'password') {
                $t->type('password');
            } elseif ($p->type == 'bool' || $p->type == 'boolean') {
                $t->type('checkbox');
                $t->value('true');
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
        if (static::$fieldWrapper) {
            $t = call_user_func(
                'Luracast\Restler\Tags::' . static::$fieldWrapper,
                static::title($p->name),
                $t
            );
        }
        return $t;
    }

    protected static function initTag(Tags $t, ValidationInfo $p, $customPresets = array())
    {
        $presets = static::$fieldPresets['*']
            + $customPresets
            + (Util::nestedValue(static::$fieldPresets, $t->tag) ? : array());
        foreach ($presets as $k => $v) {
            if ($v{0} == '$') {
                //variable substitution
                $v = Util::nestedValue($p, substr($v, 1));
            }
            if (!is_null($v))
                $t->{$k}($v);
        }
        return $t;
    }

    protected static function title($name)
    {
        return ucfirst(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name));
    }

} 