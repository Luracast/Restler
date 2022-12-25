<?php


namespace Luracast\Restler\OpenApi3\Security;


use Luracast\Restler\Utils\Text;

abstract class OAuth2Flow
{
    protected object $scopes;
    protected string $refreshUrl;

    public function toArray(string $basePath = '/'): array
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
