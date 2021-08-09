<?php

namespace Auth;

use Luracast\Restler\Contracts\ExplorableAuthenticationInterface;
use Luracast\Restler\Contracts\SelectivePathsInterface;
use Luracast\Restler\Contracts\SelectivePathsTrait;
use Luracast\Restler\Contracts\UserIdentificationInterface;
use Luracast\Restler\Defaults;
use Luracast\Restler\OpenApi3\Security\AuthorizationCode as AuthorizationCodeFlow;
use Luracast\Restler\OpenApi3\Security\OAuth2;
use Luracast\Restler\OpenApi3\Security\Scheme;
use Luracast\Restler\ResponseHeaders;
use OAuth2\Convert;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\UserCredentials;
use OAuth2\Request;
use OAuth2\Response;
use OAuth2\Server as OAuth2Server;
use OAuth2\Storage\Pdo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Class Server
 *
 * @package OAuth2
 *
 */
class Server implements ExplorableAuthenticationInterface, SelectivePathsInterface
{
    use SelectivePathsTrait;

    /**
     * @var OAuth2Server
     */
    protected static $server;
    /**
     * @var Pdo
     */
    protected static $storage;
    /**
     * @var Request
     */
    protected $request;

    public static $fileName = 'oauth2_server.sqlite';
    public static $targetFile = null;

    public function __construct(ServerRequestInterface $psrRequest)
    {
        if (is_null(static::$targetFile)) {
            static::$targetFile = Defaults::$cacheDirectory . DIRECTORY_SEPARATOR . static::$fileName;
        }
        if (!file_exists(static::$targetFile)) {
            include_once __DIR__ . '/db/rebuild_db.php';
        }
        static::$storage = new Pdo(
            ['dsn' => 'sqlite:' . static::$targetFile]
        );
        // create array of supported grant types
        $grantTypes = array(
            'authorization_code' => new AuthorizationCode(static::$storage),
            'user_credentials' => new UserCredentials(static::$storage),
        );
        $this->request = Convert::fromPSR7($psrRequest);
        static::$server = new OAuth2Server(
            static::$storage,
            array('enforce_state' => true, 'allow_implicit' => true),
            $grantTypes
        );
    }

    /**
     * Stage 1: Client sends the user to this page
     *
     * User responds by accepting or denying
     *
     * @view oauth2/server/authorize.twig
     * @response-format Html
     */
    public function authorize()
    {
        // validate the authorize request.  if it is invalid,
        // redirect back to the client with the errors
        if (!static::$server->validateAuthorizeRequest($this->request)) {
            return Convert::toPSR7(static::$server->getResponse());
        }
        return ['queryString' => http_build_query($this->request->getAllQueryParameters())];
    }

    /**
     * Stage 2: User response is captured here
     *
     * Success or failure is communicated back to the Client using the redirect
     * url provided by the client
     *
     * On success authorization code is sent along
     *
     *
     * @param bool $authorize
     *
     * @return ResponseInterface
     *
     * @request-format Json,Upload
     */
    public function postAuthorize($authorize = false)
    {
        /** @var Response $response */
        $response = static::$server->handleAuthorizeRequest(
            $this->request,
            new Response(),
            (bool)$authorize
        );
        return Convert::toPSR7($response);
    }

    /**
     * Stage 3: Client directly calls this api to exchange access token
     *
     * It can then use this access token to make calls to protected api
     *
     * @request-format Json,Upload
     */
    public function postGrant()
    {
        /** @var Response $response */
        $response = static::$server->handleTokenRequest($this->request);
        return Convert::toPSR7($response);
    }

    /**
     * Sample api protected with OAuth2
     *
     * For testing the oAuth token
     *
     * @access protected
     * @url GET access
     * @url POST access
     */
    public function access()
    {
        return ['friends' => ['john', 'matt', 'jane']];
    }


    public static function getWWWAuthenticateString(): string
    {
        return 'Bearer realm="example"';
    }


    /**
     * Access verification method.
     *
     * API access will be denied when this method returns false
     *
     *
     * @param ServerRequestInterface $request
     *
     * @param UserIdentificationInterface $userIdentifier
     * @param ResponseHeaders $responseHeaders
     * @return bool true when api access is allowed false otherwise
     */
    public function _isAllowed(
        ServerRequestInterface $request,
        UserIdentificationInterface $userIdentifier,
        ResponseHeaders $responseHeaders
    ): bool
    {
        $authRequest = Convert::fromPSR7($request);
        return self::$server->verifyResourceRequest($authRequest);
    }

    public static function scheme(): Scheme
    {
        /** @var AuthorizationCode $g1 */
        $g1 = static::$server->getGrantType('authorization_code');
        //providing relative path here! explorer will compute the full url
        return new OAuth2(
            new AuthorizationCodeFlow(
                'examples/_015_oauth2_server/authorize',
                'examples/_015_oauth2_server/grant',
                'examples/_015_oauth2_server/grant',
                []
            )
        );
    }
}
