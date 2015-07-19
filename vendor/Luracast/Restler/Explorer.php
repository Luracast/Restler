<?php
namespace Luracast\Restler;

use stdClass;
use Luracast\Restler\Data\String;
use Luracast\Restler\Data\ValidationInfo;

/**
 * Class Explorer
 * @package Luracast\Restler
 *
 * @access  hybrid
 */
class Explorer
{
    const SWAGGER_VERSION = '1.2';
    /**
     * @var bool should protected resources be shown to unauthenticated users?
     */
    public static $hideProtected = true;
    /**
     * @var bool should we use format as extension?
     */
    public static $useFormatAsExtension = true;
    /*
     * @var bool can we accept scalar values (string, int, float etc) as the request body?
     */
    public static $allowScalarValueOnRequestBody = false;
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
     * @var bool should we group all the operations with the same url or not
     */
    public static $groupOperations = false;

    /**
     * @var string class that holds metadata as static properties
     */
    public static $infoClass = 'Luracast\Restler\ExplorerInfo';
    /**
     * Injected at runtime
     *
     * @var Restler instance of restler
     */
    public $restler;
    /**
     * @var string when format is not used as the extension this property is
     * used to set the extension manually
     */
    public $formatString = '';
    protected $models = array();
    /**
     * @var bool|stdClass
     */
    protected $_fullDataRequested = false;
    protected $crud = array(
        'POST' => 'create',
        'GET' => 'retrieve',
        'PUT' => 'update',
        'DELETE' => 'delete',
        'PATCH' => 'partial update'
    );
    protected static $prefixes = array(
        'get' => 'retrieve',
        'index' => 'list',
        'post' => 'create',
        'put' => 'update',
        'patch' => 'modify',
        'delete' => 'remove',
    );
    protected $_authenticated = false;
    protected $cacheName = '';

    /**
     * @var array type mapping for converting data types to JSON-Schema Draft 4
     * Which is followed by swagger 1.2 spec
     */
    public static $dataTypeAlias = array(
        //'string' => 'string',
        'int' => 'integer',
        'number' => 'number',
        'float' => array('number', 'float'),
        'bool' => 'boolean',
        //'boolean' => 'boolean',
        //'NULL' => 'null',
        'array' => 'array',
        //'object' => 'object',
        'stdClass' => 'object',
        'mixed' => 'string',
        'date' => array('string', 'date'),
        'datetime' => array('string', 'date-time'),
    );

    public function __construct()
    {

    }

    /**
     * Serve static files for exploring
     *
     * Serves explorer html, css, and js files
     *
     * @url GET *
     */
    public function get()
    {
        if (func_num_args() > 1 && func_get_arg(0) == 'resources') {
            return $this->getResources(func_get_arg(1));
        }
        $filename = implode('/', func_get_args());
        $redirect = false;
        if (
            (empty($filename) && substr($_SERVER['REQUEST_URI'], -1, 1) != '/') ||
            $filename == 'index.html'
        ) {
            $status = 302;
            $url = $this->restler->getBaseUrl() . '/' . $this->base() . '/';
            header("{$_SERVER['SERVER_PROTOCOL']} $status " . RestException::$codes[$status]);
            header("Location: $url");
            exit;
        }
        PassThrough::file(__DIR__ . '/explorer/' . (empty($filename) ? 'index.html' : $filename), false, 0); //60 * 60 * 24);
    }

    public function resources()
    {
        $r = new stdClass();
        $r->apiVersion = (string)$this->restler->getRequestedApiVersion();
        $r->swaggerVersion = static::SWAGGER_VERSION;
        $r->apis = $this->apis($r->apiVersion);
        $r->authorizations = $this->authorizations();
        $r->info = array_filter(get_class_vars(static::$infoClass));
        return $r;
    }

    public function getResources($id)
    {
        $r = new stdClass();
        $r->apiVersion = (string)$this->restler->getRequestedApiVersion();
        $r->swaggerVersion = static::SWAGGER_VERSION;
        $r->basePath = $this->restler->getBaseUrl();
        $r->resourcePath = "/$id";

        $r->apis = $this->apis($r->apiVersion, $id);
        $r->models = (object)$this->models;

        $r->produces = $this->restler->getWritableMimeTypes();
        $r->consumes = $this->restler->getReadableMimeTypes();
        $r->authorizations = $this->authorizations();
        return $r;
    }

