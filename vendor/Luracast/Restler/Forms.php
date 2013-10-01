<?php
namespace Luracast\Restler;

use Luracast\Restler\Data\ValidationInfo;

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
        $childIndent = $prefix . $indent;
        $m = $info->metadata;
        $r = $prefix . '<form method="' . $method . '" action="'
            . Util::$restler->getBaseUrl()
            . '/' . rtrim($action, '/') . '">' . PHP_EOL;
        $r .= static::fields($m['param'], $info->parameters, $childIndent);
        $r .= $childIndent . '<input type="submit"></input>' . PHP_EOL;
        $r .= $prefix . '</form>' . PHP_EOL;
        echo $r;
    }

    public static function fields(array $params, array $values, $indent = "    ")
    {
        $r = '';
        foreach ($params as $k => $p) {
            $r .= $indent
                . static::field(
                    new ValidationInfo($p), Util::nestedValue($values, $k)
                )
                . PHP_EOL;
        }

        return $r;
    }

    public static function field(ValidationInfo $p, $value = null)
    {
        $r = '<' . static::$fieldWrapper . '>' . static::title($p->name)
            . '<input name="' . $p->name . '"';
        if (!is_null($value))
            $r .= ' value="' . $value . '"';
        if ($p->required)
            $r .= ' required';
        if ($p->default)
            $r .= ' placeholder="' . $p->default . '"';
        $r .= '></input>'
            . '</' . static::$fieldWrapper . '>';
        return $r;
    }

    protected static function title($name)
    {
        return ucfirst(preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $name));
    }

} 