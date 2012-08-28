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
        $responseClass = 'Array'
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
            $type = null;
            if (is_object($value)) {
                $oc = get_class($value);
                $type = $this->_noNamespace($oc);
                $this->_model($oc, $value);
            } elseif (is_array($value)) {
                $type = 'Array';
                /*
                foreach ($value as $v) {
                    if (is_object($v)) {
                        $oc = get_class($v);
                        $this->_model($oc, $v);
                    }
                }
                */
            } elseif (is_bool($value)) {
                $type = 'boolean';
            } elseif (is_numeric($value)) {
                $type = is_float($value) ? 'float' : 'int';
            } else {
                $type = 'string';
            }
            $properties[$key] = array(
                'type' => $type,
            /*    'description' => '' */ //TODO: add description
            );
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
