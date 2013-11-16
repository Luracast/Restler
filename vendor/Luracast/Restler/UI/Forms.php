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
    /**
     * @var bool should we fill up the form using given data?
     */
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
    private static $key = null;

    /**
     * Get the form
     *
     * @param string $method   http method to submit the form
     * @param string $action   relative path from the web root. When set to null
     *                         it uses the current api method's path
     * @param bool   $dataOnly if you want to render the form yourself use this
     *                         option
     * @param string $prefix   used for adjusting the spacing in front of
     *                         form elements
     * @param string $indent   used for adjusting indentation
     *
     * @return array|T
     *
     * @throws \Luracast\Restler\RestException
     */
    public static function get($method = 'POST', $action = null, $dataOnly = false, $prefix = '', $indent = '    ')
    {
        if (!static::$style)
            static::$style = FormStyles::$html;

        try {
            if (is_null($action))
                $action = Util::$restler->url;

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

        } catch (RestException $e) {
            //echo $e->getErrorMessage();
            $info = false;
        }
        if (!$info)
            throw new RestException(500, 'invalid action path for form');

        $m = $info->metadata;
        $r = static::fields($m['param'], $info->parameters, $dataOnly);
        if ($method != 'GET' && $method != 'POST') {
            if (empty(Defaults::$httpMethodOverrideProperty))
                throw new RestException(
                    500,
                    'Forms require `Defaults::\$httpMethodOverrideProperty`' .
                    "for supporting HTTP $method"
                );
            if ($dataOnly) {
                $r[] = array(
                    'tag' => 'input',
                    'name' => Defaults::$httpMethodOverrideProperty,
                    'type' => 'hidden',
                    'value' => 'method',
                );
            } else {
                $r[] = T::input()
                    ->name(Defaults::$httpMethodOverrideProperty)
                    ->value($method)
                    ->type('hidden');
            }

            $method = 'POST';
        }
        if (session_id() != '') {
            static::generateKey();
            if ($dataOnly) {
                $r[] = array(
                    'tag' => 'input',
                    'name' => 'form_key',
                    'type' => 'hidden',
                    'value' => 'hidden',
                );
            } else {
                $key = T::input()
                    ->name('form_key')
                    ->type('hidden')
                    ->value(static::$key);
                $r[] = $key;
            }
        }

        $s = array(
            'tag' => 'button',
            'type' => 'submit',
            'label' =>
                Util::nestedValue($m, CommentParser::$embeddedDataName, 'submit')
                    ? : 'Submit'
        );

        if (!$dataOnly)
            $s = Emmet::make(static::$style['submit'], $s);
        $r[] = $s;
        $t = array(
            'action' => Util::$restler->getBaseUrl() . '/' . rtrim($action, '/'),
            'method' => $method,
        );
        if (static::$fileUpload) {
            static::$fileUpload = false;
            $t['enctype'] = 'multipart/form-data';
        }
        if (!$dataOnly) {
            $t = Emmet::make(static::$style['form'], $t);
            $t->prefix = $prefix;
            $t->indent = $indent;
            $t[] = $r;
        } else {
            $t['fields'] = $r;
        }
        return $t;
    }

    public static function fields(array $params, array $values, $dataOnly = false)
    {
        $r = array();
        foreach ($params as $k => $p) {
            $value = Util::nestedValue($values, $k);
            if (is_scalar($value))
                $p['value'] = $value;
            static::$validationInfo = $v = new ValidationInfo($p);
            if ($v->from == 'path')
                continue;
            $f = static::field($v, $dataOnly);
            $r [] = $f;
            static::$validationInfo = null;
        }
        return $r;
    }

    /**
     * @param ValidationInfo $p
     *
     * @param bool           $dataOnly
     *
     * @return array|T
     */
    public static function field(ValidationInfo $p, $dataOnly = false)
    {
        $type = $p->field ? : static::guessFieldType($p);
        $tag = in_array($type, static::$inputTypes)
            ? 'input' : $type;
        $options = array();
        if ($p->choice) {
            foreach ($p->choice as $i => $choice) {
                $option = array('name' => $p->name, 'value' => $choice);
                $option['text'] = isset($p->rules['select'][$i])
                    ? $p->rules['select'][$i]
                    : $choice;
                if ($choice == $p->value)
                    $option['selected'] = true;
                $options[] = $option;
            }
        }
        if ($type == 'radio' && empty($options)) {
            $options[] = array('name' => $p->name, 'text' => ' Yes ',
                'value' => 'true');
            $options[] = array('name' => $p->name, 'text' => ' No ',
                'value' => 'false');
            if ($p->value || $p->default)
                $options[0]['selected'] = true;
        } elseif ($type == 'file') {
            static::$fileUpload = true;
        }
        $r = array(
            'tag' => $tag,
            'name' => $p->name,
            'type' => $type,
            'label' => $p->label ? : static::title($p->name),
            'value' => $p->value,
            'default' => $p->default,
            'options' => $options,
        );
        if ($p->required)
            $r['required'] = true;
        if (isset($p->rules['autofocus']))
            $r['autofocus'] = true;
        /*
        echo "<pre>";
        print_r($r);
        echo "</pre>";
        */
        if ($dataOnly)
            return $r;
        $t = Emmet::make(
            isset(static::$style[$type])
                ? static::$style[$type]
                : static::$style[$tag], $r
        );
        return $t;
    }

    protected static function guessFieldType(ValidationInfo $p)
    {
        if (in_array($p->type, static::$inputTypes))
            return $p->type;
        if ($p->choice)
            return 'select';
        switch ($p->type) {
            case 'boolean':
                return 'radio';
            case 'int':
            case 'number':
            case 'float':
                return 'number';
            case 'array':
                if ($p->choice)
                    return 'checkbox';
        }
        if ($p->name == 'password')
            return 'password';
        return 'text';
    }

    protected static function title($name)
    {
        return ucfirst(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name));
    }

    protected static function generateKey()
    {
        if (!static::$key)
            static::$key = md5(User::getIpAddress() . uniqid(mt_rand(), true));
        $_SESSION['form_key'] = static::$key;
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
