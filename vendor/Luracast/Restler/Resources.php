<?php
namespace Luracast\Restler;

use stdClass;

/**
 * API Class to create Swagger Spec 1.1 compatible id and operation
 * listing
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc3
 */
class Resources implements iUseAuthentication
{
    /**
     * @var bool should protected resources be shown to unauthenticated users?
     */
    public static $hideProtected = true;

    /**
     * @var bool should we use format as extension?
     */
    public static $useFormatAsExtension = true;

    /**
     * @var array all http methods specified here will be excluded from
     * documentation
     */
    public static $excludedHttpMethods = array('OPTIONS');

    /**
     * @var array all paths beginning with any of the following will be excluded
     * from documentation
     */
    public static $excludedPaths = array();

    /**
     * @var bool
     */
    public static $placeFormatExtensionBeforeDynamicParts = true;

    /**
     * @var null|callable if the api methods are under access control mechanism
     * you can attach a function here that returns true or false to determine
     * visibility of a protected api method. this function will receive method
     * info as the only parameter.
     */
    public static $accessControlFunction = null;

    public static $dataTypeAlias = array(
        'string' => 'string',
        'int' => 'int',
        'number' => 'float',
        'float' => 'float',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'NULL' => 'null',
        'array' => 'Array',
        'object' => 'Object',
        'stdClass' => 'Object',
        'mixed' => 'string',
		'DateTime' => 'Date'
    );

    /**
     * Injected at runtime
     *
     * @var Restler instance of restler
     */
    public $restler;
    public $formatString = '';

    private $_models;

    private $_bodyParam;

    private $crud = array('POST' => 'create', 'GET' => 'retrieve',
        'PUT' => 'update', 'DELETE' => 'delete', 'PATCH' => 'partial update');

    private $_authenticated = false;

    /**
     * This method will be called first for filter classes and api classes so
     * that they can respond accordingly for filer method call and api method
     * calls
     *
     *
     * @param bool $isAuthenticated passes true when the authentication is
     *                              done, false otherwise
     *
     * @return mixed
     */
    public function __setAuthenticationStatus($isAuthenticated = false)
    {
        $this->_authenticated = $isAuthenticated;
    }

    public function __construct()
    {
        if (static::$useFormatAsExtension) {
            $this->formatString = '.{format}';
        }
    }

