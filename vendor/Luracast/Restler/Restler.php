<?php
namespace Luracast\Restler;

use stdClass;
use Reflection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use InvalidArgumentException;
use Luracast\Restler\Format\iFormat;
use Luracast\Restler\Format\JsonFormat;
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
 * @version    3.0.0rc3
 */
class Restler extends EventEmitter
{

    // ==================================================================
    //
    // Public variables
    //
    // ------------------------------------------------------------------

    const VERSION = '3.0.0rc3';

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
     * method information including metadata
     *
     * @var stdClass
     */
    public $apiMethodInfo;

    /**
     * Associated array that maps urls to their respective class and method
     *
     * @var array
     */
    public $routes = array();

    /**
     * Response data format.
     *
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
     * Base URL currently being used
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * When set to false, it will run in debug mode and parse the
     * class files every time to map it to the URL
     *
     * @var boolean
     */
    protected $productionMode;

    /**
     * Associated array that maps formats to their respective format class name
     *
     * @var array
     */
    protected $formatMap = array();

    /**
     * Instance of the current api service class
     *
     * @var object
     */
    protected $apiClassInstance;

    /**
     * Name of the api method being called
     *
     * @var string
     */
    protected $apiMethod;

    /**
     * list of filter classes
     *
     * @var array
     */
    protected $filterClasses = array();
    protected $filterObjects = array();

    /**
     * list of authentication classes
     *
     * @var array
     */
    protected $authClasses = array();

    /**
     * list of error handling classes
     *
     * @var array
     */
    protected $errorClasses = array();

    /**
     * Caching of url map is enabled or not
     *
     * @var boolean
     */
    protected $cached;

    protected $apiVersion = 1;
    protected $requestedApiVersion = 1;
    protected $apiMinimumVersion = 1;

