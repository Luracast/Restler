<?php
namespace Luracast\Restler;

use stdClass;
use Exception;
use Reflection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use InvalidArgumentException;
use Luracast\Restler\Format\iFormat;
use Luracast\Restler\Format\UrlEncodedFormat;
use Luracast\Restler\Data\iValidate;
use Luracast\Restler\Data\Validator;
use Luracast\Restler\Data\ValidationInfo;

/**
 * REST API Server. It is the server part of the Restler framework.
 * inspired by the RestServer code from
 * <http://jacwright.com/blog/resources/RestServer.txt>
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0
 */
class Restler
{

    // ==================================================================
    //
    // Public variables
    //
    // ------------------------------------------------------------------

    const VERSION = '3.0.0';

    /**
     * Base URL currently being used
     *
     * @var string
     */
    public $baseUrl;

    /**
     * URL of the currently mapped service
     *
     * @var string
     */
    public $url;

    /**
     * Http request method of the current request.
     * Any value between [GET, PUT, POST, DELETE]
     *
     * @var string
     */
    public $requestMethod;

    /**
     * Requested data format.
     * Instance of the current format class
     * which implements the iFormat interface
     *
     * @var iFormat
     * @example jsonFormat, xmlFormat, yamlFormat etc
     */
    public $requestFormat;

    /**
     * Data sent to the service
     *
     * @var array
     */
    public $requestData = array();

    /**
     * Used in production mode to store the routes and more
     *
     * @var iCache
     */
    public $cache;

    /**
     * base directory to locate format and auth files
     *
     * @var string
     */
    public $baseDir;

    /**
     * Response data format.
     * Instance of the current format class
     * which implements the iFormat interface
     *
     * @var iFormat
     * @example jsonFormat, xmlFormat, yamlFormat etc
     */
    public $responseFormat;

    // ==================================================================
    //
    // Private & Protected variables
    //
    // ------------------------------------------------------------------

    /**
     * When set to false, it will run in debug mode and parse the
     * class files every time to map it to the URL
     *
     * @var boolean
     */
    protected $_productionMode;

    /**
     * Associated array that maps urls to their respective class and method
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * Associated array that maps formats to their respective format class name
     *
     * @var array
     */
    protected $_formatMap = array();

    /**
     * Instance of the current api service class
     *
     * @var object
     */
    protected $_apiClassInstance;

    /**
     * Name of the api method being called
     *
     * @var string
     */
    protected $_apiMethod;

    /**
     * method information including metadata
     *
     * @var stdClass
     */
    protected $_apiMethodInfo;

    /**
     * list of filter classes
     *
     * @var array
     */
    protected $_filterClasses = array();

    /**
     * list of authentication classes
     *
     * @var array
     */
    protected $_authClasses = array();

    /**
     * list of error handling classes
     *
     * @var array
     */
    protected $_errorClasses = array();

    /**
     * Caching of url map is enabled or not
     *
     * @var boolean
     */
    protected $_cached;

    protected $_apiVersion = 1;
    protected $_requestedApiVersion = 1;
    protected $_apiMinimumVersion = 1;
    protected $_apiClassPath = '';
    protected $_log = '';
    protected $startTime;
    protected $_authenticated = false;

    // ==================================================================
    //
    // Public functions
    //
    // ------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param boolean $productionMode
     *                              When set to false, it will run in
     *                              debug mode and parse the class files
     *                              every time to map it to the URL
     *
     * @param bool    $refreshCache will update the cache when set to true
     */
    public function __construct($productionMode = false, $refreshCache = false)
    {
        $this->startTime = time();
        $this->_productionMode = $productionMode;
        $this->cache = new Defaults::$cacheClass();
        $this->baseDir = __DIR__;
        // use this to rebuild cache every time in production mode
        if ($productionMode && $refreshCache) {
            $this->_cached = false;
        }
        Util::$restler = $this;
    }

    /**
     * Store the url map cache if needed
     */
    public function __destruct()
    {
        if ($this->_productionMode && !$this->_cached) {
            $this->cache->set('routes', $this->_routes);
        }
    }

