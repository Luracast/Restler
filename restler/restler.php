<?php
/**
 * REST API Server. It is the server part of the Restler framework.
 * Based on the RestServer code from <http://jacwright.com/blog/resources/RestServer.txt>
 *
 * @category   Framework
 * @package    restler
 * @author     Jac Wright <jacwright@gmail.com>
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    1.0.19 beta
 */

class Restler
{
	/**
	 * URL of the currently mapped service
	 * @var string
	 */
	public $url;

	/**
	 * Http request method of the current request.
	 * Any value between [GET, PUT, POST, DELETE]
	 * @var string
	 */
	public $request_method;

	/**
	 * Requested data format. Instance of the current format class
	 * which implements the iFormat interface
	 * @var iFormat
	 * @example jsonFormat, xmlFormat, yamlFormat etc
	 */
	public $request_format;

	/**
	 * Data sent to the service
	 * @var string
	 */
	public $request_data;

	/**
	 * Used in production mode to store the URL Map to disk
	 * @var string
	 */
	public $cache_dir = '.';

	/**
	 * Response data format. Instance of the current format class
	 * which implements the iFormat interface
	 * @var iFormat
	 * @example jsonFormat, xmlFormat, yamlFormat etc
	 */
	public $response_format;

	///////////////////////////////////////

	/**
	 * When set to false, it will run in debug mode and parse the
	 * class files every time to map it to the URL
	 * @var boolean
	 */
	protected $production_mode;


	/**
	 * Associated array that maps urls to their respective service and function
	 * @var array
	 */
	protected $url_map = array();

	/**
	 * Associated array that maps formats to their respective format class name
	 * @var array
	 */
	protected $format_map = array();

	/**
	 * Instance of the current api service class
	 * @var object
	 */
	protected $service_class;

	/**
	 * Name of the api method being called
	 * @var string
	 */
	protected $service_method;

	protected $auth_classes = array();
	protected $error_classes = array();

	/**
	 * Caching of url map is enabled or not
	 * @var boolean
	 */
	protected $cached;

	/**
	 * Constructor
	 * @param boolean $production_mode When set to false, it will run in
	 * debug mode and parse the class files every time to map it to the URL
	 */
	public function  __construct($production_mode = false)
	{
		$this->production_mode = $production_mode;
		$this->cache_dir = $this->cache_dir == '.' ? getcwd() : $this->cache_dir;
	}

	/**
	 * Store the url map cache if needed
	 */
	public function  __destruct()
	{
		if ($this->production_mode && !$this->cached) {
			if (function_exists('apc_store')) {
				apc_store('urlMap', $this->url_map);
			} else {
				file_put_contents($this->cache_dir . '/urlMap.cache', serialize($this->url_map));
			}
		}
	}

	/**
	 * Use it in production mode to refresh the url map cache
	 */
	public function refreshCache()
	{
		$this->url_map = array();
		$this->cached = false;
	}

	/**
	 * Call this method and pass all the formats that should be
	 * supported by the API. Accepts multiple parameters
	 * @param string class name of the format class (iFormat)
	 * @example $restler->setSupportedFormats('JsonFormat', 'XmlFormat'...);
	 */
	public function setSupportedFormats()
	{
		$args = func_get_args();
		foreach ($args as $class) {
			if (is_string($class) && !class_exists($class)){
				throw new Exception('Invalid format class');
			} elseif (!is_string($class) && !is_object($class)) {
				throw new Exception('Invalid format class; must be a classname or object');
			}
			/**
			 * Format Instance
			 * @var iFormat
			 */
			$obj = is_string($class) ? new $class() : $class;
			if(! $obj instanceof iFormat){
				throw new Exception('Invalid format class; must be implementing iFormat');
			}
			foreach ($obj->getMIMEMap() as $key => $value) {
				if(!isset($this->format_map[$key]))$this->format_map[$key]=$class;
				if(!isset($this->format_map[$value]))$this->format_map[$value]=$class;
			}
		}
		$this->format_map['default']=$args[0];
	}

