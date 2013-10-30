<?php
namespace Luracast\Restler\UI;

use Luracast\Restler\CommentParser;
use Luracast\Restler\Data\ValidationInfo;
use Luracast\Restler\Defaults;
use Luracast\Restler\iFilter;
use Luracast\Restler\RestException;
use Luracast\Restler\Routes;
use Luracast\Restler\UI\Tags as T;
use Luracast\Restler\User;
use Luracast\Restler\Util;


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
class Forms implements iFilter
{
    public static $style;
    public static $preFill = true;
    /**
     * @var ValidationInfo
     */
    public static $validationInfo = null;
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
    protected static $fileUpload = false;
    private static $presets = array();
    private static $key = null;

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

        if (isset(Util::$restler) && Util::$restler->getProductionMode()) {
            $name = 'form_' . strtolower($method) . '_' . str_replace('/', '_', $action);
            if (Util::$restler->cache->isCached($name)) {
                $txt = Util::$restler->cache->get($name);
                if (session_id() != '') {
                    static::generateKey();
                    $txt = str_replace('{form_key}', static::$key, $txt);
                }
            }
        }

        if (!static::$style) {
            static::$style = FormStyles::$html5;
        }
        try {
            if (is_null($action)) {
                $action = Util::$restler->url;
                $info = Util::$restler->apiMethodInfo;
            } else {
                $info = Util::$restler->url == $action
                && Util::getRequestMethod() == $method
                    ? Util::$restler->apiMethodInfo
                    : Routes::find(
                        'v' . Util::$restler->getRequestedApiVersion()
                        . (empty($action) ? '' : "/$action"),
                        $method,
                        static::$preFill ||
                        (Util::$restler->requestMethod == $method &&
                            Util::$restler->url == $action)
                            ? Util::$restler->getRequestData()
                            : array()
                    );
            }
        } catch (RestException $e) {
            //echo $e->getErrorMessage();
            $info = false;
        }
        if (!$info)
            throw new RestException(500, 'invalid action path for form');

