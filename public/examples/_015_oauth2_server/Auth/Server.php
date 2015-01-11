<?php
namespace Auth;

use Luracast\Restler\iAuthenticate;
use OAuth2\GrantType\UserCredentials;
use OAuth2\Storage\Pdo;
use OAuth2\Server as OAuth2Server;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\Request;
use OAuth2\Response;


/**
 * Class Server
 *
 * @package OAuth2
 *
 */
class Server implements iAuthenticate
{
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
    protected static $request;

    public function __construct()
    {
        $dir = __DIR__ . '/db/';
        $file = 'oauth.sqlite';
        if (!file_exists($dir . $file)) {
            include_once $dir . 'rebuild_db.php';
        }
        static::$storage = new Pdo(
            array('dsn' => 'sqlite:' . $dir . $file)
        );
        // create array of supported grant types
        $grantTypes = array(
            'authorization_code' => new AuthorizationCode(static::$storage),
            'user_credentials'   => new UserCredentials(static::$storage),
        );
        static::$request = Request::createFromGlobals();
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
     * @format HtmlFormat
     */
    public function authorize()
    {
        static::$server->getResponse(static::$request);
        // validate the authorize request.  if it is invalid,
        // redirect back to the client with the errors in tow
        if (!static::$server->validateAuthorizeRequest(static::$request)) {
            static::$server->getResponse()->send();
            exit;
        }
        return array('queryString' => $_SERVER['QUERY_STRING']);
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
     * @return \OAuth2\Response
     *
     * @format JsonFormat,UploadFormat
     */
    public function postAuthorize($authorize = false)
    {
        static::$server->handleAuthorizeRequest(
            static::$request,
            new Response(),
            (bool)$authorize
        )->send();
        exit;
    }

    /**
     * Stage 3: Client directly calls this api to exchange access token
     *
     * It can then use this access token to make calls to protected api
     *
     * @format JsonFormat,UploadFormat
     */
    public function postGrant()
    {
        static::$server->handleTokenRequest(static::$request)->send();
        exit;
    }

    /**
     * Sample api protected with OAuth2
     *
     * For testing the oAuth token
     *
     * @access protected
     */
    public function access()
    {
        return array(
            'friends' => array('john', 'matt', 'jane')
        );
    }


    /**
     * Access verification method.
     *
     * API access will be denied when this method returns false
     *
     * @return boolean true when api access is allowed; false otherwise
     */
    public function __isAllowed()
    {
        return self::$server->verifyResourceRequest(static::$request);
    }

    public function __getWWWAuthenticateString()
    {
        return 'Bearer realm="example"';
    }
}
