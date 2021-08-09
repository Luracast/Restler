<?php


namespace Luracast\Restler\OpenApi3\Security;


use Luracast\Restler\Utils\Text;

abstract class OAuth2Flow
{
    protected array $scopes = [];
    protected $refreshUrl;

    public function toArray(string $basePath = '/')
    {
        $result = get_object_vars($this);
        foreach ($result as $key => $value) {
            if (Text::endsWith($key, 'Url')) {
                $result[$key] = $basePath . $value;
            }
        }
        return $result;
    }
}