        $oldInitializer = T::$initializer;
        T::$initializer = __CLASS__ . '::' . 'tagInit';
        $m = $info->metadata;
        $r = static::fields($m['param'], $info->parameters);
        if ($method != 'GET' && $method != 'POST') {
            if (empty(Defaults::$httpMethodOverrideProperty))
                throw new RestException(
                    500,
                    'Forms require `Defaults::\$httpMethodOverrideProperty`' .
                    "for supporting HTTP $method"
                );
            $r[] = T::input()
                ->name(Defaults::$httpMethodOverrideProperty)
                ->value($method)
                ->type('hidden')
                ->class(null);

            $method = 'POST';
        }
        if (session_id() != '') {
            static::generateKey();
            $key = T::input()
                ->name('form_key')
                ->type('hidden')
                ->value(static::$key)
                ->class(null);
            $r[] = & $key;
        }
        $s = T::button(
            Util::nestedValue($m, CommentParser::$embeddedDataName, 'submit')
                ? : 'Submit'
        )->type('submit');
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
        if (isset($name)) {
            if (isset($key))
                $key->value('{form_key}');
            Util::$restler->cache->set($name, (string)$t);
            if (isset($key))
                $key->value(static::$key);

        }
        if (static::$fileUpload) {
            static::$fileUpload = false;
            $t->enctype('multipart/form-data');
        }
        return $t;
    }

    protected static function generateKey()
    {
        if (!static::$key)
            static::$key = md5(User::getIpAddress() . uniqid(mt_rand(), true));
        $_SESSION['form_key'] = static::$key;
    }

    public static function fields(array $params, array $values)
    {
        $r = array();
        foreach ($params as $k => $p) {
            $p['value'] = Util::nestedValue($values, $k);
            static::$validationInfo = $v = new ValidationInfo($p);
            if ($v->from == 'path')
                continue;
            $f = static::field($v);
            is_array($f) ? $r = array_merge($r, $f) : $r [] = $f;
            static::$validationInfo = null;
        }
        return $r;
    }

    /**
     * @param ValidationInfo $p
     *
     * @return array|Tags
     */
    public static function field(ValidationInfo $p)
    {
        $outerWrapper = null;
        if ($p->choice) {
            if ($p->field == 'radio') {
                $a = array();
                $with = Util::nestedValue(static::$style, 'radio', 'wrapper')
                    ? : array('label');
                $wrapFirst = reset($with) == 'label' || key($with) == 'label';
                $outerWrapper = Util::nestedValue(static::$style, 'radio', 'outerWrapper');
                foreach ($p->choice as $i => $option) {
                    if ($option == $p->value) {
                        static::$presets = array('checked' => true);
                    }
                    if ($style = Util::nestedValue(static::$style, 'radio', 'style'))
                        static::$presets = is_array(static::$presets)
                            ? array_merge(static::$presets, $style)
                            : $style;
                    $t = T::input()->type('radio')->value($option);
                    $a[] = static::wrap(
                        $t,
                        $with,
                        Util::nestedValue($p->rules, 'select', $i) ? : $option,
                        false,
                        $wrapFirst
                    );
                }
                $t = $a;
            } else {
                $options = array(T::option('')->value(''));
                foreach ($p->choice as $i => $option) {
                    if ($option == $p->value) {
                        static::$presets = array('selected' => true);
                    }
                    $options[] = T::option(
                        Util::nestedValue($p->rules, 'select', $i) ? : $option
                    )->value($option);
                }
                $t = T::select($options);
            }
        } elseif ($p->field == 'textarea') {
            $t = T::textarea($p->value ? $p->value : "\r");
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
                $with = Util::nestedValue(static::$style, 'radio', 'wrapper')
                    ? : array('label');
                $wrapFirst = reset($with) == 'label' || key($with) == 'label';
                $outerWrapper = Util::nestedValue(static::$style, 'radio', 'outerWrapper');
                if ($p->type == 'bool' || $p->type == 'boolean') {
                    if ($style = Util::nestedValue(static::$style, 'radio', 'style'))
                        static::$presets = $style;
                    $t = T::input()->type('radio')->value('true');
                    if ($p->value)
                        $t->checked(true);
                    $a[] = static::wrap($t, $with, ' Yes', false, $wrapFirst);
                    if ($style)
                        static::$presets = $style;
                    $t = T::input()->type('radio')->value('false');
                    if (!$p->value)
                        $t->checked(true);
                    $a[] = static::wrap($t, $with, ' No', false, $wrapFirst);
                }
                $t = $a;
            }
        } elseif ($p->field) {
            $t = new T($p->field);
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
        if (isset($t->type)) {
            if ('password' == $t->type) {
                //remove value from password fields
                $t->value(null);
            } elseif ('file' == $t->type) {
                //set enc type properly
                static::$fileUpload = true;
            }
        }
        $wrapFirst = false;
        if (is_array($outerWrapper)) {
            $wrapFirst = true;
        } elseif (false !== $outerWrapper && isset(static::$style['wrapper'])) {
            $outerWrapper = static::$style['wrapper'];
        }
        if (is_array($outerWrapper)) {
            $text = static::$validationInfo->label
                ? : static::title(static::$validationInfo->name);
            $t = static::wrap($t, $outerWrapper, " $text ", true, $wrapFirst);
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
        $counter = 0;
        $T = 'Luracast\Restler\UI\Tags::';
        foreach ($with as $i => $wrapper) {
            if (is_string($i)) {
                static::$presets = $wrapper;
                $wrapper = $i;
                $i = $counter;
            }
            if ($i == 0) {
                if ($wrapFirst) {
                    if ($prefixText) {
                        $t = new T($wrapper, array($text, $t));
                    } else { //text last
                        $t = new T($wrapper, array($t, $text));
                    }
                } else {
                    $w = new T($wrapper, array($text));
                    if ($prefixText) {
                        $t = is_array($t) ? array_merge(array($w), $t) : array($w, $t);
                    } else { //text last
                        $t = is_array($t) ? array_merge($t, array($w)) : array($t, $w);
                    }
                }
            } else {
                $t = new T($wrapper, array($t));
            }
            $counter++;
        }
        return $t;
    }

    protected static function title($name)
    {
        return ucfirst(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name));
    }

    public static function tagInit(T & $t)
    {
        $presets = static::$style['*']
            + static::$presets
            + (Util::nestedValue(static::$style, $t->tag) ? : array());
        foreach ($presets as $k => $v) {
            if ($v{0} == '$') {
                //variable substitution
                $var = substr($v, 1);
                $v = Util::nestedValue(static::$validationInfo, $var);
                if (is_null($v)) {
                    $v = Util::nestedValue(static::$validationInfo, 'rules', $var);
                }
            }
            if (!is_null($v))
                $t->{$k}($v);
        }
        //reset custom presets
        static::$presets = array();
        return $t;
    }

    /**
     * Access verification method.
     *
     * API access will be denied when this method returns false
     *
     * @return boolean true when api access is allowed false otherwise
     *
     * @throws RestException 403 security violation
     */
    public function __isAllowed()
    {
        if (session_id() == '') {
            session_start();
        }
        if (!empty($_POST)) {
            if (isset($_POST['form_key'])
                && isset($_SESSION['form_key'])
                && $_POST['form_key'] == $_SESSION['form_key']
            ) {
                return true;
            }
            throw new RestException(403, 'Insecure form submission');
        }
        return true;
    }
}