    private function apis($version = 1, $resource = false)
    {
        $map = Routes::findAll(static::$excludedPaths + array($this->base()), static::$excludedHttpMethods, $version);
        $r = array();
        $a = array();
        foreach ($map as $path => $data) {
            $route = $data[0]['route'];
            $access = $data[0]['access'];
            if ($access && !String::contains($path, '{')) {
                $r[] = array(
                    'path' => empty($path) ? '/root' : "/$path",
                    'description' => ''
                    //TODO: Util::nestedValue($route, 'metadata', 'classDescription') ? : ''
                );
            }
            if (static::$hideProtected && !$access)
                continue;
            foreach ($data as $item) {
                $route = $item['route'];
                $access = $item['access'];
                if (static::$hideProtected && !$access)
                    continue;
                $url = $route['url'];
                $a[$path][] = array(
                    'path' => "/$url",
                    'description' =>
                        Util::nestedValue($route, 'metadata', 'classDescription') ? : '',
                    'operations' => array($this->operation($route))
                );
            }
        }
        if (false !== $resource) {
            if ($resource == 'root') $resource = '';
            if (isset($a[$resource])) return $a[$resource];
        }
        return $r;
    }

    private function operation($route)
    {
        $r = new stdClass();
        $r->method = $route['httpMethod'];
        $r->nickname = $this->nickname($route);
        $r->parameters = $this->parameters($route);

        $m = $route['metadata'];

        $r->summary = isset($m['description'])
            ? $m['description']
            : '';
        $r->notes = isset($m['longDescription'])
            ? $m['longDescription']
            : '';
        $r->responseMessages = $this->responseMessages($route);
        $this->setType(
            $r,
            new ValidationInfo(Util::nestedValue($m, 'return') ? : array())
        );
        if (is_null($r->type) || 'mixed' == $r->type) {
            $r->type = 'array';
        } elseif ($r->type == 'null') {
            $r->type = 'void';
        } elseif (String::contains($r->type, '|')) {
            $r->type = 'array';
        }

        //TODO: add $r->authorizations
        //A list of authorizations required to execute this operation. While not mandatory, if used, it overrides
        //the value given at the API Declaration's authorizations. In order to completely remove API Declaration's
        //authorizations completely, an empty object ({}) may be applied.
        //TODO: add $r->produces
        //TODO: add $r->consumes
        //A list of MIME types this operation can produce/consume. This is overrides the global produces definition at the root of the API Declaration. Each string value SHOULD represent a MIME type.
        //TODO: add $r->deprecated
        //Declares this operation to be deprecated. Usage of the declared operation should be refrained. Valid value MUST be either "true" or "false". Note: This field will change to type boolean in the future.
        return $r;
    }

    private function parameters(array $route)
    {
        $r = array();
        $children = array();
        $required = false;
        foreach ($route['metadata']['param'] as $param) {
            $info = new ValidationInfo($param);
            $description = isset($param['description']) ? $param['description'] : '';
            if ('body' == $info->from) {
                if ($info->required)
                    $required = true;
                $param['description'] = $description;
                $children[] = $param;
            } else {
                $r[] = $this->parameter($info, $description);
            }
        }
        if (!empty($children)) {
            if (
                1 == count($children) &&
                (static::$allowScalarValueOnRequestBody || !empty($children[0]['children']))
            ) {
                $firstChild = $children[0];
                if (empty($firstChild['children'])) {
                    $description = $firstChild['description'];
                } else {
                    $description = '<section class="body-param">';
                    foreach ($firstChild['children'] as $child) {
                        $description .= isset($child['required'])
                            ? '<strong>' . $child['name'] . '</strong> (required)<br/>'
                            : $child['name'] . '<br/>';
                    }
                    $description .= '</section>';
                }
                $r[] = $this->parameter(new ValidationInfo($firstChild), $description);
            } else {
                $description = '<section class="body-param">';
                foreach ($children as $child) {
                    $description .= isset($child['required'])
                        ? '<strong>' . $child['name'] . '</strong> (required)<br/>'
                        : $child['name']. '<br/>';
                }
                $description .= '</section>';

                //lets group all body parameters under a generated model name
                $name = $this->nameModel($route);
                $r[] = $this->parameter(
                    new ValidationInfo(array(
                        'name' => $name,
                        'type' => $name,
                        'from' => 'body',
                        'required' => $required,
                        'children' => $children
                    )),
                    $description
                );
            }
        }
        return $r;
    }

