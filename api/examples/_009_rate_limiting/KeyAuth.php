<?php

use Luracast\Restler\Contracts\ExplorableAuthenticationInterface;
use Luracast\Restler\Contracts\SelectivePathsInterface;
use Luracast\Restler\Contracts\SelectivePathsTrait;
use Luracast\Restler\Contracts\UserIdentificationInterface;
use Luracast\Restler\OpenApi3\Security\ApiKeyAuth;
use Luracast\Restler\OpenApi3\Security\Scheme;
use Luracast\Restler\ResponseHeaders;
use Psr\Http\Message\ServerRequestInterface;

class KeyAuth implements ExplorableAuthenticationInterface, SelectivePathsInterface
{
    use SelectivePathsTrait;

    /**
     * @return string string to be used with WWW-Authenticate header
     * @example Basic
     * @example Digest
     * @example OAuth
     */
    public static function getWWWAuthenticateString(): string
    {
        return 'Query name="api_key"';
    }

    public static function scheme(): Scheme
    {
        return new ApiKeyAuth('api_key', ApiKeyAuth::IN_QUERY);
    }

    public function _isAllowed(
        ServerRequestInterface $request,
        UserIdentificationInterface $userIdentifier,
        ResponseHeaders $responseHeaders
    ): bool {
        $query = $request->getQueryParams();
        if ('r4rocks' === ($query['api_key'] ?? '')) {
            // if api key is unique for each user
            // we can use that to identify and track the user
            // for rate limiting and more
            $userIdentifier->setUniqueIdentifier($query['api_key']);
            return true;
        }
        return false;
    }
}
