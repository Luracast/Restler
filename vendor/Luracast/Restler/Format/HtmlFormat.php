<?php
namespace Luracast\Restler\Format;

use Luracast\Restler\Data\Util as DataUtil;
use Luracast\Restler\Defaults;
use Luracast\Restler\RestException;
use Luracast\Restler\Restler;
use Luracast\Restler\Util;

class HtmlFormat extends Format
{
    const MIME = 'text/html';
    const EXTENSION = 'html';
    public static $view = 'debug';
    public static $format = 'php';
    /**
     * @var array global key value pair to be supplied to the templates. All
     * keys added here will be available as a variable inside the template
     */
    public static $data = array();
    /**
     * @var string set it to the location of your the view files. Defaults to
     * views folder which is same level as vendor directory.
     */
    public static $viewPath;
    /**
     * @var Restler;
     */
    public $restler;

    public function __construct()
    {
        if (!static::$viewPath) {
            $array = explode('vendor', __DIR__, 2);
            static::$viewPath = $array[0] . 'views';
            if (!is_readable(static::$viewPath)) {
                throw new \Exception(
                    'The views directory `'
                        . self::$viewPath . '` should exist with read permission.'
                );
            }
        }
        static::$data['basePath'] = dirname($_SERVER['SCRIPT_NAME']);
        static::$data['baseUrl'] = Util::$restler->_baseUrl;
    }

    /**
     * Encode the given data in the format
     *
     * @param array   $data           resulting data that needs to
     *                                be encoded in the given format
     * @param boolean $humanReadable  set to TRUE when restler
     *                                is not running in production mode.
     *                                Formatter has to make the encoded output
     *                                more human readable
     *
     * @return string encoded string
     */
    public function encode($data, $humanReadable = false)
    {

        $data = array(
            'response' => DataUtil::objectToArray($data)
        ) + static::$data;
        $params = array();
        //print_r($this->restler);
        if (isset($this->restler->apiMethodInfo->metadata)) {
            $info = $data['info'] = $this->restler->apiMethodInfo;
            $metadata = $info->metadata;
            $params = $metadata['param'];
        }
        foreach ($params as $index => &$param) {
            $index = intval($index);
            if (is_numeric($index)) {
                $param['value'] = $this->restler->apiMethodInfo->parameters[$index];
            }
        }
        $data['param'] = $params;
        if (isset($metadata['view'])) {
            self::$view = $metadata['view'];
        }
        if (false === ($i = strpos(self::$view, '.'))) {
            $extension = self::$format;
            self::$view .= '.' . $extension;
        } else {
            $extension = substr(self::$view, $i + 1);
        }
        switch ($extension) {
            case 'php':
                $view = self::$viewPath . DIRECTORY_SEPARATOR .
                    self::$view;

                if (!is_readable($view)) {
                    throw new RestException(500, "view file `$view` is not readable. Check for file presence and file permissions ");
                }

                $template = function ($view) use ($data) {
                    extract($data);
                    include $view;
                };
                $template($view);
                break;
            case 'twig':
                $loader = new \Twig_Loader_Filesystem(static::$viewPath);
                $twig = new \Twig_Environment($loader, array(
                    'cache' => Defaults::$cacheDirectory,
                    'debug' => true,
                ));
                $template = $twig->loadTemplate(self::$view);
                return $template->render($data);
            case 'handlebar':
            case 'mustache':
                break;
            default:
                throw new RestException(500, "Unsupported template system `$extension`");
        }
    }

    /**
     * Decode the given data from the format
     *
     * @param string $data
     *            data sent from client to
     *            the api in the given format.
     *
     * @return array associative array of the parsed data
     *
     * @throws RestException
     */
    public function decode($data)
    {
        throw new RestException(500, 'HtmlFormat is write only');
    }
}
