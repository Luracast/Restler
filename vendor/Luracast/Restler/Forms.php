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
                        : null
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
        $t = T::input()->name($p->name);
        if (!is_null($value))
            $t->value($value);
        if ($p->required)
            $t->required(true);
        if ($p->default)
            $t->placeholder($p->default);
        if (static::$fieldWrapper) {
            $t = call_user_func(
                'Luracast\Restler\Tag::' . static::$fieldWrapper,
                static::title($p->name),
                $t
            );
        }
        return $t;
    }

    protected static function title($name)
    {
        return ucfirst(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name));
    }

} 