    /**
     * @access hybrid
     *
     * @param int    $version
     * @param string $id
     *
     * @throws RestException
     * @return null|stdClass
     *
     * @url    GET {id}-v{version}
     * @url    GET v{version}
     */
    public function get($version, $id = '')
    {
        if (!Defaults::$useUrlBasedVersioning
            && $version != $this->restler->_requestedApiVersion
        ) {
            throw new RestException(404);
        }
        $this->_models = new stdClass();
        $r = null;
        $count = 0;

        $target = empty($id) ? "v$version" : "v$version/$id";

        foreach ($this->restler->routes as $httpMethod => $value) {
            if (in_array($httpMethod, static::$excludedHttpMethods)) {
                continue;
            }
            foreach ($value as $fullPath => $route) {
                if (0 !== strpos($fullPath, $target)) {
                    continue;
                }
                if (strlen($fullPath) != strlen($target) &&
                    0 !== strpos($fullPath, $target . '/')
                ) {
                    continue;
                }
                if (
                    self::$hideProtected
                    && !$this->_authenticated
                    && $route['accessLevel'] > 1
                ) {
                    continue;
                }
                foreach (static::$excludedPaths as $exclude) {
                    if (0 === strpos($fullPath, "v$version/$exclude")) {
                        continue 2;
                    }
                }
                $m = $route['metadata'];
                if ($id == '' && $m['resourcePath'] != "v$version/") {
                    continue;
                }
                if ($this->_authenticated
                    && static::$accessControlFunction
                    && (!call_user_func(
                        static::$accessControlFunction, $route['metadata']))
                ) {
                    continue;
                }
                $count++;
                $className = $this->_noNamespace($route['className']);
                if (!$r) {
                    $resourcePath = '/'
                        . trim($m['resourcePath'], '/');
                    if (!Defaults::$useUrlBasedVersioning) {
                        $resourcePath = str_replace("/v$version", '',
                            $resourcePath);
                    }
                    $r = $this->_operationListing($resourcePath);
                }
                $parts = explode('/', $fullPath);
                $pos = count($parts) - 1;
                if (count($parts) == 1 && $httpMethod == 'GET') {
                } else {
                    for ($i = 0; $i < count($parts); $i++) {
                        if ($parts[$i]{0} == '{') {
                            $pos = $i - 1;
                            break;
                        }
                    }
                }
                $nickname = preg_replace(
                    array('/[{]/', '/[^A-Za-z0-9-_]/'),
                    array('_', '-'),
                    implode('-', $parts));
                $parts[self::$placeFormatExtensionBeforeDynamicParts ? $pos : 0]
                    .= $this->formatString;
                // $parts[0] .= $this->formatString; //".{format}";
                if (!Defaults::$useUrlBasedVersioning) {
                    array_shift($parts);
                }
                $fullPath = implode('/', $parts);
                $description = isset(
                $m['classDescription'])
                    ? $m['classDescription']
                    : $className . ' API';
                $api = $this->_api("/$fullPath", $description);
                if (empty($m['description'])) {
                    $m['description'] = $this->restler->_productionMode
                        ? ''
                        : 'routes to <mark>'
                            . $route['className']
                            . '::'
                            . $route['methodName'] . '();</mark>';
                }
                if (empty($m['longDescription'])) {
                    $m['longDescription'] = $this->restler->_productionMode
                        ? ''
                        : 'Add PHPDoc long description to '
                            . "<mark>$className::"
                            . $route['methodName'] . '();</mark>'
                            . '  (the api method) to write here';
                }
                $operation = $this->_operation(
                    $nickname,
                    $httpMethod,
                    $m['description'],
                    $m['longDescription']
                );
                if (isset($m['throws'])) {
                    foreach ($m['throws'] as $exception) {
                        $operation->errorResponses[] = array(
                            'reason' => $exception['reason'],
                            'code' => $exception['code']);
                    }
                }
                if (isset($m['param'])) {
                    foreach ($m['param'] as $param) {
                        //combine body params as one
                        $p = $this->_parameter($param);
                        if ($p->paramType == 'body') {
                            $this->_appendToBody($p);
                        } else {
                            $operation->parameters[] = $p;
                        }
                    }
                }
                if (count($this->_bodyParam['description'])) {
                    $operation->parameters[] = $this->_getBody();
                }
                if (isset($m['return']['type'])) {
                    $responseClass = $m['return']['type'];
                    if (is_string($responseClass)) {
                        if (class_exists($responseClass)) {
                            $this->_model($responseClass);
                            $operation->responseClass
                                = $this->_noNamespace($responseClass);
                        } elseif (strtolower($responseClass) == 'array') {
                            $operation->responseClass = 'Array';
                            $rt = $m['return'];
                            if (isset(
                            $rt[CommentParser::$embeddedDataName]['type'])
                            ) {
                                $rt = $rt[CommentParser::$embeddedDataName]
                                ['type'];
                                if (class_exists($rt)) {
                                    $this->_model($rt);
                                    $operation->responseClass .= '[' .
                                        $this->_noNamespace($rt) . ']';
                                }

                            }
                        }
                    }
                }
                $api->operations[] = $operation;
                $r->apis[] = $api;
            }
        }
        if (!$count) {
            throw new RestException(404);
        }
        if (!is_null($r))
            $r->models = $this->_models;
        return $r;
    }