    /**
     * Provides backward compatibility with older versions of Restler
     *
     * @param int $version restler version
     *
     * @throws \OutOfRangeException
     */
    public function setCompatibilityMode($version = 2)
    {
        switch ($version) {

            case 2:

                //changes in auto loading
                spl_autoload_register(function ($class)
                {
                    include strtolower($class) . '.php';
                });

                //changes in iAuthenticate
                Defaults::$authenticationMethod = '__isAuthenticated';
                eval('
                interface iAuthenticate{
                    public function __isAuthenticated();
                }
                ');

                //changes in auto routing
                Defaults::$smartAutoRouting = false;

                //changes in parsing embedded data in comments
                CommentParser::$embeddedDataPattern = '/\((\S+)\)/ms';
                CommentParser::$embeddedDataIndex = 1;

                break;

            case 1:

                //changes in iAuthenticate
                Defaults::$authenticationMethod = 'isAuthenticated';
                eval('
                interface iAuthenticate{
                    public function isAuthenticated();
                }
                ');

                //changes in routing
                Defaults::$autoRoutingEnabled = false;
                break;

            default:
                throw new \OutOfRangeException();
        }
    }

    public function setApiClassPath($path)
    {
        $this->_apiClassPath = !empty($path) &&
            $path{0} == '/' ? $path : $_SERVER['DOCUMENT_ROOT'] .
            dirname($_SERVER['SCRIPT_NAME']) . '/' . $path;
        $this->_apiClassPath = rtrim($this->_apiClassPath, '/');
    }

    public function setAPIVersion($version, $minimum = 0, $apiClassPath = '')
    {
        if (!is_int($version)) {
            throw new InvalidArgumentException
            ('version should be an integer');
        }
        $this->_apiVersion = $version;
        if (is_int($minimum)) {
            $this->_apiMinimumVersion = $minimum;
        }
        if (!empty($apiClassPath)) {
            $this->setAPIClassPath($apiClassPath);
        }
        $path = $this->_apiClassPath . DIRECTORY_SEPARATOR .
            "v$this->_apiVersion" . DIRECTORY_SEPARATOR;
        set_include_path($path . PATH_SEPARATOR . get_include_path());
    }

    /**
     * Call this method and pass all the formats that should be
     * supported by the API.
     * Accepts multiple parameters
     *
     * @param string ,... $formatName class name of the format class that
     *               implements iFormat
     *
     * @example $restler->setSupportedFormats('JsonFormat', 'XmlFormat'...);
     * @throws \Exception
     */
    public function setSupportedFormats()
    {
        $args = func_get_args();
        $extensions = array();
        foreach ($args as $className) {
            if (!is_string($className) || !class_exists($className))
                throw new Exception("$className is not a valid Format Class.");

            $obj = new $className ();

            if (!$obj instanceof iFormat)
                throw new Exception('Invalid format class; must implement ' .
                    'iFormat interface');

            foreach ($obj->getMIMEMap() as $mime => $extension) {
                if (!isset($this->_formatMap[$extension]))
                    $this->_formatMap[$extension] = $className;
                if (!isset($this->_formatMap[$mime]))
                    $this->_formatMap[$mime] = $className;
                $extensions[".$extension"] = true;
            }
        }
        $this->_formatMap['default'] = $args[0];
        $this->_formatMap['extensions'] = array_keys($extensions);
    }

    /**
     * Add api classes through this method.
     *
     * All the public methods that do not start with _ (underscore)
     * will be will be exposed as the public api by default.
     *
     * All the protected methods that do not start with _ (underscore)
     * will exposed as protected api which will require authentication
     *
     * @param string $className
     *            name of the service class
     * @param string $resourcePath
     *            optional url prefix for mapping, uses
     *            lowercase version of the class name when not specified
     *
     * @throws \Exception when supplied with invalid class name
     */
    public function addAPIClass($className, $resourcePath = null)
    {
        if (!class_exists($className, true)) {
            throw new Exception("API class $className is missing.");
        }
        $this->loadCache();
        if (!$this->_cached) {
            if (is_null($resourcePath)) {
                $resourcePath = str_replace('__v', '/v',
                    strtolower($className));
                $index = strrpos($className, '\\');
                if ($index !== false)
                    $resourcePath = substr($resourcePath, $index + 1);
            } else
                $resourcePath = trim($resourcePath, '/');
            if (strlen($resourcePath) > 0)
                $resourcePath .= '/';
            $this->generateMap($className, $resourcePath);
        }
    }

    /**
     * Classes implementing iFilter interface can be added for filtering out
     * the api consumers.
     *
     * It can be used for rate limiting based on usage from a specific ip
     * address or filter by country, device etc.
     *
     * @param $className
     */
    public function addFilterClass($className)
    {
        $this->_filterClasses[] = $className;
    }

    /**
     * protected methods will need at least one authentication class to be set
     * in order to allow that method to be executed
     *
     * @param string $className
     *            of the authentication class
     * @param string $resourcePath
     *            optional url prefix for mapping
     */
    public function addAuthenticationClass($className, $resourcePath = null)
    {
        $this->_authClasses[] = $className;
        $this->addAPIClass($className, $resourcePath);
    }

    /**
     * Add class for custom error handling
     *
     * @param string $className
     *            of the error handling class
     */
    public function addErrorClass($className)
    {
        $this->_errorClasses[] = $className;
    }

    /**
     * Convenience method to respond with an error message.
     *
     * @param int    $statusCode   http error code
     * @param string $errorMessage optional custom error message
     *
     * @return null
     */
    public function handleError($statusCode, $errorMessage = null)
    {
        $method = "handle$statusCode";
        $handled = false;
        foreach ($this->_errorClasses as $className) {
            if (method_exists($className, $method)) {
                $obj = new $className ();
                $obj->restler = $this;
                $obj->$method ();
                $handled = true;
            }
        }
        if ($handled)
            return null;
        $this->sendData(null, $statusCode, $errorMessage);
    }

    /**
     * An initialize function to allow use of the restler error generation
     * functions for pre-processing and pre-routing of requests.
     */
    public function init()
    {
        if (empty($this->_formatMap)) {
            $this->setSupportedFormats('JsonFormat');
        }
        $this->url = $this->getPath();
        $this->requestMethod = Util::getRequestMethod();
        $this->responseFormat = $this->getResponseFormat();
        $this->requestFormat = $this->getRequestFormat();
        $this->responseFormat->restler = $this;
        if (is_null($this->requestFormat)) {
            $this->requestFormat = $this->responseFormat;
        } else {
            $this->requestFormat->restler = $this;
        }
        if ($this->requestMethod == 'PUT' ||
            $this->requestMethod == 'PATCH' ||
            $this->requestMethod == 'POST'
        ) {
            $this->requestData = $this->getRequestData();
        }
        //parse defaults
        foreach ($_GET as $key => $value) {
            if (isset(Defaults::$aliases[$key])) {
                $_GET[Defaults::$aliases[$key]] = $value;
                unset($_GET[$key]);
                $key = Defaults::$aliases[$key];
            }
            if (in_array($key, Defaults::$overridables)) {
                Defaults::setProperty($$key, $value);
            }
        }
    }

    /**
     * Main function for processing the api request
     * and return the response
     *
     * @throws \Exception     when the api service class is missing
     * @throws RestException to send error response
     */
    public function handle()
    {
        $this->init();
        $this->_apiMethodInfo = $o = $this->mapUrlToMethod();
        if (isset($o->metadata)) {
            foreach (Defaults::$fromComments as $key => $defaultsKey) {
                if (array_key_exists($key, $o->metadata)) {
                    $value = $o->metadata[$key];
                    Defaults::setProperty($defaultsKey, $value);
                }
            }
        }
        $result = null;
        if (!isset($o->className)) {
            $this->handleError(404);
        } else {
            try {
                $accessLevel = max(Defaults::$apiAccessLevel, $o->accessLevel);
                if ($accessLevel) {
                    if (!count($this->_authClasses)) {
                        throw new RestException(401);
                    }
                    foreach ($this->_authClasses as $authClass) {
                        $authObj = Util::setProperties(
                            $authClass,
                            $o->metadata
                        );
                        if (!method_exists($authObj,
                            Defaults::$authenticationMethod)
                        ) {
                            throw new RestException (
                                500, 'Authentication Class ' .
                                'should implement iAuthenticate');
                        } elseif (
                            !$authObj->{Defaults::$authenticationMethod}()
                        ) {
                            throw new RestException(401);
                        }
                    }
                    $this->_authenticated = true;
                }
            } catch (RestException $e) {
                if ($accessLevel > 1) { //when it is not a hybrid api
                    $this->handleError($e->getCode(), $e->getMessage());
                } else {
                    $this->_authenticated = false;
                }
            }
            try {
                foreach ($this->_filterClasses as $filterClass) {
                    /**
                     * @var iFilter
                     */
                    $filterObj = Util::setProperties(
                        $filterClass,
                        $o->metadata
                    );
                    if (!$filterObj instanceof iFilter) {
                        throw new RestException (
                            500, 'Filter Class ' .
                            'should implement iFilter');
                    } elseif (!$filterObj->__isAllowed()) {
                        throw new RestException(403); //Forbidden
                    }
                }
                Util::setProperties(
                    get_class($this->requestFormat),
                    $o->metadata, $this->requestFormat
                );

                $preProcess = '_' . $this->requestFormat->getExtension() .
                    '_' . $o->methodName;
                $this->_apiMethod = $o->methodName;
                $object = $this->_apiClassInstance = null;
                // TODO:check if the api version requested is allowed by class
                // TODO: validate params using iValidate
                foreach ($o->metadata['param'] as $index => $param) {
                    $info = &$param [CommentParser::$embeddedDataName];
                    if (!isset ($info['validate'])
                        || $info['validate'] != false
                    ) {
                        if (isset($info['method'])) {
                            if (!isset($object)) {
                                $object = $this->_apiClassInstance
                                    = Util::setProperties($o->className);
                            }
                            $info ['apiClassInstance'] = $object;
                        }
                        //convert to instance of ValidationInfo
                        $info = new ValidationInfo($param);
                        $valid = Validator::validate(
                            $o->arguments[$index], $info);
                        $o->arguments[$index] = $valid;
                    }
                }
                if (!isset($object)) {
                    $object = $this->_apiClassInstance
                        = Util::setProperties($o->className);
                }
                if (method_exists($o->className, $preProcess)) {
                    call_user_func_array(array(
                        $object,
                        $preProcess
                    ), $o->arguments);
                }
                switch ($accessLevel) {
                    case 3 : //protected method
                        $reflectionMethod = new ReflectionMethod(
                            $object,
                            $o->methodName
                        );
                        $reflectionMethod->setAccessible(true);
                        $result = $reflectionMethod->invokeArgs(
                            $object,
                            $o->arguments
                        );
                        break;
                    default :
                        $result = call_user_func_array(array(
                            $object,
                            $o->methodName
                        ), $o->arguments);
                }
            } catch (RestException $e) {
                $this->handleError($e->getCode(), $e->getMessage());
            }
        }
        $this->sendData($result);
    }

    /**
     * Encodes the response in the preferred format and sends back.
     *
     * @param mixed       $data array or scalar value or iValueObject or null
     * @param int         $statusCode
     * @param string|null $statusMessage
     */
    public function sendData($data, $statusCode = 0, $statusMessage = null)
    {
        //$this->_log = ob_get_clean ();
        @header('Cache-Control: ' . Defaults::$headerCacheControl);
        $expires = Defaults::$headerExpires;
        if ($expires > 0) {
            $expires = gmdate('D, d M Y H:i:s \G\M\T', time() + $expires);
        }
        @header('Expires: ' . $expires);
        @header('Content-Type: ' . $this->responseFormat->getMIME());
        @header('X-Powered-By: Luracast Restler v' . Restler::VERSION);

        if (isset($this->_apiMethodInfo->metadata['header'])) {
            foreach ($this->_apiMethodInfo->metadata['header'] as $header)
                @header($header, true);
        }

        /**
         *
         * @var iRespond DefaultResponder
         */
        $responder = Util::setProperties(
            Defaults::$responderClass,
            isset($this->_apiMethodInfo->metadata)
                ? $this->_apiMethodInfo->metadata
                : null
        );
        if ($statusCode == 0) {
            if (isset($this->_apiMethodInfo->metadata['status'])) {
                $this->setStatus($this->_apiMethodInfo->metadata['status']);
            }
            $data = $responder->formatResponse($data);
            $data = $this->responseFormat->encode($data,
                !$this->_productionMode);
            $postProcess = '_' . $this->_apiMethod . '_' .
                $this->responseFormat->getExtension();
            if (isset($this->_apiClassInstance)
                && method_exists(
                    $this->_apiClassInstance,
                    $postProcess
                )
            ) {
                $data = call_user_func(array(
                    $this->_apiClassInstance,
                    $postProcess
                ), $data);
            }
        } else {
            $message = RestException::$codes[$statusCode] .
                (empty($statusMessage) ? '' : ': ' . $statusMessage);
            $this->setStatus($statusCode);
            $data = $this->responseFormat->encode(
                $responder->formatError($statusCode, $message),
                !$this->_productionMode);
        }
        //handle throttling
        if (Defaults::$throttle) {
            $elapsed = time() - $this->startTime;
            if (Defaults::$throttle / 1e3 > $elapsed) {
                usleep(1e6 * (Defaults::$throttle / 1e3 - $elapsed));
            }
        }
        die($data);
    }

    /**
     * Sets the HTTP response status
     *
     * @param int $code
     *            response code
     */
    public function setStatus($code)
    {
        if (Defaults::$suppressResponseCode) {
            $code = 200;
        }
        @header("{$_SERVER['SERVER_PROTOCOL']} $code " .
            RestException::$codes[$code]);
    }

    /**
     * Magic method to expose some protected variables
     *
     * @param string $name
     *
     * @return null
     */
    public function __get($name)
    {
        $privateProperty = "_$name";
        if (isset($this->$privateProperty)) {
            return $this->$privateProperty;
        }
        return null;
    }

    // ==================================================================
    //
    // Protected functions
    //
    // ------------------------------------------------------------------

    /**
     * Parses the request url and get the api path
     *
     * @return string api path
     */
    protected function getPath()
    {
        $fullPath = $_SERVER['REQUEST_URI'];
        $path = urldecode(
            Util::removeCommonPath(
                $fullPath,
                $_SERVER['SCRIPT_NAME']
            )
        );
        $baseUrl = isset($_SERVER['HTTPS']) &&
            $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        if ($_SERVER['SERVER_PORT'] != '80') {
            $baseUrl .= $_SERVER['SERVER_NAME'] . ':'
                . $_SERVER['SERVER_PORT'];
        } else {
            $baseUrl .= $_SERVER['SERVER_NAME'];
        }
        $this->baseUrl = $baseUrl . rtrim(substr(
            $fullPath,
            0,
            strlen($fullPath) - strlen($path)
        ), '/');

        $path = preg_replace('/(\/*\?.*$)|(\/$)/', '', $path);
        $path = str_replace($this->_formatMap['extensions'], '', $path);
        if ($this->_apiVersion && strlen($path) && $path{0} == 'v') {
            $version = intval(substr($path, 1));
            if ($version && $version <= $this->_apiVersion) {
                $this->_requestedApiVersion = $version;
                $path = explode('/', $path, 2);
                $path = $path[1];
            }
        } elseif ($this->_apiVersion)
            $this->_requestedApiVersion = $this->_apiVersion;

        return $path;
    }


    /**
     * Parses the request to figure out format of the request data
     *
     * @return iFormat any class that implements iFormat
     * @example JsonFormat
     */
    protected function getRequestFormat()
    {
        $format = null;
        // check if client has sent any information on request format
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $mime = $_SERVER['CONTENT_TYPE'];
            if (false !== $pos = strpos($mime, ';')) {
                $mime = substr($mime, 0, $pos);
            }
            if ($mime == UrlEncodedFormat::MIME)
                $format = new UrlEncodedFormat ();
            elseif (isset($this->_formatMap[$mime])) {
                $format = $this->_formatMap[$mime];
                if (is_string($format)) {
                    $format = is_string($format) ? new $format () : $format;
                }
                $format->setMIME($mime);
            } else {
                $this->handleError(403, "Content type $mime is not supported.");
                return null;
            }
        }
        return $format;
    }

    /**
     * Parses the request to figure out the best format for response.
     * Extension, if present, overrides the Accept header
     *
     * @return iFormat any class that implements iFormat
     * @example JsonFormat
     */
    protected function getResponseFormat()
    {
        // check if client has specified an extension
        /**
         *
         * @var iFormat
         */
        $format = null;
        $extensions = explode(
            '.',
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );
        while ($extensions) {
            $extension = array_pop($extensions);
            $extension = explode('/', $extension);
            $extension = array_shift($extension);
            if ($extension && isset($this->_formatMap[$extension])) {
                $format = $this->_formatMap[$extension];
                $format = is_string($format) ? new $format () : $format;
                $format->setExtension($extension);
                // echo "Extension $extension";
                return $format;
            }
        }
        // check if client has sent list of accepted data formats
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $acceptList = array();
            $accepts = explode(',', strtolower($_SERVER['HTTP_ACCEPT']));
            if (!is_array($accepts)) {
                $accepts = array($accepts);
            }
            foreach ($accepts as $pos => $accept) {
                $parts = explode(';q=', trim($accept));
                $type = array_shift($parts);
                $quality = count($parts) ?
                    floatval(array_shift($parts)) :
                    (1000 - $pos) / 1000;
                $acceptList[$type] = $quality;
            }
            arsort($acceptList);
            foreach ($acceptList as $accept => $quality) {
                if (isset($this->_formatMap[$accept])) {
                    $format = $this->_formatMap[$accept];
                    $format = is_string($format) ? new $format : $format;
                    $format->setMIME($accept);
                    //echo "MIME $accept";
                    // Tell cache content is based on Accept header
                    @header("Vary: Accept");

                    return $format;
                }
            }
        } else {
            // RFC 2616: If no Accept header field is
            // present, then it is assumed that the
            // client accepts all media types.
            $_SERVER['HTTP_ACCEPT'] = '*/*';
        }
        if (strpos($_SERVER['HTTP_ACCEPT'], '*') !== false) {
            if (strpos($_SERVER['HTTP_ACCEPT'], 'application/*') !== false) {
                $format = new JsonFormat;
            } elseif (strpos($_SERVER['HTTP_ACCEPT'], 'text/*') !== false) {
                $format = new XmlFormat;
            } elseif (strpos($_SERVER['HTTP_ACCEPT'], '*/*') !== false) {
                $format = $this->_formatMap['default'];
                $format = new $format;
            }
        }
        if (empty($format)) {
            // RFC 2616: If an Accept header field is present, and if the
            // server cannot send a response which is acceptable according to
            // the combined Accept field value, then the server SHOULD send
            // a 406 (not acceptable) response.
            @header('HTTP/1.1 406 Not Acceptable');
            die('406 Not Acceptable: The server was unable to ' .
                'negotiate content for this request.');
        } else {
            // Tell cache content is based ot Accept header
            @header("Vary: Accept");
            return $format;
        }
    }

