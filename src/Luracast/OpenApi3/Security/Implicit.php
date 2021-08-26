<?php


namespace Luracast\Restler\OpenApi3\Security;


class Implicit extends OAuth2Flow
{
    private string $authorizationUrl;

    /**
     * Implicit OAuth2 Flow.
     * @param string $authorizationUrl
     * @param string $refreshUrl
     * @param array $scopes key value pairs of allowed scope and description
     */
    public function __construct(string $authorizationUrl, string $refreshUrl, array $scopes)
    {
        $this->authorizationUrl = $authorizationUrl;
        $this->refreshUrl = $refreshUrl;
        $this->scopes = $scopes;
    }
}