    /**
     * @access hybrid
     * @return \stdClass
     */
    public function index()
    {
        $r = $this->_resourceListing();
        $map = array();
        foreach ($this->restler->routes as $httpMethod => $routes) {
            if (in_array($httpMethod, static::$excludedHttpMethods)) {
                continue;
            }
            foreach ($routes as $fullPath => $route) {
                if (
                    self::$hideProtected
                    && !$this->_authenticated
                    && $route['accessLevel'] > 1
                ) {
                    continue;
                }
                $path = explode('/', $fullPath);

                $resource = isset($path[1]) ? $path[1] : '';

                $version = intval(substr($path[0], 1));

                if ($resource == 'resources'
                    || (!Defaults::$useUrlBasedVersioning
                        && $version != $this->restler->_requestedApiVersion)
                ) {
                    continue;
                }

                foreach (static::$excludedPaths as $exclude) {
                    if (0 === strpos($fullPath, "v$version/$exclude")) {
                        continue 2;
                    }
                }

                if ($this->_authenticated
                    && static::$accessControlFunction
                    && (!call_user_func(
                        static::$accessControlFunction, $route['metadata']))
                ) {
                    continue;
                }

                $resource = $resource ? $resource . "-v$version" : "v$version";

                if (empty($map[$resource])) {
                    $map[$resource] = isset(
                    $route['metadata']['classDescription'])
                        ? $route['metadata']['classDescription'] : '';
                }
            }
        }
        foreach ($map as $path => $description) {
            //add id
            $r->apis[] = array(
                'path' => "/resources/{$path}$this->formatString",
                'description' => $description
            );
        }
        return $r;
    }

    /**
     * Find the data type of the given value.
     *
     *
     * @param mixed $o              given value for finding type
     *
     * @param bool  $appendToModels if an object is found should we append to
     *                              our models list?
     *
     * @return string
     *
     * @access private
     */
    public function getType($o, $appendToModels = false)
    {
        if (is_object($o)) {
            $oc = get_class($o);
            if ($appendToModels) {
                $this->_model($oc, $o);
            }
            return $this->_noNamespace($oc);
        }
        if (is_array($o)) {
            if (count($o)) {
                $child = end($o);
                if (Util::isObjectOrArray($child)) {
                    $childType = $this->getType($child, $appendToModels);
                    return "Array[$childType]";
                }
            }
            return 'array';
        }
        if (is_bool($o)) return 'boolean';
        if (is_numeric($o)) return is_float($o) ? 'float' : 'int';
        return 'string';
    }

    private function _resourceListing()
    {
        $r = new stdClass();
        $r->apiVersion = (string)$this->restler->_apiVersion;
        $r->swaggerVersion = "1.1";
        $r->basePath = $this->restler->_baseUrl;
        $r->apis = array();
        return $r;
    }

    private function _operationListing($resourcePath = '/')
    {
        $r = $this->_resourceListing();
        $r->resourcePath = $resourcePath;
        $r->models = new stdClass();
        return $r;
    }

    private function _api($path, $description = '')
    {
        $r = new stdClass();
        $r->path = $path;
        $r->description =
            empty($description) && $this->restler->_productionMode
                ? 'Use PHPDoc comment to describe here'
                : $description;
        $r->operations = array();
        return $r;
    }

    private function _operation(
        $nickname,
        $httpMethod = 'GET',
        $summary = 'description',
        $notes = 'long description',
        $responseClass = 'void'
    )
    {
        //reset body params
        $this->_bodyParam = array(
            'required' => false,
            'description' => array()
        );

        $r = new stdClass();
        $r->httpMethod = $httpMethod;
        $r->nickname = $nickname;
        $r->responseClass = $responseClass;

        $r->parameters = array();

        $r->summary = $summary;
        $r->notes = $notes;

        $r->errorResponses = array();
        return $r;
    }

    private function _appendToBody($p)
    {
        if($p->name === Defaults::$fullRequestDataName)
        return;
        $this->_bodyParam['description'][$p->name]
            = "<mark>$p->name</mark>"
            . ($p->required ? ' <i>(required)</i>: ' : ': ')
            . $p->description;
        $this->_bodyParam['required'] = $p->required
            || $this->_bodyParam['required'];
        $this->_bodyParam['names'][$p->name] = true;
    }

    private function _getBody()
    {
        $r = new stdClass();
        $r->name = 'REQUEST_BODY';
        $p = array_values($this->_bodyParam['description']);
        $r->description = "Paste JSON data here";
        if (count($p) == 1
            && isset(
            $this->_bodyParam['description'][Defaults::$fullRequestDataName])
        ) {

        } else {
            $r->description .= " with the following"
                . (count($p) > 1 ? ' properties.' : ' property.')
                . '<hr/>'
                . implode("<hr/>", $p);
        }
        $r->paramType = 'body';
        $r->required = $this->_bodyParam['required'];
        $r->allowMultiple = false;
        $r->dataType = 'Object';
        unset($this->_bodyParam['names'][Defaults::$fullRequestDataName]);
        $r->defaultValue = "{\n    \""
            . implode("\": \"\",\n    \"", array_keys($this->_bodyParam['names']))
            . "\": \"\"\n}";
        return $r;
    }

