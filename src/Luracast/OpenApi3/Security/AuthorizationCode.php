<?php


namespace Luracast\Restler\OpenApi3\Security;


class AuthorizationCode extends OAuth2Flow
{
    protected string $authorizationUrl;
    protected string $tokenUrl;

    /**
     * AuthorizationCode OAuth2 Flow.
     * @param string $authorizationUrl
     * @param string $tokenUrl
     * @param string $refreshUrl
     * @param array $scopes key value pairs of allowed scope and description
     */
    public function __construct(string $authorizationUrl, string $tokenUrl, string $refreshUrl, array $scopes)
    {
        $this->authorizationUrl = $authorizationUrl;
        $this->refreshUrl = $refreshUrl;
        $this->scopes = $scopes;
        $this->tokenUrl = $tokenUrl;
    }
}
