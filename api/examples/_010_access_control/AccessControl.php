<?php

use Luracast\Restler\Contracts\AccessControlInterface;
use Luracast\Restler\Contracts\ExplorableAuthenticationInterface;
use Luracast\Restler\Contracts\SelectivePathsInterface;
use Luracast\Restler\Contracts\SelectivePathsTrait;
use Luracast\Restler\Contracts\UserIdentificationInterface;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\OpenApi3\Security\ApiKeyAuth;
use Luracast\Restler\OpenApi3\Security\Scheme;
use Luracast\Restler\ResponseHeaders;
use Psr\Http\Message\ServerRequestInterface;

class AccessControl implements AccessControlInterface, SelectivePathsInterface, ExplorableAuthenticationInterface
{
    use SelectivePathsTrait;

    /** @var string[][] hardcoded to string[password]=>[id,role] for brevity */
    private static $users = [
        '123' => ['a', 'user'],
        '456' => ['b', 'user'],
        '789' => ['c', 'admin']
    ];
    public $requires = 'user';
    public $role = 'user';
    public $id = null;

    public static function getWWWAuthenticateString(): string
    {
        return 'Query name="api_key"';
    }

    public static function scheme(): Scheme
    {
        return new ApiKeyAuth('api_key', ApiKeyAuth::IN_QUERY);
    }

    /**
     * @param string $owner
     * @param bool $throwException
     * @return bool
     * @throws HttpException
     */
    public function _verifyPermissionForDocumentOwnedBy(string $owner, bool $throwException = false): bool
    {
        if ('admin' === $this->role) {
            return true;
        } //comment this line to make it owner only
        if ($owner === $this->id) {
            return true;
        }
        if (!$throwException) {
            return false;
        }
        throw new HttpException(403, 'permission denied.');
    }

    /**
     * @param ServerRequestInterface $request
     * @param UserIdentificationInterface $userIdentifier
     * @param ResponseHeaders $responseHeaders
     * @return bool
     * @throws HttpException 401
     */
    public function _isAllowed(
        ServerRequestInterface $request,
        UserIdentificationInterface $userIdentifier,
        ResponseHeaders $responseHeaders
    ): bool {
        if (!$api_key = $request->getQueryParams()['api_key'] ?? $request->getHeaderLine('api_key') ?? false) {
            return false;
        }
        if (!$user = (self::$users[$api_key] ?? null)) {
            $userIdentifier->setCacheIdentifier($api_key);
            return false;
        }
        [$id, $role] = $user;
        $userIdentifier->setCacheIdentifier($id);
        $this->role = $role;
        $this->id = $id;
        //Role-based access control (RBAC)
        return $role === 'admin' || $role === $this->requires;
    }
}
