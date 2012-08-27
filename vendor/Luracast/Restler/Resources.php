<?php
namespace Luracast\Restler;

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
        $r = null;
        $name = strtolower(str_replace('-', '\\', $name));
        foreach ($this->restler->routes as $httpMethod => $value) {
            foreach ($value as $key => $route) {
                if (0 == strcasecmp($name, $route['className'])) {
                    $className = explode('\\', $route['className']);
                    $className = end($className);
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
                    $api->operations[] = $operation;
                    $r->apis[] = $api;

                }
            }
        }
        return $r;
    }

    private function _resourceListing()
    {
        $r = new \stdClass();
        $r->apiVersion = $this->restler->apiVersion;
        $r->swaggerVersion = "1.1";
        $r->basePath = $this->restler->baseUrl;
        $r->apis = array();
        return $r;
    }

    private function _operationListing($resourcePath = '/')
    {
        $r = $this->_resourceListing();
        $r->resourcePath = $resourcePath;
        $r->models = array();
        return $r;
    }

    private function _api($path, $description = '')
    {
        $r = new \stdClass();
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
        $r = new \stdClass();
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
        $r = new \stdClass();
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
}
