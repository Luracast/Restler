<?php
namespace Luracast\Restler\Format;

use Luracast\Restler\Data\Util;
use Mustache_Engine;
use Luracast\Restler\RestException;

class MustacheFormat extends Format
{
    const MIME = 'text/html';
    const EXTENSION = 'mustache';

    public static $template = 'default';

    public function encode($data, $humanReadable = false)
    {
        $data = Util::objectToArray($data);
        $params = array();
        if (isset($this->restler->apiMethodInfo->metadata)) {
            $metadata = $this->restler->apiMethodInfo->metadata;
            $params = $metadata['param'];
        }
        foreach ($params as $index => &$param) {
            $index = intval($index);
            if (is_numeric($index)) {
                $param['value'] = $this->restler->apiMethodInfo->arguments[$index];
            }
        }
        $data['param'] = $params;
        if (isset($metadata['template'])) {
            self::$template = $metadata['template'];
        }
        $m = new Mustache_Engine;
        return $m->render($this->loadTemplate(self::$template), $data);
    }

    protected function loadTemplate($name)
    {
        return file_get_contents($_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/templates/' . $name . '.htm');
    }

    public function decode($data)
    {
        throw new RestException(405, 'MustacheFormat is write only');
    }
}