	/**
	 * Add api classes throgh this function. All the public methods which have
	 * url comment will be exposed as the public api.
	 * All the protected methods with url comment will exposed as protected api
	 * which will require authentication
	 * @param string $class name of the service class
	 * @param string $basePath optional url prefix for mapping
	 * @throws Exception when supplied with invalid class name
	 */
	public function addAPIClass($class, $basePath = '')
	{
		$this->loadCache();
		if (!$this->cached) {
			if (is_string($class) && !class_exists($class)){
				throw new Exception('Invalid method or class');
			} elseif (!is_string($class) && !is_object($class)) {
				throw new Exception('Invalid method or class; must be a classname or object');
			}

			if (strlen($basePath) > 0 && $basePath[0] == '/') {
				$basePath = substr($basePath, 1);
			}
			if (strlen($basePath) > 0 && $basePath[strlen($basePath) - 1] != '/') {
				$basePath .= '/';
			}

			$this->generateMap($class, $basePath);
		}
	}

	/**
	 * protected methods will need atleast one authentication class to be set
	 * in order to allow that method to be executed
	 * @param string $class name of the authentication class
	 */
	public function addAuthenticationClass($class)
	{
		$this->auth_classes[] = $class;
		$this->addAPIClass($class);
	}

	/**
	 * Add class for custom error handling
	 * @param string $class name of the error handling class
	 */
	public function addErrorClass($class)
	{
		$this->errorClasses[] = $class;
	}

	/**
	 * Convenience method to respond with an error message
	 * @param int $statusCode http error code
	 * @param string $errorMessage optional custom error message
	 */
	public function handleError($statusCode, $errorMessage = null)
	{
		$method = "handle$statusCode";
		foreach ($this->error_classes as $class) {
			$obj = is_string($class) ? new $class() : $class;
			if (!method_exists($obj, $method)) {
				$obj->$method();
				return;
			}
		}
		$message = $this->codes[$statusCode] . (!$errorMessage || $this->production_mode ? '' : ': ' . $errorMessage);

		$this->setStatus($statusCode);
		$this->sendData(array('error' => array('code' => $statusCode, 'message' => $message)));
	}

	/**
	 * Main function for processing the api request
	 * and return the response
	 * @throws Exception when the api service class is missing
	 * @throws RestException to send error response
	 */
	public function handle()
	{
		$this->url = $this->getPath();
		$this->request_method = $this->getRequestMethod();

		if(empty($this->format_map))$this->setSupportedFormats('JsonFormat');
		$this->response_format = $this->getResponseFormat();
		$this->request_format = $this->getRequestFormat();
		if($this->request_format==null)$this->request_format = $this->response_format;
		//echo $this->request_format;

		if($this->request_method == 'PUT' || $this->request_method == 'POST')	{
			$this->request_data = $this->getRequestData();
		}
		list($class, $method, $params, $is_public) = $this->mapUrlToMethod();

		if($class) {
			if(is_string($class) && class_exists($class)){
				$this->service_class=$obj=new $class();
				$this->service_method=$method;
			}else{
				throw new Exception("Class $class does not exist");
			}
		}else{
			$this->handleError(404);
			return;
		}
		$obj->restler = $this;

		$pre_process = $this->request_format->getExtension().'_'.$method;
		if(method_exists($obj,$pre_process)) {
			call_user_func_array(array($obj, $pre_process), $params);
		}
		try {
			if($is_public) {
				$result = call_user_func_array(array($obj,$method), $params);
			}else{
				$auth_method = 'isAuthenticated';
				if(!count($this->auth_classes))throw new RestException(401);
				foreach ($this->auth_classes as $auth_class) {
					$auth_obj = is_string($auth_class) ? new $auth_class() : $auth_class;
					if (!method_exists($auth_obj, $auth_method) || !$auth_obj->$auth_method()) {
						throw new RestException(401);
					}
				}
				$reflection_method = new ReflectionMethod($class, $method);
				$reflection_method->setAccessible(true);
				$result = $reflection_method->invokeArgs($obj, $params);
			}
		} catch (RestException $e) {
			$this->handleError($e->getCode(), $e->getMessage());
		}
		if (isset($result) && $result !== null) {
			$this->sendData($result);
		}
	}

	/**
	 * Encodes the response in the prefered format
	 * and sends back
	 * @param $data array php data
	 */
	public function sendData($data)
	{
		$data =  $this->response_format->encode($data, !$this->production_mode);
		$post_process =  $this->service_method .'_'.$this->response_format->getExtension();
		if(isset($this->service_class) && method_exists($this->service_class,$post_process)){
			$data = call_user_method($post_process, $this->service_class, $data);
		}
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: 0");
		header('Content-Type: ' . $this->response_format->getMIME());
		echo $data;
	}

	/**
	 * Sets the HTTP response status
	 * @param int $code response code
	 */
	public function setStatus($code)
	{
		header("{$_SERVER['SERVER_PROTOCOL']} $code ".$this->codes[strval($code)]);
	}
	///////////////////////////////////////////////////////////////