    protected $log = array();
    protected $startTime;
    protected $authenticated = false;

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
        Util::$restler = $this;
        $this->productionMode = $productionMode;
        if (is_null(Defaults::$cacheDirectory)) {
            Defaults::$cacheDirectory = dirname($_SERVER['SCRIPT_FILENAME']) .
                DIRECTORY_SEPARATOR . 'cache';
        }
        $this->cache = new Defaults::$cacheClass();
        // use this to rebuild cache every time in production mode
        if ($productionMode && $refreshCache) {
            $this->cached = false;
        }
    }

    /**
     * Store the url map cache if needed
     */
    public function __destruct()
    {
        if ($this->productionMode && !$this->cached) {
            $this->cache->set('routes', $this->routes);
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
        if ($version <= intval(self::VERSION) && $version > 0) {
            require_once "restler{$version}.php";
            return;
        }
        throw new \OutOfRangeException();
    }

    /**
     * @param int         $version         maximum version number supported
     *                                     by  the api
     * @param int         $minimum         minimum version number supported
     * (optional)
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function setAPIVersion($version = 1, $minimum = 1)
    {
        if (!is_int($version) && $version < 1) {
            throw new InvalidArgumentException
            ('version should be an integer greater than 0');
        }
        $this->apiVersion = $version;
        if (is_int($minimum)) {
            $this->apiMinimumVersion = $minimum;
        }
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
     * @throws Exception
     */
    public function setSupportedFormats($format = null /*[, $format2...$farmatN]*/)
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
                if (!isset($this->formatMap[$extension]))
                    $this->formatMap[$extension] = $className;
                if (!isset($this->formatMap[$mime]))
                    $this->formatMap[$mime] = $className;
                $extensions[".$extension"] = true;
            }
        }
        $this->formatMap['default'] = $args[0];
        $this->formatMap['extensions'] = array_keys($extensions);
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
     * @throws Exception when supplied with invalid class name
     */
    public function addAPIClass($className, $resourcePath = null)
    {
        $this->loadCache();
        if (!$this->cached) {
            $foundClass = array();
            if (class_exists($className)) {
                $foundClass[$className] = $className;
            }

            //versioned api
            if (false !== ($index = strrpos($className, '\\'))) {
                $name = substr($className, 0, $index)
                    . '\\v{$version}' . substr($className, $index);
            } else if (false !== ($index = strrpos($className, '_'))) {
                $name = substr($className, 0, $index)
                    . '_v{$version}' . substr($className, $index);
            } else {
                $name = 'v{$version}\\' . $className;
            }

            for ($version = $this->apiMinimumVersion;
                 $version <= $this->apiVersion;
                 $version++) {

                $versionedClassName = str_replace('{$version}', $version,
                    $name);
                if (class_exists($versionedClassName)) {
                    $this->generateMap($versionedClassName,
                        Util::getResourcePath(
                            $className,
                            $resourcePath,
                            "v{$version}/"
                        )
                    );
                    $foundClass[$className] = $versionedClassName;
                } elseif (isset($foundClass[$className])) {
                    $this->generateMap($foundClass[$className],
                        Util::getResourcePath(
                            $className,
                            $resourcePath,
                            "v{$version}/"
                        )
                    );
                }
            }

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
        $this->filterClasses[] = $className;
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
        $this->authClasses[] = $className;
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
        $this->errorClasses[] = $className;
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
        foreach ($this->errorClasses as $className) {
            if (method_exists($className, $method)) {
                $obj = new $className ();
                $obj->restler = $this;
                $obj->$method ();
                $handled = true;
            }
        }
        if ($handled)
            return null;
        if (!isset($this->responseFormat))
            $this->responseFormat = new JsonFormat();
        $this->sendData(null, $statusCode, $errorMessage);
    }

    /**
     * An initialize function to allow use of the restler error generation
     * functions for pre-processing and pre-routing of requests.
     */
    public function init()
    {
        if (Defaults::$crossOriginResourceSharing
            && $this->requestMethod == 'OPTIONS'
        ) {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header('Access-Control-Allow-Methods: '
                    . Defaults::$accessControlAllowMethods);

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header('Access-Control-Allow-Headers: '
                    . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
            exit(0);
        }
        if (empty($this->formatMap)) {
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
        if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
            $found = false;
            $charList = Util::sortByPriority($_SERVER['HTTP_ACCEPT_CHARSET']);
            foreach ($charList as $charset => $quality) {
                if (in_array($charset, Defaults::$supportedCharsets)) {
                    $found = true;
                    Defaults::$charset = $charset;
                    break;
                }
            }
            if (!$found) {
                if (strpos($_SERVER['HTTP_ACCEPT_CHARSET'], '*') !== false) {
                    //use default charset
                } else {
                    $this->handleError(406, 'Content negotiation failed. '
                        . "Requested charset is not supported");
                }
            }
        }
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $found = false;
            $langList = Util::sortByPriority($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($langList as $lang => $quality) {
                foreach (Defaults::$supportedLanguages as $supported) {
                    if (strcasecmp($supported, $lang) == 0) {
                        $found = true;
                        Defaults::$language = $supported;
                        break 2;
                    }
                }
            }
            if (!$found) {
                if (strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], '*') !== false) {
                    //use default language
                } else {
                    //ignore
                }
            }
        }
    }

    /**
     * Main function for processing the api request
     * and return the response
     *
     * @throws Exception     when the api service class is missing
     * @throws RestException to send error response
     */
    public function handle()
    {
        try {
            $this->init();
            foreach ($this->filterClasses as $filterClass) {
                /**
                 * @var iFilter
                 */
                $filterObj = new $filterClass;
                $filterObj->restler = $this;
                if (!$filterObj instanceof iFilter) {
                    throw new RestException (
                        500, 'Filter Class ' .
                        'should implement iFilter');
                } else {
                    $ok = $filterObj->__isAllowed();
                    if (is_null($ok)
                        && $filterObj instanceof iUseAuthentication
                    ) {
                        //handle at authentication stage
                        $this->filterObjects[] = $filterObj;
                        continue;
                    }
                    throw new RestException(403); //Forbidden
                }
            }
            Util::setProperties(
                get_class($this->requestFormat),
                null, $this->requestFormat
            );

            $this->requestData = $this->getRequestData();

            //parse defaults
            foreach ($_GET as $key => $value) {
                if (isset(Defaults::$aliases[$key])) {
                    $_GET[Defaults::$aliases[$key]] = $value;
                    unset($_GET[$key]);
                    $key = Defaults::$aliases[$key];
                }
                if (in_array($key, Defaults::$overridables)) {
                    Defaults::setProperty($key, $value);
                }
            }

            $this->apiMethodInfo = $o = $this->mapUrlToMethod();
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
                    $accessLevel = max(Defaults::$apiAccessLevel,
                        $o->accessLevel);
                    if ($accessLevel || count($this->filterObjects)) {
                        if (!count($this->authClasses)) {
                            throw new RestException(401);
                        }
                        foreach ($this->authClasses as $authClass) {
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
                        $this->authenticated = true;
                    }
                } catch (RestException $e) {
                    if ($accessLevel > 1) { //when it is not a hybrid api
                        $this->handleError($e->getCode(), $e->getMessage());
                    } else {
                        $this->authenticated = false;
                    }
                }
                try {
                    foreach ($this->filterObjects as $filterObj) {
                        Util::setProperties(get_class($filterObj),
                            $o->metadata,
                            $filterObj);
                    }
                    $preProcess = '_' . $this->requestFormat->getExtension() .
                        '_' . $o->methodName;
                    $this->apiMethod = $o->methodName;
                    $object = $this->apiClassInstance = null;
                    // TODO:check if the api version requested is allowed by class
                    if (Defaults::$autoValidationEnabled) {
                        foreach ($o->metadata['param'] as $index => $param) {
                            $info = &$param [CommentParser::$embeddedDataName];
                            if (!isset ($info['validate'])
                                || $info['validate'] != false
                            ) {
                                if (isset($info['method'])) {
                                    if (!isset($object)) {
                                        $object = $this->apiClassInstance
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
                    }
                    if (!isset($object)) {
                        $object = $this->apiClassInstance
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
        } catch (RestException $e) {
            $this->handleError($e->getCode(), $e->getMessage());
        } catch (\Exception $e) {
            $this->log[] = $e->getMessage();
            if ($this->productionMode) {
                $this->handleError(500);
            } else {
                $this->handleError(500, $e->getMessage());
            }
        }
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
        //$this->log []= ob_get_clean ();
        //only GET method should be cached if allowed by API developer
        $expires = $this->requestMethod == 'GET' ? Defaults::$headerExpires : 0;
        $cacheControl = Defaults::$headerCacheControl[0];
        if ($expires > 0) {
            $cacheControl = $this->apiMethodInfo->accessLevel
                ? 'private, ' : 'public, ';
            $cacheControl .= end(Defaults::$headerCacheControl);
            $cacheControl = str_replace('{expires}', $expires, $cacheControl);
            $expires = gmdate('D, d M Y H:i:s \G\M\T', time() + $expires);
        }
        @header('Cache-Control: ' . $cacheControl);
        @header('Expires: ' . $expires);
        @header('X-Powered-By: Luracast Restler v' . Restler::VERSION);

        if (Defaults::$crossOriginResourceSharing
            && isset($_SERVER['HTTP_ORIGIN'])
        ) {
            header('Access-Control-Allow-Origin: ' .
                    (Defaults::$accessControlAllowOrigin == '*'
                        ? $_SERVER['HTTP_ORIGIN']
                        : Defaults::$accessControlAllowOrigin)
            );
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if (isset($this->apiMethodInfo->metadata['header'])) {
            foreach ($this->apiMethodInfo->metadata['header'] as $header)
                @header($header, true);
        }

        /**
         *
         * @var iRespond DefaultResponder
         */
        $responder = Util::setProperties(
            Defaults::$responderClass,
            isset($this->apiMethodInfo->metadata)
                ? $this->apiMethodInfo->metadata
                : null
        );
        $this->responseFormat->setCharset(Defaults::$charset);
        $charset = $this->responseFormat->getCharset()
            ? : Defaults::$charset;
        @header('Content-Type: ' . (
            Defaults::$useVendorMIMEVersioning
                ? 'application/vnd.'
                . Defaults::$apiVendor
                . "-v{$this->requestedApiVersion}"
                . '+' . $this->responseFormat->getExtension()
                : $this->responseFormat->getMIME())
                . '; charset=' . $charset
        );
        @header('Content-Language: ' . Defaults::$language);
        if ($statusCode == 0) {
            if (isset($this->apiMethodInfo->metadata['status'])) {
                $this->setStatus($this->apiMethodInfo->metadata['status']);
            }
            $data = $responder->formatResponse($data);
            $data = $this->responseFormat->encode($data,
                !$this->productionMode);
            $postProcess = '_' . $this->apiMethod . '_' .
                $this->responseFormat->getExtension();
            if (isset($this->apiClassInstance)
                && method_exists(
                    $this->apiClassInstance,
                    $postProcess
                )
            ) {
                $data = call_user_func(array(
                    $this->apiClassInstance,
                    $postProcess
                ), $data);
            }
        } else {
            $message = RestException::$codes[$statusCode] .
                (empty($statusMessage) ? '' : ': ' . $statusMessage);
            $this->setStatus($statusCode);
            $data = $this->responseFormat->encode(
                $responder->formatError($statusCode, $message),
                !$this->productionMode);
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
     * @param string $name name of the hidden property
     *
     * @return null|mixed
     */
    public function __get($name)
    {
        if ($name{0} == '_') {
            $hiddenProperty = substr($name, 1);
            if (isset($this->$hiddenProperty)) {
                return $this->$hiddenProperty;
            }
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
        $path = str_replace($this->formatMap['extensions'], '', $path);
        if (Defaults::$useUrlBasedVersioning
            && strlen($path) && $path{0} == 'v'
        ) {
            $version = intval(substr($path, 1));
            if ($version && $version <= $this->apiVersion) {
                $this->requestedApiVersion = $version;
                $path = explode('/', $path, 2);
                $path = $path[1];
            }
        } elseif ($this->apiVersion)
            $this->requestedApiVersion = $this->apiVersion;

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
        if (!empty($_SERVER['CONTENT_TYPE'])) {
            $mime = $_SERVER['CONTENT_TYPE'];
            if (false !== $pos = strpos($mime, ';')) {
                $mime = substr($mime, 0, $pos);
            }
            if ($mime == UrlEncodedFormat::MIME)
                $format = new UrlEncodedFormat ();
            elseif (isset($this->formatMap[$mime])) {
                $format = $this->formatMap[$mime];
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
            if ($extension && isset($this->formatMap[$extension])) {
                $format = $this->formatMap[$extension];
                $format = is_string($format) ? new $format () : $format;
                $format->setExtension($extension);
                // echo "Extension $extension";
                return $format;
            }
        }
        // check if client has sent list of accepted data formats
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $acceptList = Util::sortByPriority($_SERVER['HTTP_ACCEPT']);
            foreach ($acceptList as $accept => $quality) {
                if (isset($this->formatMap[$accept])) {
                    $format = $this->formatMap[$accept];
                    $format = is_string($format) ? new $format : $format;
                    //TODO: check if the string verfication above is needed
                    $format->setMIME($accept);
                    //echo "MIME $accept";
                    // Tell cache content is based on Accept header
                    @header('Vary: Accept');

                    return $format;
                } elseif (false !== ($index = strrpos($accept, '+'))) {
                    $mime = substr($accept, 0, $index);
                    if (is_string(Defaults::$apiVendor)
                        && 0 === strpos($mime,
                            'application/vnd.'
                                . Defaults::$apiVendor . '-v')
                    ) {
                        $extension = substr($accept, $index + 1);
                        if (isset($this->formatMap[$extension])) {
                            //check the MIME and extract version
                            $version = intVal(substr($mime,
                                18 + strlen(Defaults::$apiVendor)));
                            if ($version > 0 && $version <= $this->apiVersion) {
                                $this->requestedApiVersion = $version;
                                $format = $this->formatMap[$extension];
                                $format = is_string($format)
                                    ? new $format ()
                                    : $format;
                                $format->setExtension($extension);
                                // echo "Extension $extension";
                                Defaults::$useVendorMIMEVersioning = true;
                                @header('Vary: Accept');

                                return $format;
                            }
                        }
                    }

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
                $format = $this->formatMap['default'];
                $format = new $format;
            }
        }
        if (empty($format)) {
            // RFC 2616: If an Accept header field is present, and if the
            // server cannot send a response which is acceptable according to
            // the combined Accept field value, then the server SHOULD send
            // a 406 (not acceptable) response.
            $format = $this->formatMap['default'];
            $format = new $format;
            $this->responseFormat = $format;
            $this->handleError(406, 'Content negotiation failed. '
                . 'Try \'' . $format->getMIME() . '\' instead.');
        } else {
            // Tell cache content is based at Accept header
            @header("Vary: Accept");
            return $format;
        }
    }

    /**
     * Parses the request data and returns it
     *
     * @return array php data
     */
    public function getRequestData()
    {
        if ($this->requestMethod == 'PUT'
            || $this->requestMethod == 'PATCH'
            || $this->requestMethod == 'POST'
        ) {
            if (!empty($this->requestData)) {
                return $this->requestData;
            }
            try {
                $r = file_get_contents('php://input');
                if (is_null($r)) {
                    return array();
                }
                $r = $this->requestFormat->decode($r);
                return is_null($r) ? array() : $r;
            } catch (RestException $e) {
                $this->handleError($e->getCode(), $e->getMessage());
            }
        }
        return array();
    }

    /**
     * Find the api method to execute for the requested Url
     *
     * @return \stdClass
     */
    public function mapUrlToMethod()
    {
        if (!isset($this->routes[$this->requestMethod])) {
            return new stdClass ();
        }
        $urls = $this->routes[$this->requestMethod];
        if (!$urls) {
            return new stdClass ();
        }
        $found = false;
        if (!is_array($this->requestData)) {
            $this->requestData = array(
                Defaults::$fullRequestDataName => $this->requestData
            );
            $this->requestData += $_GET;
            $params = $this->requestData;
        } else {
            $this->requestData += $_GET;
            $params = array(
                Defaults::$fullRequestDataName => $this->requestData
            );
            $params = $this->requestData + $params;

        }
        $call = new stdClass;
        $currentUrl = "v{$this->requestedApiVersion}/{$this->url}";
        $lc = strtolower($currentUrl);
        foreach ($urls as $url => $call) {
            $this->trigger('onRoute', array('url' => $url, 'target' => $call));
            $call = (object)$call;
            if (strstr($url, '{')) {
                $regex = str_replace(array('{', '}'),
                    array('(?P<', '>[^/]+)'), $url);
                if (preg_match(":^$regex$:i", $currentUrl, $matches)) {
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
                if (preg_match(":^$regex$:i", $currentUrl, $matches)) {
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
        if ($this->cached !== null)
            return null;
        $this->cached = false;
        if ($this->productionMode) {
            $routes = $this->cache->get('routes');
            if (isset($routes) && is_array($routes)) {
                $this->routes = $routes;
                $this->cached = true;
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
            //method name should not begin with _
            if ($methodUrl{0} == '_') {
                continue;
            }
            $doc = $method->getDocComment();
            $metadata = CommentParser::parse($doc) + $classMetadata;
            //@access should not be private
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
            $allowAmbiguity
                = (isset($metadata['smart-auto-routing'])
                && $metadata['smart-auto-routing'] != 'true')
                || !Defaults::$smartAutoRouting;
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
                    if ((isset($type) && Util::isObjectOrArray($type))
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
                    $this->routes[$httpMethod][$url] = $call;
                }
                //if auto route enabled, do so
            } elseif (Defaults::$autoRoutingEnabled) {
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
                    $this->routes[$httpMethod][$url] = $call;
                }
                $position = 1;
                foreach ($params as $param) {
                    $from = $metadata ['param'] [$position - 1] ['from'];

                    if ($from == 'body' && ($httpMethod == 'GET' ||
                        $httpMethod == 'DELETE')
                    ) {
                        $from = $metadata ['param'] [$position - 1] ['from']
                            = 'query';
                    }

                    if (!$allowAmbiguity && $from != 'path') {
                        break;
                    }
                    if (!empty($url)) {
                        $url .= '/';
                    }
                    $url .= '{' . $param->getName() . '}';
                    if ($allowAmbiguity || $position == $ignorePathTill) {
                        $this->routes[$httpMethod][$url] = $call;
                    }
                    $position++;
                }
            }
        }
    }
}

