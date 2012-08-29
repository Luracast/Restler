<?php
namespace Luracast\Restler;

use stdClass;

/**
 * Describe the purpose of this class/interface/trait
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0
 */
class Resources
{
    /**
     * Injected at runtime
     *
     * @var Restler instance of restler
     */
    public $restler;

    private $_models;

    public function index()
    {
        $r = $this->_resourceListing();
        $mappedResource = array();
        foreach ($this->restler->routes as $verb => $routes) {
            foreach ($routes as $route) {
                $path = $route['path'];
                $name = strtolower(str_replace('\\', '-', $route['className']));
                $classDescription = isset(
                $route['metadata']['classDescription'])
                    ? $route['metadata']['classDescription']
                    : $route['className'] . ' API';
                if (
                    !isset($mappedResource[$path])
                    && FALSE === strpos($path, 'resources')
                ) {
                    //add resource
                    $r->apis[] = array(
                        'path' => "/resources/$name.{format}",
                        'description' => $classDescription
                    );
                }
                $mappedResource[$path] = TRUE;
            }
        }
        return $r;
    }

    public function get($name)
    {
        $this->_models = new stdClass();
        $r = null;
        $name = strtolower(str_replace('-', '\\', $name));
        $count = 0;
        foreach ($this->restler->routes as $httpMethod => $value) {
            foreach ($value as $key => $route) {
                if (0 == strcasecmp($name, $route['className'])) {
                    $count++;
                    $className = $this->_noNamespace($route['className']);
                    $m = $route['metadata'];
                    if (!$r) {
                        $resourcePath = '/'
                            . trim($m['resourcePath'], '/');
                        $r = $this->_operationListing($resourcePath);
                    }
                    $parts = explode('/', $key);
                    if (count($parts) == 1 && $httpMethod == 'GET')
                        continue;
                    for ($i = 0; $i < count($parts); $i++) {
                        if ($parts[$i][0] == ':') {
                            $parts[$i] = '{' . substr($parts[$i], 1) . '}';
                        }
                    }
                    $nickname = implode('_', $parts);
                    $nickname = preg_replace('/[^A-Za-z0-9-]/', '', $nickname);
                    $parts[0] .= ".{format}";
                    $key = implode('/', $parts);
                    $description = isset(
                    $m['classDescription'])
                        ? $m['classDescription']
                        : $className . ' API';
                    $api = $this->_api("/$key", $description);
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
                            $operation->parameters[]
                                = $this->_parameter($param);
                        }
                    }
                    if (isset($m['return']['type'])) {
                        $responseClass = $m['return']['type'];
                        if (class_exists($responseClass)) {
                            $this->_model($responseClass);
                            $operation->responseClass
                                = $this->_noNamespace($responseClass);
                        }
                    }
                    $api->operations[] = $operation;
                    $r->apis[] = $api;
                }
            }
        }
        if (!$count) {
            throw new RestException(404);
        }
        $r->models = &$this->_models;
        return $r;
    }

    /**
     * Find the data type of the given value.
     *
     * @url-    do not map this function to url
     *
     * @param mixed $o              given value for finding type
     *
     * @param bool  $appendToModels if an object is found should we append to
     *                              our models list?
     *
     * @return string
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
        if (is_array($o)) return 'Array';
        if (is_bool($o)) return 'boolean';
        if (is_numeric($o)) return is_float($o) ? 'float' : 'int';
        return 'string';
    }

    private function _resourceListing()
    {
        $r = new stdClass();
        $r->apiVersion = (string)$this->restler->apiVersion;
        $r->swaggerVersion = "1.1";
        $r->basePath = $this->restler->baseUrl;
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
        $r->description = $description;
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

    private function _parameter($param)
    {
        $r = new stdClass();
        $r->name = $param['name'];
        $r->description = isset($param['description'])
            ? $param['description'] . '. '
            : '';
        //TODO: verify for header params and body params
        //paramType can be path or query or body or header
        $r->paramType = $param['required'] ? 'path' : 'query';
        $r->required = $param['required'];
        $r->allowMultiple = false;
        $r->dataType = 'string';
        //TODO: use validation info to set allowable values below
        //$r->allowableValues;
        return $r;
    }

    private function _model($className, $instance = null)
    {
        $properties = array();
        if (!$instance) {
            $instance = new $className();
        }
        $data = get_object_vars($instance);
        //TODO: parse the comments of properties, use it for type, description
        foreach ($data as $key => $value) {

            $type = $this->getType($value, true);
            $properties[$key] = array(
                'type' => $type,
                /*'description' => '' */ //TODO: add description
            );
            if ($type == 'Array') {
                $itemType = count($value)
                    ? $this->getType($value[0], true)
                    : 'string';
                $properties[$key]['item'] = array(
                    'type' => $itemType,
                    /*'description' => '' */ //TODO: add description
                );
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
        $className = explode('\\', $className);
        return end($className);
    }
}
