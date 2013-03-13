<?php
use Luracast\Restler\Restler;
use Luracast\Restler\Util;

$call_trace = '';

function exceptions()
{
    global $call_trace;
    $r = Util::$restler;
    $source = $r->_exceptions;
    if (count($source)) {
        $source = end($source);
        $traces = array();
        do {
            $traces += $source->getTrace();
        } while ($source = $source->getPrevious());
        $traces += debug_backtrace();
        $call_trace
            = parse_backtrace($traces, 0);
    } else {
        $call_trace
            = parse_backtrace(debug_backtrace());
    }

}
exceptions();

function parse_backtrace($raw, $skip = 1)
{
    $output = "";
    foreach ($raw as $entry) {
        if ($skip-- > 0) {
            continue;
        }
        //$output .= print_r($entry, true) . "\n";
        $output .= "\nFile: " . $entry['file'] . " (Line: " . $entry['line'] . ")\n";
        if (isset($entry['class']))
            $output .= $entry['class'] . "::";
        $output .= $entry['function']
            . "( " . json_encode($entry['args']) . " )\n";
    }
    return $output;
}


//print_r(get_defined_vars());
//print_r($response);
if (isset($info)) {
    $arguments = implode(', ', $info->arguments);
    $title = "{$info->className}::"
        . "{$info->methodName}({$arguments})";
} else {
    if (isset($response['error']['message'])) {
        $title = 'Error: ' . $response['error']['message'];
    } else {
        $title = 'No Matching Resource';
    }
}
function render($data)
{
    $r = '';
    if (empty($data))
        return $r;
    $r .= "<ul>\n";
    if (is_array($data)) {
        // field name
        foreach ($data as $key => $value) {
            $r .= '<li>';
            $r .= is_numeric($key)
                ? "[<strong>$key</strong>]"
                : "<strong>$key: </strong>";
            $r .= '<span>';
            if (is_array($value)) {
                // recursive
                $r .= render($value);
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
    return $r;
}
$reqHeadersArr = array();
$requestHeaders = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . PHP_EOL;
foreach ($reqHeadersArr as $key => $value) {
    if ($key == 'Host')
        continue;
    $requestHeaders .= "$key: $value" . PHP_EOL;
}
// $requestHeaders = $this->encode(apache_request_headers(), FALSE,
// FALSE);
$responseHeaders = implode(PHP_EOL, headers_list());

?>
<!DOCTYPE html>
<html>
<head>
    <title>Restler v<?php echo Restler::VERSION?> - <?php echo $title?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link type="text/css" rel="stylesheet" media="all"
          href="<?php echo $baseUrl ?>/debug/debug.css">
</head>
<body>
{$notices}
<h1><?php echo $title?></h1>

<h2>Response:</h2>
<?php echo render($response);?>
<h2>Log:</h2>
<pre>
<?php echo $call_trace ?>
<?php echo Util::$restler->log ?>
{$all_traces}
{$all_trace_infos}
</pre>
<h2>Request Headers:</h2>
<pre><?php echo $requestHeaders ?></pre>
<h2>Response Headers:</h2>
<pre><?php echo $responseHeaders ?></pre>
</body>
</html>