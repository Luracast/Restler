<?php
namespace Luracast\Restler;

use stdClass;
use Luracast\Restler\Data\String;

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
     * @var null|callable if the api methods are under access control mechanism
     * you can attach a function here that returns true or false to determine
     * visibility of a protected api method. this function will receive method
     * info as the only parameter.
     */
    public static $accessControlFunction = null;
    /**
     * @var array metadata about the api
     */
    public static $info = array(
        'title' => 'Restler API Explorer',
        'description' => 'Live API Documentation',
        //'termsOfServiceUrl' => "http://myapi.com/terms/",
        'contact' => 'arul@luracast.com',
        'license' => 'LGPL-2.1',
        'licenseUrl' => 'https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html',
    );
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
    protected $_models;
    protected $_bodyParam;
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
     * @var string base path for explorer
     */
    private $base = 'explorer';

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
        if (func_get_arg(0) == 'resources') {
            return $this->getResources(func_get_arg(1));
        }
        $filename = implode('/', func_get_args());
        if (empty($filename))
            $filename = 'index.html';
        PassThrough::file(__DIR__ . '/explorer/' . $filename, false, 60 * 60 * 24);
    }

    public function resources()
    {
        if (!String::beginsWith($this->base . '/', $this->restler->url))
            $this->base = pathinfo($this->restler->url, PATHINFO_DIRNAME);
        $r = new stdClass();
        $r->apiVersion = (string)$this->restler->getRequestedApiVersion();
        $r->swaggerVersion = static::SWAGGER_VERSION;
        $r->apis = $this->apis();
        $r->authorizations = $this->authorizations();
        $r->info = static::$info;
        return $r;
    }

    public function getResources($id)
    {
        if (!String::beginsWith($this->base . '/', $this->restler->url))
            $this->base = pathinfo($this->restler->url, PATHINFO_DIRNAME);
        $r = new stdClass();
        $r->apiVersion = (string)$this->restler->getRequestedApiVersion();
        $r->swaggerVersion = static::SWAGGER_VERSION;
        $r->basePath = $this->restler->getBaseUrl();
        $r->resourcePath = "/$id";

        $r->apis = $this->apis($id);
        $r->models = new stdClass();

        $r->produces = $this->restler->getProducedMimeTypes();
        $r->consumes = $this->restler->getConsumedMimeTypes();
        $r->authorizations = $this->authorizations();
        return $r;
    }

    private function apis($path = false)
    {
        if (!static::$accessControlFunction && Defaults::$accessControlFunction)
            static::$accessControlFunction = Defaults::$accessControlFunction;
        $version = $this->restler->getRequestedApiVersion();
        $allRoutes = Util::nestedValue(Routes::toArray(), "v$version");
        $map = array();
        $r = array();
        if (isset($allRoutes['*'])) {
            $this->filteredApiListing($allRoutes['*'], $map, $version);
            unset($allRoutes['*']);
        }
        $this->filteredApiListing($allRoutes, $map, $version);
        foreach ($map as $path => $description) {
            if (!String::contains($path, '{')) {
                //add id
                $r[] = array(
                    'path' => $path . $this->formatString,
                    'description' => $description
                );
            }
        }
        return $r;
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

    private function filteredApiListing(array $allRoutes, array &$map, $version = 1)
    {
        foreach ($allRoutes as $fullPath => $routes) {
            $path = explode('/', $fullPath);
            $resource = isset($path[0]) ? $path[0] : '';
            if (String::beginsWith($resource, $this->base) || String::endsWith($resource, 'index'))
                continue;
            foreach ($routes as $httpMethod => $route) {
                if (in_array($httpMethod, static::$excludedHttpMethods)) {
                    continue;
                }
                if (!$this->verifyAccess($route)) {
                    continue;
                }

                foreach (static::$excludedPaths as $exclude) {
                    if (empty($exclude)) {
                        if ($fullPath == $exclude)
                            continue 2;
                    } elseif (String::beginsWith($fullPath, $exclude)) {
                        continue 2;
                    }
                }

                $res = $resource
                    ? ($version == 1 ? "/$this->base/resources/$resource" : "/v$version/resources/$resource-v$version")
                    : ($version == 1 ? "/$this->base/resources/root" : "/v$version/resources/root-v$version");

                if (empty($map[$res])) {
                    $map[$res] = isset(
                    $route['metadata']['classDescription'])
                        ? $route['metadata']['classDescription'] : '';
                }
            }
        }
    }

    /**
     * Verifies that the requesting user is allowed to view the docs for this API
     *
     * @param $route
     *
     * @return boolean True if the user should be able to view this API's docs
     */
    protected function verifyAccess($route)
    {
        if (
            static::$hideProtected
            && !$this->_authenticated
            && $route['accessLevel'] > 1
        ) {
            return false;
        }
        if ($this->_authenticated
            && static::$accessControlFunction
            && (!call_user_func(
                static::$accessControlFunction, $route['metadata']))
        ) {
            return false;
        }
        return true;
    }
} 