	/**
	 * Parses the requst url and get the api path
	 * @return string api path
	 */
	protected function getPath()
	{
		$sn = trim($_SERVER['SCRIPT_NAME'],'/');
		$path = $_SERVER['REQUEST_URI'];
		if(strpos($path, $sn)===false){
			$sn = dirname($sn);
			if(count($sn)>1)
				$path = str_replace($sn, '', $path);
		}else{
			$path = str_replace($sn, '', $path);
		}
		$path = trim($path,'/');
		$path = preg_replace('/(\.\w+)|(\?.*$)/', '', $path);
		//echo $path;
		return $path;
	}

	/**
	 * Parses the request to figure out the http request type
	 * @return string which will be one of the following
	 * [GET, POST, PUT, DELETE]
	 * @example GET
	 */
	protected function getRequestMethod()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])){
			$method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}elseif ($method == 'POST' && isset($_GET['method'])){
			switch ($_GET['method']){
				case 'PUT':
				case 'DELETE':
					$method = $_GET['method'];
			}
		}
		return $method;
	}

	/**
	 * Parses the request to figure out format of the request data
	 * @return iFormat any class that implements iFormat
	 * @example JsonFormat
	 */
	protected function getRequestFormat(){
		$format=null;
		//check if client has sent any information on request format
		if(isset($_SERVER['CONTENT_TYPE'])){
			$mime = $_SERVER['CONTENT_TYPE'];
			if($mime==UrlEncodedFormat::MIME){
				$format = new UrlEncodedFormat();
			}else{
				if(isset($this->format_map[$mime])){
					$format = $this->format_map[$mime];
					$format = is_string($format) ? new $format: $format;
					$format->setMIME($accept);
					return $format;
				}
			}
		}
		return $format;
	}

	/**
	 * Parses the request to figure out the best format for response
	 * @return iFormat any class that implements iFormat
	 * @example JsonFormat
	 */
	protected function getResponseFormat()
	{
		//check if client has specified an extension
		/**
		* @var iFormat
		*/
		$format;
		$extension = array_pop(explode('.', parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)));
		if($extension && isset($this->format_map[$extension])){
			$format = $this->format_map[$extension];
			$format = is_string($format) ? new $format: $format;
			$format->setExtension($extension);
			//echo "Extension $extension";
			return $format;
		}
		//check if client has sent list of accepted data formats
		if(isset($_SERVER['HTTP_ACCEPT'])){
			$accepts = explode(',', $_SERVER['HTTP_ACCEPT']);
			foreach ($accepts as $accept) {
				if($extension && isset($this->format_map[$accept])){
					$format = $this->format_map[$accept];
					$format = is_string($format) ? new $format: $format;
					$format->setMIME($accept);
					//echo "MIME $accept";
					return $format;
				}
			}
		}
		$format = $this->format_map['default'];
		//echo "DEFAULT ".$this->format_map['default'];
		return is_string($format) ? new $format: $format;

	}

	/**
	 * Parses the request data and returns it
	 * @return array php data
	 */
	protected function getRequestData()
	{
		try{
			$r = file_get_contents('php://input');
			if(is_null($r))return $_GET;
			return $this->request_format->decode($r);
		} catch (RestException $e) {
			$this->handleError($e->getCode(), $e->getMessage());
		}
	}

	protected function loadCache()
	{
		if ($this->cached !== null) {
			return;
		}

		$this->cached = false;

		if ($this->production_mode) {
			if (function_exists('apc_fetch')) {
				$map = apc_fetch('urlMap');
			} elseif (file_exists($this->cache_dir . '/urlMap.cache')) {
				$map = unserialize(file_get_contents($this->cache_dir . '/urlMap.cache'));
			}
			if (isset($map) && is_array($map)) {
				$this->url_map = $map;
				$this->cached = true;
			}
		} else {
			if (function_exists('apc_store')) {
				apc_delete('urlMap');
			} else {
				@unlink($this->cache_dir . '/urlMap.cache');
			}
		}
	}


	protected function mapUrlToMethod()
	{
		if(!isset($this->url_map[$this->request_method])){
			return array(null,null,null,null,null,null);
		}
		$urls = $this->url_map[$this->request_method];
		if (!$urls)return array(null,null,null,null,null,null);

		$found=false;

		foreach ($urls as $url => $call) {
			$params = array('data'=>$this->request_data);
			if(is_array($this->request_data))$params+=$this->request_data;
			//use query parameters
			$params+=$_GET;
			$args = $call[2];
			//if it has url based parameters
			if (strstr($url, ':')) {
				$regex = preg_replace('/\\\:([^\/]+)/', '(?P<$1>[^/]+)', preg_quote($url));
				if (preg_match(":^$regex$:", $this->url, $matches)) {
					foreach ($matches as $arg => $match) {
						//echo "$arg => $match $args[$arg] \n";
						if (isset($args[$arg]))$params[$arg] = $match;
					}
					$found=true;
					break;
				}
			}elseif ($url == $this->url){
				$found=true;
				break;
			}
		}
		if($found){
			$p = is_null($call[5]) ? array() : $call[5];
			foreach ($args as $key => $value) {
				//echo "$key => $value \n";
				if(isset($params[$key]))$p[$value] = $params[$key];
			}
			$call[2]=$p;
			return $call;

		}
	}

	protected function generateMap($class, $basePath = '')
	{
		if (is_object($class)) {
			$reflection = new ReflectionObject($class);
		} elseif (class_exists($class)) {
			$reflection = new ReflectionClass($class);
		}

		$methods = $reflection->getMethods(
		ReflectionMethod::IS_PUBLIC +
		ReflectionMethod::IS_PROTECTED
		);

		foreach ($methods as $method) {
			$doc = $method->getDocComment();
			if (preg_match_all('/@url\s+(GET|POST|PUT|DELETE|HEAD|OPTIONS)[ \t]*\/?(\S*)/s', $doc, $matches, PREG_SET_ORDER)) {

				$params = $method->getParameters();

				foreach ($matches as $match) {
					$httpMethod = $match[1];
					$url = $basePath . $match[2];
					if (strlen($url)>0 && $url[strlen($url) - 1] == '/') {
						$url = substr($url, 0, -1);
					}
					$call = array($class, $method->getName());
					$args = array();
					$defaults = array();
					$optional_index = $method->getNumberOfRequiredParameters();
					foreach ($params as $param){
						$args[$param->getName()] = $param->getPosition();
						if($param->isDefaultValueAvailable()){
							$defaults[$param->getPosition()]=$param->getDefaultValue();
						}
					}
					$call[] = $args;
					$call[] = $method->isPublic();
					$call[] = @$optional_index;
					$call[] = $defaults;

					$this->url_map[$httpMethod][$url] = $call;
				}
			}
		}
	}

	private $codes = array(
	100 => 'Continue',
	101 => 'Switching Protocols',
	200 => 'OK',
	201 => 'Created',
	202 => 'Accepted',
	203 => 'Non-Authoritative Information',
	204 => 'No Content',
	205 => 'Reset Content',
	206 => 'Partial Content',
	300 => 'Multiple Choices',
	301 => 'Moved Permanently',
	302 => 'Found',
	303 => 'See Other',
	304 => 'Not Modified',
	305 => 'Use Proxy',
	306 => '(Unused)',
	307 => 'Temporary Redirect',
	400 => 'Bad Request',
	401 => 'Unauthorized',
	402 => 'Payment Required',
	403 => 'Forbidden',
	404 => 'Not Found',
	405 => 'Method Not Allowed',
	406 => 'Not Acceptable',
	407 => 'Proxy Authentication Required',
	408 => 'Request Timeout',
	409 => 'Conflict',
	410 => 'Gone',
	411 => 'Length Required',
	412 => 'Precondition Failed',
	413 => 'Request Entity Too Large',
	414 => 'Request-URI Too Long',
	415 => 'Unsupported Media Type',
	416 => 'Requested Range Not Satisfiable',
	417 => 'Expectation Failed',
	500 => 'Internal Server Error',
	501 => 'Not Implemented',
	502 => 'Bad Gateway',
	503 => 'Service Unavailable',
	504 => 'Gateway Timeout',
	505 => 'HTTP Version Not Supported'
	);
}
/**
 * Special Exception for raising API errors
 * that can be used in API methods
 * @category   Framework
 * @package    restler
 * @subpackage exception
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class RestException extends Exception
{

	public function __construct($code, $message = null)
	{
		parent::__construct($message, $code);
	}

}

/**
 * Conveniance function that converts the given object
 * in to associative array
 * @param object $object that needs to be converted
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
function object_to_array($object, $utf_encode=true)
{
	if(is_array($object) || is_object($object))
	{
		$array = array();
		foreach($object as $key => $value)
		{
			$value = object_to_array($value, $utf_encode);
			if($utf_encode && is_string($value)){
				$value = utf8_encode($value);
			}
			$array[$key] = $value;
		}
		return $array;
	}
	return $object;
}

/**
 * Interface for creating authentication classes
 * @category   Framework
 * @package    restler
 * @subpackage auth
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
interface iAuthenticate
{
	/**
	 * Auth function that is called when a protected method is requested
	 * @return boolean true or false
	 */
	public function isAuthenticated();
}


