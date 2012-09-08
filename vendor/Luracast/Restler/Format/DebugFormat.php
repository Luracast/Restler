<?php
namespace Luracast\Restler\Format {

    use Luracast\Restler\RestException;
    use Luracast\Restler\Restler;
    use Luracast\Restler\Data\Util;

    class DebugFormat extends Format
    {
        public static $traces = array();
        public static $traceInfos = array();
        const MIME = 'text/html';
        const EXTENSION = 'html';
        public $restler;

        public function encode($data, $humanReadable = FALSE, $wrapHtml = TRUE)
        {
            if ($wrapHtml) {
                $data = Util::objectToArray($data);
            }
            $r = '';
            $r .= "<ul>\n";
            if (is_array($data)) {
                // field name
                foreach ($data as $key => $value) {
                    $r .= '<li>';
                    $r .= is_numeric($key) ? "[<strong>$key</strong>]" : "<strong>$key: </strong>";
                    $r .= '<span>';
                    if (is_array($value)) {
                        // recursive
                        $r .= $this->encode($value, $humanReadable, FALSE);
                    } else {
                        // value, with hyperlinked hyperlinks
                        if (is_bool($value)) {
                            $value = $value ? 'true' : 'false';
                        }
                        $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
                        if (strpos($value, 'http://') === 0) {
                            $r .= '<a href=\"' . $value . '\">' . $value . '</a>';
                        } else {
                            $r .= $value;
                        }
                    }
                    $r .= "</span></li>\n";
                }
            } elseif (is_bool($data)) {
                $r .= '<li>' . ($data ? 'true' : 'false') . '</li>';
            } else {
                $r .= "<li><strong>$data</strong></li>";
            }
            $r .= "</ul>\n";
            if ($wrapHtml) {
                $r = $this->header() . $r;
                $r .= $this->footer();
            }

            return $r;
        }

        public function decode($data)
        {
            throw new RestException(405, 'DebugFormat is write only');
        }

        public function header()
        {
            // print_r($this->restler->serviceMethodInfo);
            $version = Restler::VERSION;
            $info = $this->restler->apiMethodInfo;
            if (! is_null($info)) {
                $arguments = implode(', ', $info->arguments);
                $title = "{$info->className}->"
                    . "{$info->methodName}({$arguments})";
            } else {
                $title = 'No Matching Resource';
            }

            $notices = '';
            $styles = array(
                1 => 'error',
                5 => 'info',
                4 => 'warning',
                8 => 'success'
            );
            // die(print_r(self::$traces,TRUE)) ;
            foreach (self::$traces as $i => $o) {
                $style = $styles[self::$traceInfos[$i]['level']];
                $notices .= "<a class=\"{$style}\"><strong>" . self::$traceInfos[$i]['source']. "</strong>: {$o} </a>";
            }
            //TODO: Alter the path to avoid hard coding
            $path = '/restler3.dev/public/debug';
            //substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));

            return <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>Restler v$version - {$title}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link type="text/css" rel="stylesheet" media="all" href="{$path}/debug.css">
</head>
<body>
{$notices}
<h1>{$title}</h1>
<h2>Response:</h2>
EOT;
        }

        public function footer()
        {
            $all_traces = print_r(self::$traces, TRUE);
            $all_trace_infos = print_r(self::$traceInfos, TRUE);
            $reqHeadersArr = array();//\apache_request_headers();
            $requestHeaders = $_SERVER['REQUEST_METHOD']. ' ' . $_SERVER['REQUEST_URI']. ' ' . $_SERVER['SERVER_PROTOCOL']. PHP_EOL;
            foreach ($reqHeadersArr as $key => $value) {
                if ($key == 'Host')
                    continue;
                $requestHeaders .= "$key: $value" . PHP_EOL;
            }
            // $requestHeaders = $this->encode(apache_request_headers(), FALSE,
            // FALSE);
            $responseHeaders = implode(PHP_EOL, headers_list());

            return <<<EOT
<h2>Log:</h2>
<pre>{$this->restler->log}
{$all_traces}
{$all_trace_infos}
</pre>
<h2>Request Headers:</h2>
<pre>{$requestHeaders}</pre>
<h2>Response Headers:</h2>
<pre>{$responseHeaders}</pre>
</body>
</html>

EOT;
        }

        public function setCharset($charset)
        {
            // TODO Auto-generated method stub
        }

        public function getCharset()
        {
            // TODO Auto-generated method stub
        }
    }
}

namespace {

    use Luracast\Restler\Format\DebugFormat;
    use Luracast\Restler\Restler;

    if (! function_exists('trace')) {

        function debug_backtrace_callee($o, $index)
        {
            $n = $o[$index];
            if (empty($n['type']))
                return $n['function'];
            return $n['class']. $n['type']. $n['function'];
        }

        /**
         * * @var JsonFormat
         */
        function trace($o, $level = LOG_NOTICE)
        {
            $i = debug_backtrace();
            $info = array(
                'level' => $level,
                'file' => $i[0]['file'],
                'line' => $i[0]['line']
            );
            $info['source']= debug_backtrace_callee($i, 1);
            if (! is_scalar($o)) {
                // $format = new JsonFormat();
                $info['object']= print_r($o, TRUE); // $format->encode($o, TRUE);
                DebugFormat::$traces[]= '[' . gettype($o) . ']';
            } else {
                DebugFormat::$traces[]= (string) $o;
            }
            DebugFormat::$traceInfos[]= $info;
        }

        function trace_error_handler($errno, $errstr, $errfile, $errline)
        {
            if (!(error_reporting() & $errno)) {
                // This error code is not included in error_reporting
                // return;
            }
            //echo "$errno $errstr $errfile $errline <hr/>";
            DebugFormat::$traces[]= $errstr;
            $level = LOG_NOTICE;
            $info = array(
                'source' => 'Error',
                'file' => $errfile,
                'line' => $errline
            );
            switch ($errno) {
                case E_PARSE :
                case E_COMPILE_ERROR :
                case E_ERROR :
                case E_RECOVERABLE_ERROR :
                case E_CORE_ERROR :
                    return FALSE;
                case E_USER_ERROR :
                    $level = LOG_ALERT;
                    break;

                case E_USER_WARNING :
                    $level = LOG_WARNING; //
                    break;

                case E_USER_NOTICE :
                    $level = LOG_ALERT; //LOG_NOTICE; // 5
                    break;
            }
            $info['level']= $level;
            DebugFormat::$traceInfos[]= $info;
            /* return FALSE to execute PHP internal error handler */

            return TRUE;
        }
        set_error_handler('trace_error_handler');

        function handleShutdown()
        {
            $error = error_get_last();
            $info = '';
            if ($error !== NULL) {
                echo implode('<hr/>', get_included_files());
                $info = "[SHUTDOWN]file:" . $error['file']. " | ln:" . $error['line']. " | msg:" . $error['message']. PHP_EOL;
                echo $info;
            } else {
                //echo "SHUTDOWN";
            }
            //file_put_contents(dirname(__DIR__)."/../../../scratch/restler
            //.out.log",Restler::$log."\n$info\n===================\n",
            //FILE_APPEND);
            exit;
        }
        register_shutdown_function('handleShutdown');
    }

}