    /**
     * Parses the request data and returns it
     *
     * @return array php data
     */
    protected function getRequestData()
    {
        try {
            $r = file_get_contents('php://input');
            if (is_null($r)) {
                return $_GET;
            }
            $r = $this->requestFormat->decode($r);
            return is_null($r) ? array() : $r;
        } catch (RestException $e) {
            $this->handleError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Find the api method to execute for the requested Url
     *
     * @return \stdClass
     */
    protected function mapUrlToMethod()
    {
        if (!isset($this->_routes[$this->requestMethod])) {
            return new stdClass ();
        }
        $urls = $this->_routes[$this->requestMethod];
        if (!$urls) {
            return new stdClass ();
        }
        $found = false;
        $this->requestData += $_GET;
        $params = array(
            'request_data' => $this->requestData
        );
        $params += $this->requestData;
        $lc = strtolower($this->url);
        $call = new stdClass;
        foreach ($urls as $url => $call) {
            /*
            Events::trigger('onRoute', array('url' => $url, 'target' => $call),
                __CLASS__);
            */
            $call = (object)$call;
            if (strstr($url, '{')) {
                $regex = str_replace(array(
                    '{',
                    '}'
                ), array(
                    '(?P<',
                    '>[^/]+)'
                ), $url);
                if (preg_match(":^$regex$:i", $this->url, $matches)) {
                    foreach ($matches as $arg => $match) {
                        if (isset($call->arguments[$arg])) {
                            $params[$arg] = $match;
                        }
                    }
                    $found = true;
                    break;
                }
            } elseif (strstr($url, ':')) {
                $regex = preg_replace(
                    '/\\\:([^\/]+)/',
                    '(?P<$1>[^/]+)',
                    preg_quote($url)
                );
                if (preg_match(":^$regex$:i", $this->url, $matches)) {
                    foreach ($matches as $arg => $match) {
                        if (isset($call->arguments[$arg])) {
                            $params[$arg] = $match;
                        }
                    }
                    $found = true;
                    break;
                }
            } elseif ($url == $lc) {
                $found = true;
                break;
            }
        }
        if ($found) {
            $p = $call->defaults;
            foreach ($call->arguments as $key => $value) {
                if (isset($params[$key])) {
                    $p[$value] = $params[$key];
                }
            }
            $call->arguments = $p;

            return $call;
        }
    }

    /**
     * Load routes from cache
     *
     * @return null
     */
    protected function loadCache()
    {
        if ($this->_cached !== null)
            return null;
        $this->_cached = false;
        if ($this->_productionMode) {
            $routes = $this->cache->get('routes');
            if (isset($routes) && is_array($routes)) {
                $this->_routes = $routes;
                $this->_cached = true;
            }
        }
    }

    /**
     * Generates cacheable url to method mapping
     *
     * @param string $className
     * @param string $resourcePath
     */
    protected function generateMap($className, $resourcePath = '')
    {
        /*
         * Mapping Rules - Optional parameters should not be mapped to URL - if
         * a required parameter is of primitive type - Map them to URL - Do not
         * create routes with out it - if a required parameter is not primitive
         * type - Do not include it in URL
         */
        $reflection = new ReflectionClass($className);
        $classMetadata = CommentParser::parse($reflection->getDocComment());
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC +
            ReflectionMethod::IS_PROTECTED);
        foreach ($methods as $method) {
            $methodUrl = strtolower($method->getName());
            if ($methodUrl{0} == '_') {
                continue;
            }
            $doc = $method->getDocComment();
            $metadata = CommentParser::parse($doc) + $classMetadata;
            if (isset($metadata['access'])
                && $metadata['access'] == 'private'
            ) {
                continue;
            }
            $arguments = array();
            $defaults = array();
            $params = $method->getParameters();
            $position = 0;
            $ignorePathTill = false;
            $allowAmbiguity = !Defaults::$smartAutoRouting;
            $metadata['resourcePath'] = $resourcePath;
            if (isset($classMetadata['description'])) {
                $metadata['classDescription'] = $classMetadata['description'];
            }
            if (isset($classMetadata['classLongDescription'])) {
                $metadata['classLongDescription']
                    = $classMetadata['longDescription'];
            }
            if (!isset($metadata['param'])) {
                $metadata['param'] = array();
            }
            foreach ($params as $param) {
                $type =
                    $param->isArray() ? 'array' : $param->getClass();
                if ($type instanceof ReflectionClass) {
                    $type = $type->getName();
                }
                $arguments[$param->getName()] = $position;
                $defaults[$position] = $param->isDefaultValueAvailable() ?
                    $param->getDefaultValue() : null;
                if (!isset($metadata['param'][$position])) {
                    $metadata['param'][$position] = array();
                }
                $m = &$metadata ['param'] [$position];
                if (isset($type)) {
                    $m['type'] = $type;
                }
                $m ['name'] = trim($param->getName(), '$ ');
                $m ['default'] = $defaults [$position];
                $m ['required'] = !$param->isOptional();

                if (isset($m[CommentParser::$embeddedDataName]['from'])) {
                    $from = $m[CommentParser::$embeddedDataName]['from'];
                } else {
                    if (isset($type) && Util::isObjectOrArray($type)
                        || $param->getName() == Defaults::$fullRequestDataName
                    ) {
                        $from = 'body';
                    } elseif ($m['required']) {
                        $from = 'path';
                    } else {
                        $from = 'query';
                    }
                }
                $m['from'] = $from;

                if (!$allowAmbiguity && $from == 'path') {
                    $ignorePathTill = $position + 1;
                }
                $position++;
            }
            $accessLevel = 0;
            if ($method->isProtected()) {
                $accessLevel = 3;
            } elseif (isset($metadata['access'])) {
                if ($metadata['access'] == 'protected') {
                    $accessLevel = 2;
                } elseif ($metadata['access'] == 'hybrid') {
                    $accessLevel = 1;
                }
            } elseif (isset($metadata['protected'])) {
                $accessLevel = 2;
            }
            /*
            echo " access level $accessLevel for $className::"
            .$method->getName().$method->isProtected().PHP_EOL;
            */

            // take note of the order
            $call = array(
                'className' => $className,
                'path' => rtrim($resourcePath, '/'),
                'methodName' => $method->getName(),
                'arguments' => $arguments,
                'defaults' => $defaults,
                'metadata' => $metadata,
                'accessLevel' => $accessLevel,
            );
            // if manual route
            if (preg_match_all(
                '/@url\s+(GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS)'
                    . '[ \t]*\/?(\S*)/s',
                $doc, $matches, PREG_SET_ORDER
            )
            ) {
                foreach ($matches as $match) {
                    $httpMethod = $match[1];
                    $url = rtrim($resourcePath . $match[2], '/');
                    $this->_routes[$httpMethod][$url] = $call;
                }
                //if auto route enabled, do so
            } elseif (Defaults::$autoRoutingEnabled) {
                // not prefixed with underscore
                // no configuration found so use convention
                if (preg_match_all(
                    '/^(GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS)/i',
                    $methodUrl, $matches)
                ) {
                    $httpMethod = strtoupper($matches[0][0]);
                    $methodUrl = substr($methodUrl, strlen($httpMethod));
                } else {
                    $httpMethod = 'GET';
                }
                if ($methodUrl == 'index') {
                    $methodUrl = '';
                }
                $url = empty($methodUrl) ? rtrim($resourcePath, '/')
                    : $resourcePath . $methodUrl;
                if (!$ignorePathTill) {
                    $this->_routes[$httpMethod][$url] = $call;
                }
                $position = 1;
                foreach ($params as $param) {
                    if (($param->isOptional() && !$allowAmbiguity)
                        || $param->getName() == 'request_data'
                    ) {
                        break;
                    }
                    if (!empty($url)) {
                        $url .= '/';
                    }
                    $url .= '{' . $param->getName() . '}';
                    if ($allowAmbiguity || $position == $ignorePathTill) {
                        $this->_routes[$httpMethod][$url] = $call;
                    }
                    $position++;
                }
            }
        }
    }
}