/**
 * Interface for creating custom data formats
 * like xml, json, yaml, amf etc
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
interface iFormat
{
	/**
	 * Get Extension => MIME type mappings as an associative array
	 * @return array list of mime strings for the format
	 * @example array('json'=>'application/json');
	 */
	public function getMIMEMap();

	/**
	 * Set the selected MIME type
	 * @param string $mime MIME type
	 */
	public function setMIME($mime);
	/**
	 * Get selected MIME type
	 */
	public function getMIME();

	/**
	 * Set the selected file extension
	 * @param string $extension file extension
	 */
	public function setExtension($extension);
	/**
	 * Get the selected file extension
	 * @return string file extension
	 */
	public function getExtension();


	/**
	 * Encode the given data in the format
	 * @param array $data resulting data that needs to
	 * be encoded in the given format
	 * @param boolean $human_readable set to true when restler
	 * is not running in production mode. Formatter has to
	 * make the encoded output more human readable
	 * @return string encoded string
	 */
	public function encode($data, $human_readable=false);

	/**
	 * Decode the given data from the format
	 * @param string $data data sent from client to
	 * the api in the given format.
	 * @return array associative array of the parsed data
	 */
	public function decode($data);
}

/**
 * URL Encoded String Format
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class UrlEncodedFormat implements iFormat
{
	const MIME = 'application/x-www-form-urlencoded';
	const EXTENSION = 'post';
	public function getMIMEMap()
	{
		return array(UrlEncodedFormat::EXTENSION=>UrlEncodedFormat::MIME);
	}
	public function getMIME(){
		return  UrlEncodedFormat::MIME;
	}
	public function getExtension(){
		return UrlEncodedFormat::EXTENSION;
	}
	public function setMIME($mime){
		//do nothing
	}
	public function setExtension($extension){
		//do nothing
	}
	public function encode($data, $human_readable=false){
		return http_build_query($data);
	}
	public function decode($data){
		parse_str($data,$r);
		return $r;
	}
	public function __toString(){
		return $this->getExtension();
	}
}

/**
 * Javascript Object Notation Format
 * @category   Framework
 * @package    restler
 * @subpackage format
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 */
class JsonFormat implements iFormat
{
	const MIME ='application/json';
	const EXTENSION = 'json';
	public function getMIMEMap()
	{
		return array(JsonFormat::EXTENSION=>JsonFormat::MIME);
	}
	public function getMIME(){
		return  JsonFormat::MIME;
	}
	public function getExtension(){
		return JsonFormat::EXTENSION;
	}
	public function setMIME($mime){
		//do nothing
	}
	public function setExtension($extension){
		//do nothing
	}
	public function encode($data, $human_readable=false){
		return $human_readable ? $this->json_format(json_encode(object_to_array($data))) : json_encode(object_to_array($data));
	}
	public function decode($data){
		return json_decode($data);
	}

	/**
	 * Pretty print JSON string
	 * @param string $json
	 * @return string formated json
	 */
	private function json_format($json)
	{
		$tab = "  ";
		$new_json = "";
		$indent_level = 0;
		$in_string = false;

		$len = strlen($json);

		for($c = 0; $c < $len; $c++) {
			$char = $json[$c];
			switch($char) {
				case '{':
				case '[':
					if(!$in_string) {
						$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
						$indent_level++;
					} else {
						$new_json .= $char;
					}
					break;
				case '}':
				case ']':
					if(!$in_string) {
						$indent_level--;
						$new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
					} else {
						$new_json .= $char;
					}
					break;
				case ',':
					if(!$in_string) {
						$new_json .= ",\n" . str_repeat($tab, $indent_level);
					} else {
						$new_json .= $char;
					}
					break;
				case ':':
					if(!$in_string) {
						$new_json .= ": ";
					} else {
						$new_json .= $char;
					}
					break;
				case '"':
					if($c > 0 && $json[$c-1] != '\\') {
						$in_string = !$in_string;
					}
				default:
					$new_json .= $char;
					break;
			}
		}

		return $new_json;
	}

	public function __toString(){
		return $this->getExtension();
	}
}