    private function _parameter($param)
    {
        $r = new stdClass();
        $r->name = $param['name'];
        $r->description = !empty($param['description'])
            ? $param['description'] . '.'
            : ($this->restler->_productionMode
                ? ''
                : 'add <mark>@param {type} $' . $r->name
                    . ' {comment}</mark> to describe here');
        //paramType can be path or query or body or header
        $r->paramType = isset($param['from']) ? $param['from'] : 'query';
        $r->required = isset($param['required']) && $param['required'];
        if (isset($param['default'])) {
            $r->defaultValue = $param['default'];
        } elseif (isset($param[CommentParser::$embeddedDataName]['example'])) {
            $r->defaultValue
                = $param[CommentParser::$embeddedDataName]['example'];
        }
        $r->allowMultiple = false;
        $type = 'string';
        if (isset($param['type'])) {
            $type = $param['type'];
            if (is_array($type)) {
                $type = array_shift($type);
            }
            $type = isset(static::$dataTypeAlias[$type])
                ? static::$dataTypeAlias[$type]
                : $type;
        }
        $r->dataType = $type;
        if (isset($param[CommentParser::$embeddedDataName])) {
            $p = $param[CommentParser::$embeddedDataName];
            if (isset($p['min']) && isset($p['max'])) {
                $r->allowableValues = array(
                    'valueType' => 'RANGE',
                    'min' => $p['min'],
                    'max' => $p['max'],
                );
            } elseif (isset($p['choice'])) {
                $r->allowableValues = array(
                    'valueType' => 'LIST',
                    'values' => $p['choice']
                );
            }
        }
        return $r;
    }

    private function _model($className, $instance = null)
    {
        $properties = array();
        $reflectionClass = new \ReflectionClass($className);

        if (!$instance) {
            $instance = new $className();
        }
        $data = get_object_vars($instance);

        foreach ($data as $key => $value) {
            $propertyMetaData = null;

			try {
				$property = $reflectionClass->getProperty($key);

				if ($c = $property->getDocComment()) {
					$propertyMetaData = CommentParser::parse($c);
				}
			} catch (\ReflectionException $e) {}

            if ($propertyMetaData !== null) {
                $type = isset($propertyMetaData['var']) ? $propertyMetaData['var'] : 'string';
                $description = @$propertyMetaData['description'] ?: '';

				$type = explode(" ", $type);
				$type = array_shift($type);

                if (class_exists($type)) {
                    $this->_model($type);
                }
            } else {
                $type = $this->getType($value, true);
                $description = '';
            }

            if (isset(static::$dataTypeAlias[$type])) {
                $type = static::$dataTypeAlias[$type];
            }

            $properties[$key] = array(
                'type' => $type,
                'description' => $description
            );

            if ($type == 'Array') {
                $itemType = count($value)
                    ? $this->getType($value[0], true)
                    : 'string';
                $properties[$key]['item'] = array(
                    'type' => $itemType,
                    /*'description' => '' */ //TODO: add description
                );
            } else if (preg_match('/^Array\[(.+)\]$/', $type, $matches)) {
				$itemType = $matches[1];
				$properties[$key]['type'] = 'Array';
				$properties[$key]['item']['type'] = $itemType;

				if (class_exists($itemType)) {
					$this->_model($itemType);
				}
			}
        }

        if (!empty($properties)) {
            $id = $this->_noNamespace($className);
            $model = new stdClass();
            $model->id = $id;
            $model->properties = $properties;
            $this->_models->{$id} = $model;
        }
    }

    private function _noNamespace($className)
    {
		if (strpos($className, '\\') === false and strpos($className, '_') !== false) {
			$className = explode('_', $className);
		} else {
			$className = explode('\\', $className);
		}
        return end($className);
    }
}

