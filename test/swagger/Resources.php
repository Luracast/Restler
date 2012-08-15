<?php

use Luracast\Restler\Restler;

class Resources
{
    /**
     * Injected at runtime
     *
     * @var Restler instance of restler
     */
    public $restler;

    function routes()
    {
        return $this->restler->routes;
    }

    function get()
    {
        $r = new ResourceListing();
        $r->basePath = $this->restler->baseUrl;
        $mappedResource = array();
        foreach ($this->restler->routes as $verb => $routes) {
            foreach ($routes as $route) {
                $path = $route['path'];
                $classDescription = isset(
                $route['metadata']['classDescription'])
                    ? $route['metadata']['classDescription']
                    : $route['className'];
                if (!isset($mappedResource[$path]) &&
                    strpos($path, 'resources') === FALSE
                ) {
                    //add resource
                    $r->apis[] = array(
                        'path' => '/resources/' .
                            strtolower($route['className']) .'.{format}',
                        'description' => $classDescription
                    );
                }
                $mappedResource[$path] = TRUE;
            }
        }
        return $r;
    }

    function index($name)
    {
        $name = strtolower($name);
        $r = new OperationListing();
        foreach ($this->restler->routes as $httpMethod => $value) {
            foreach ($value as $key => $route) {
                if (strpos($key, 'resources') !== FALSE)
                    continue;
                if (strpos($key, $name) === FALSE)
                    continue;
                $parts = explode('/', $key);
                if (count($parts) == 1 && $httpMethod == 'GET')
                    continue;
                for ($i = 0; $i < count($parts); $i++) {
                    if ($parts[$i][0] == ':') {
                        $parts[$i] = '{' . substr($parts[$i], 1) . '}';
                    }
                }
                $nickName = implode('_', $parts);
                $nickName = preg_replace('/[^A-Za-z0-9-]/', '', $nickName);
                $parts[0] .= ".{format}";
                $key = implode('/', $parts);
                $api = new Api();
                $api->path = "/$key";
                $operation = new Operation();
                #OPERATIONS
                #TODO: compare version and set deprecated accordingly
                $operation->deprecated = FALSE;
                $operation->httpMethod = $httpMethod;

                $operation->nickname = $nickName;
                //$route['className'].'_'.$route['methodName'];
                //$operation->nickname = $route['className'].'_'.$route['methodName'];
                if (isset($route['metadata']['description']))
                    $operation->summary = $route['metadata']['description'];
                else
                    $operation->summary = $route['methodName'] . ' ' .
                        $route['className'];
                if (isset($route['metadata']['longDescription']))
                    $operation->notes = $route['metadata']['longDescription'];
                if (isset($route['metadata']['throws'])) {
                    foreach ($route['metadata']['throws'] as $exception) {
                        $operation->errorResponses[] = array(
                            'reason' => $exception['reason'],
                            'code' => $exception['code']);
                    }
                }
                if (isset($route['metadata']['param'])) {
                    foreach ($route['metadata']['param'] as $param) {
                        /**
                         * @var Parameter
                         */
                        $p = new Parameter();
                        $p->name = $param['name'];
                        $p->description = isset($param['description'])
                            ? $param['description'] . '. '
                            : '';
                        $p->required = $param['required'];
                        $p->paramType = $param['required'] ? 'path' : 'query';
                        if (isset($param['default']) &&
                            !is_null($param['default'])
                        )
                            $p->description .= 'default value is ' .
                                $param['default'];
                        $operation->parameters[] = $p;
                    }
                }
                $api->operations[] = $operation;
                $r->apis[] = $api;
            }
        }
        $r->basePath = $this->restler->baseUrl;
        return $r;
    }
}

class ResourceListing
{
    public $apiVersion = '1';
    public $apis = array();
    public $basePath;
    public $swaggerVersion = '1.1-SHAPSHOT.121026';
}

class OperationListing extends ResourceListing
{
    public $models = array(
        'Author' => array(
            'properties' => array('id' => array('type' => 'string'),
                'name' => array('type' => 'string'), 'email' => array('type' => 'string'))));
    public $resourcePath = '/authors';
}

class Api
{
    public $path;
    public $description = '';
    public $operations = array();
}

class Operation
{
    public $errorResponses = array();
    //array('reason' => 'user not found', 'code' => 404));
    public $httpMethod = 'GET';
    public $notes = 'none';
    public $nickname = '';
    public $responseClass = 'Author';
    public $deprecated = FALSE;
    public $summary = 'SUMMARY';
    public $parameters = array();
}

class Parameter
{
    public $name = 'id';
    public $description = 'id of the author to be fetched';
    public $dataType = 'string';
    public $required = FALSE;
    public $allowMultiple = FALSE;
    public $paramType = 'query';
}