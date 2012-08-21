<?php
namespace Luracast\Restler\Format;

use Luracast\Restler\Data\Util;
use MustacheTemplate;
use Luracast\Restler\RestException;

class MustacheFormat extends Format
{
    const MIME = 'text/html';
    const EXTENSION = 'mustache';

    public static $template = 'default';

    public function encode($data, $humanReadable = false)
    {
        $data = Util::objectToArray ( $data );
        $metadata = $this->restler->serviceMethodInfo->metadata;
        $params = $metadata ['param'];
        foreach ($params as $index => &$param) {
            $index = intval ( $index );
            if (is_numeric ( $index )) {
                $param ['value'] = $this->restler->serviceMethodInfo->arguments [$index];
            }
        }
        $data ['param'] = $params;
        if (isset ( $metadata ['template'] )) {
            self::$template = $metadata ['template'];
        }
        $m = new MustacheTemplate ( $this->loadTemplate ( self::$template ), $data );

        return $m->render ();
    }

    protected function loadTemplate($name)
    {
        return file_get_contents ( $_SERVER ['DOCUMENT_ROOT'] . dirname ( $_SERVER ['SCRIPT_NAME'] ) . '/templates/' . $name . '.htm' );
    }

    public function decode($data)
    {
        throw new RestException ( 405, 'MustacheFormat is write only' );
    }
}
