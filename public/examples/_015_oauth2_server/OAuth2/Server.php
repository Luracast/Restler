<?php
namespace OAuth2;

use Luracast\Restler\iAuthenticate;
use OAuth2_Request;
use OAuth2_Response;
use OAuth2_GrantType_AuthorizationCode;
use OAuth2_Storage_Pdo;
use OAuth2_Server;

/**
 * Class Server
 *
 * @package OAuth2
 *
 */
class Server implements iAuthenticate
{
    /**
     * @var OAuth2_Server
     */
    protected static $server;
    /**
     * @var OAuth2_Storage_Pdo
     */
    protected static $storage;
    /**
     * @var OAuth2_Request
     */
    protected static $request;

    public function __construct()
    {
        $dir = __DIR__ . '/db/';
        $file = 'oauth.sqlite';
        if (!file_exists($dir . $file)) {
            include_once $dir . 'rebuild_db.php';
        }
        static::$storage = new OAuth2_Storage_Pdo(
            array('dsn' => 'sqlite:' . $dir . $file)
        );
        static::$request = OAuth2_Request::createFromGlobals();
        static::$server = new OAuth2_Server(static::$storage);
        static::$server->addGrantType(
            new OAuth2_GrantType_AuthorizationCode(static::$storage)
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
     * @format JsonFormat,UploadFormat
     */
    public function postAuthorize($authorize = false)
    {
        $response = static::$server->handleAuthorizeRequest(
            static::$request,
            (bool)$authorize
        );
        die($response->send());
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
        $response = static::$server->handleGrantRequest(
            static::$request
        );
        die($response->send());
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
        return self::$server->verifyAccessRequest(static::$request);
    }
}
