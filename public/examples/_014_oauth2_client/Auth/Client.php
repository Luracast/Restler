<?php
namespace Auth;

use Luracast\Restler\Format\HtmlFormat;
use Luracast\Restler\RestException;
use Luracast\Restler\Restler;
use Luracast\Restler\Scope;
use Luracast\Restler\Util;
use Auth\Curl;

class Client
{
    /**
     * @var string url of the OAuth2 server to authorize
     */
    public static $serverUrl;
    public static $authorizeRoute = 'authorize';
    public static $tokenRoute = 'grant';
    public static $resourceMethod = 'GET';
    public static $resourceRoute = 'access';
    public static $resourceParams = array();
    public static $resourceOptions = array();
    public static $clientId = 'demoapp';
    public static $clientSecret = 'demopass';
    /**
     * @var string where to send the OAuth2 authorization result
     * (success or failure)
     */
    protected static $replyBackUrl;
    /**
     * @var Restler
     */
    public $restler;

    public function __construct()
    {
        //session_start(); //no need to start session, HtmlFormat does that for us
        HtmlFormat::$data['session_id'] = session_id();
        $this->restler = Scope::get('Restler');
        static::$replyBackUrl =
            $this->restler->getBaseUrl() . '/authorized';
        if (!static::$serverUrl) {
            static::$serverUrl =
                dirname($this->restler->getBaseUrl()) . '/_015_oauth2_server';
        }
        static::$authorizeRoute = static::fullURL(static::$authorizeRoute);
        static::$tokenRoute = static::fullURL(static::$tokenRoute);
        static::$resourceRoute = static::fullURL(static::$resourceRoute);
    }

    /**
     * Prefix server url if relative path is used
     *
     * @param string $path full url or relative path
     * @return string proper url
     */
    private function fullURL($path)
    {
        return 0 === strpos($path, 'http')
            ? $path
            : static::$serverUrl . '/' . $path;
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
            'authorize_url' => static::$authorizeRoute,
            'authorize_redirect_url' => static::$replyBackUrl
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
            'client_id' => static::$clientId,
            'client_secret' => static::$clientSecret,
            'redirect_uri' => static::$replyBackUrl,
        );
        //call the API using cURL
        $curl = new Curl();
        $endpoint = static::$tokenRoute;
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
            //Restler exception
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

        if ($error_uri) {
            $error['error_uri'] = $error_uri;
        }

        // if it is successful, call the API with the retrieved token
        if (($token = Util::nestedValue($response, 'access_token'))) {
            // make request to the API for awesome data
            $data = static::$resourceParams + array('access_token' => $token);
            $response = $curl->request(
                static::$resourceRoute,
                $data,
                static::$resourceMethod,
                static::$resourceOptions
            );
            HtmlFormat::$view = 'oauth2/client/granted.twig';
            return array(
                'token' => $token,
                'endpoint' => static::$resourceRoute . '?' . http_build_query($data)
            ) + json_decode($response['response'], true);
        }
        HtmlFormat::$view = 'oauth2/client/error.twig';
        return array('error' => $error);
    }
}
