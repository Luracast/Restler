<?php
namespace OAuth2;

use Luracast\Restler\Format\HtmlFormat;
use Luracast\Restler\RestException;
use Luracast\Restler\Restler;
use Luracast\Restler\Util;
use OAuth2\Curl;

class Client
{
    /**
     * @var string url of the OAuth2 server to authorize
     */
    public static $serverUrl;
    public static $clientId = 'demoapp';
    public static $clientSecret = 'demopass';
    /**
     * @var string where to send the OAuth2 authorization result
     * (success or failure)
     */
    protected static $authorizeRedirectUrl;
    protected static $authorizeUrl;
    /**
     * @var Restler
     */
    public $restler;

    public function __construct()
    {
        if (!self::$serverUrl)
            self::$serverUrl = dirname(Util::$restler->_baseUrl) . '/_015_oauth2_server';
        self::$authorizeRedirectUrl = Util::$restler->_baseUrl . '/authorized';
        if (!self::$authorizeUrl) {
            self::$authorizeUrl =
                self::$serverUrl . '/authorize';
        }
    }

    /**
     * Stage 1: Let user start the oAuth process by clicking on the button
     *
     * He will then be taken to the oAuth server to grant or deny permission
     *
     * @format HtmlFormat
     * @view   oauth2/client/index.twig
     */
    public function index()
    {
        return array(
            'authorize_url' => self::$authorizeUrl,
            'authorize_redirect_url' => self::$authorizeRedirectUrl
        );
    }

    /**
     * Stage 2: Users response is recorded by the server
     *
     * Server redirects the user back with the result.
     *
     * If successful,
     *
     * Client exchanges the authorization code by a direct call (not through
     * user's browser) to get access token which can then be used call protected
     * APIs, if completed it calls a protected api and displays the result
     * otherwise client ends up showing the error message
     *
     * Else
     *
     * Client renders the error message to the user
     *
     * @param string $code
     * @param string $error_description
     * @param string $error_uri
     *
     * @return array
     *
     * @format HtmlFormat
     */
    public function authorized($code = null,
                               $error_description = null, $error_uri = null)
    {
        // the user denied the authorization request
        if (!$code) {
            HtmlFormat::$view = 'oauth2/client/denied.twig';
            return array('error' => compact('error_description', 'error_uri'));
        }
        // exchange authorization code for access token
        $query = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => self::$clientId,
            'client_secret' => self::$clientSecret,
            'redirect_uri' => self::$authorizeRedirectUrl,
        );
        //call the API using cURL
        $curl = new Curl();
        $endpoint = self::$serverUrl . '/grant';
        $response = $curl->request($endpoint, $query, 'POST');
        if (!(json_decode($response['response'], true))) {
            $status = $response['headers']['http_code'];
            echo '<h1>something went wrong - see the raw response</h1>';
            echo '<h2> Http ' . $status . ' - '
                . RestException::$codes[$status] . '</h2>';
            exit('<pre>' . print_r($response, true) . '</pre>');
        }
        $error = array();
        $response = json_decode($response['response'], true);

        // render error if applicable
        ($error['error_description'] =
            //OAuth error
            Util::nestedValue($response, 'error_description')) ||
        ($error['error_description'] =
            //Rester exception
            Util::nestedValue($response, 'error', 'message')) ||
        ($error['error_description'] =
            //cURL error
            Util::nestedValue($response, 'errorMessage')) ||
        ($error['error_description'] =
            //cURL error with out message
            Util::nestedValue($response, 'errorNumber')) ||
        ($error['error_description'] =
            'Unknown Error');

        $error_uri = Util::nestedValue($response, 'error_uri');

        if($error_uri){
            $error['error_uri'] = $error_uri;
        }

        // if it is successful, call the API with the retrieved token
        if (($token = Util::nestedValue($response,'access_token'))) {
            // make request to the API for awesome data
            $endpoint = self::$serverUrl . '/access?access_token=' . $token;
            $response = $curl->request($endpoint);
            HtmlFormat::$view = 'oauth2/client/granted.twig';
            return array(
                'token' => $token,
                'endpoint' => $endpoint
            ) + json_decode($response['response'], true);
        }
        HtmlFormat::$view = 'oauth2/client/error.twig';
        return array('error' => $error);
    }
}