    private function parameter(ValidationInfo $info, $description = '')
    {
        $p = new stdClass();
        $p->name = $info->name;
        $this->setType($p, $info);
        if (empty($info->children) || $info->type != 'array') {
            //primitives
            if ($info->default)
                $p->defaultValue = $info->default;
            if ($info->choice)
                $p->enum = $info->choice;
            if ($info->min)
                $p->minimum = $info->min;
            if ($info->max)
                $p->maximum = $info->max;
            //TODO: $p->items and $p->uniqueItems boolean
        }
        $p->description = $description;
        $p->paramType = $info->from; //$info->from == 'body' ? 'form' : $info->from;
        $p->required = $info->required;
        $p->allowMultiple = false;
        return $p;
    }

    private function responseMessages(array $route)
    {
        $r = array();
        if (is_array($throws = Util::nestedValue($route, 'metadata', 'throws'))) {
            foreach ($throws as $message) {
                $m = (object)$message;
                //TODO: add $m->responseModel from composer class
                $r[] = $m;
            }
        }
        return $r;
    }

    private function model($type, array $children)
    {
        $type = Util::getShortName($type);
        if (isset($this->models[$type]))
            return $this->models[$type];
        $r = new stdClass();
        $r->id = $type;
        $r->description = "$type Model"; //TODO: enhance this on Router
        $r->required = array();
        $r->properties = array();
        foreach ($children as $child) {
            $info = new ValidationInfo($child);
            $p = new stdClass();
            $this->setType($p, $info);
            $p->description = $child['description'];
            if ($info->default)
                $p->defaultValue = $info->default;
            if ($info->choice)
                $p->enum = $info->choice;
            if ($info->min)
                $p->minimum = $info->min;
            if ($info->max)
                $p->maximum = $info->max;
            if ($info->required)
                $r->required[] = $info->name;
            $r->properties[$info->name] = $p;
        }
        //TODO: add $r->subTypes https://github.com/wordnik/swagger-spec/blob/master/versions/1.2.md#527-model-object
        //TODO: add $r->discriminator https://github.com/wordnik/swagger-spec/blob/master/versions/1.2.md#527-model-object
        $this->models[$type] = $r;
        return $r;
    }

    private function setType(&$object, ValidationInfo $info)
    {
        //TODO: proper type management
        if ($info->type == 'array') {
            if ($info->children) {
                $this->model($info->contentType, $info->children);
                $object->items = (object)array(
                    '$ref' => $info->contentType
                );
            } elseif ($info->contentType) {
                $object->items = (object)array(
                    'type' => $info->contentType
                );
            } else {
                $object->items = (object)array(
                    'type' => 'string'
                );
            }
        } elseif ($info->children) {
            $this->model($info->type, $info->children);
        } elseif ($t = Util::nestedValue(static::$dataTypeAlias, strtolower($info->type))) {
            if (is_array($t)) {
                $info->type = $t[0];
                $object->format = $t[1];
            } else {
                $info->type = $t;
            }
        } else {
            $info->type = 'string';
        }
        $object->type = $info->type;
        $has64bit = PHP_INT_MAX > 2147483647;
        if ($object->type == 'integer') {
            $object->format = $has64bit
                ? 'int64'
                : 'int32';
        } elseif ($object->type == 'number') {
            $object->format = $has64bit
                ? 'double'
                : 'float';
        }
    }

    private function nickname(array $route)
    {
        static $hash = array();
        $method = $route['methodName'];
        if (isset(static::$prefixes[$method])) {
            $method = static::$prefixes[$method];
        } else {
            $method = str_replace(
                array_keys(static::$prefixes),
                array_values(static::$prefixes),
                $method
            );
        }
        while (isset($hash[$method]) && $route['url'] != $hash[$method]) {
            //create another one
            $method .= '_';
        }
        $hash[$method] = $route['url'];
        return $method;
    }

    private function nameModel(array $route)
    {
        static $hash = array();
        $count = 1;
        //$name = str_replace('/', '-', $route['url']) . 'Model';
        $name = $route['className'] . 'Model';
        while (isset($hash[$name . $count])) {
            //create another one
            $count++;
        }
        $name .= $count;
        $hash[$name] = $route['url'];
        return $name;
    }

    private function authorizations()
    {
        $r = new stdClass();
        $r->apiKey = (object)array(
            'type' => 'apiKey',
            'passAs' => 'query',
            'keyname' => 'api_key',
        );
        return $r;
    }

    private function base()
    {
        return strtok($this->restler->url, '/');
    }
} 