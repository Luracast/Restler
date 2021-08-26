<?php


namespace Luracast\Restler\OpenApi3\Security;


class ClientCredentials extends OAuth2Flow
{
    protected string $tokenUrl;

    /**
     * AuthorizationCode OAuth2 Flow.
     * @param string $tokenUrl
     * @param string $refreshUrl
     * @param array $scopes key value pairs of allowed scope and description
     */
    public function __construct(string $tokenUrl, string $refreshUrl, array $scopes)
    {
        $this->refreshUrl = $refreshUrl;
        $this->scopes = $scopes;
        $this->tokenUrl = $tokenUrl;
    }
}