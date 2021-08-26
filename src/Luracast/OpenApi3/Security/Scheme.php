<?php


namespace Luracast\Restler\OpenApi3\Security;


abstract class Scheme
{
    public const TYPE_API_KEY = 'apiKey';
    public const TYPE_HTTP = 'http';
    public const TYPE_OAUTH2 = 'oauth2';
    public const TYPE_OPEN_ID_CONNECT = 'openIdConnect';

    public const HTTP_SCHEME_BASIC = 'basic';
    public const HTTP_SCHEME_BEARER = 'bearer';

    protected $type;
    protected $description;

    public function toArray(string $basePath = '/')
    {
        $result = array_filter(get_object_vars($this));
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (is_object($v)) {
                        $result[$key][$k] = $v->toArray($basePath);
                    }
                }
            } elseif (is_object($value)) {
                $result[$key] = $value->toArray($basePath);
            }
        }
        return $result;
    }
}
