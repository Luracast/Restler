<?php

use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Restler;
use Luracast\Restler\StaticProperties;
use Luracast\Restler\Utils\Convert;
use Luracast\Restler\Utils\Dump;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

if (!function_exists('exceptions')) {
    function exceptions(Restler $r, $path)
    {
        if ($source = $r->exception) {
            $traces = [];
            do {
                $traces += $source->getTrace();
            } while ($source = $source->getPrevious());
            $traces += debug_backtrace();
            return parse_backtrace($traces, $path, 0);
        } else {
            return parse_backtrace(debug_backtrace(), $path);
        }
    }

    function parse_backtrace($raw, $path, $skip = 1)
    {
        $base = strlen($path) + 1;
        $output = [];
        $index = 0;
        foreach ($raw as $entry) {
            if ($skip-- > 0) {
                continue;
            }
            $key = '';
            if (isset($entry['line'])) {
                $file = substr($entry['file'], $base);
                $key = "$file:" . $entry['line'] . ' ';
            }
            if (isset($entry['class'])) {
                $output[++$index][$key] = ' ' . $entry['class'] . "::" . $entry['function']
                    . '(' . ')'; //substr(json_encode($entry['args']), 1, -1)
            }
        }
        return $output;
    }
}

$trace = exceptions($restler, dirname($path, 2));

$data['render'] = $render = function ($data, $shadow = true) use (&$render) {
    $r = '';
    if (empty($data)) {
        return $r;
    }
    $r .= $shadow ? "<ul class=\"shadow\">\n" : "<ul>\n";
    if (is_iterable($data)) {
        // field name
        foreach ($data as $key => $value) {
            $r .= '<li>';
            $r .= is_numeric($key)
                ? "<strong>[$key]</strong> "
                : "<strong>$key: </strong>";
            $r .= '<span>';
            if (is_iterable($value)) {
                // recursive
                $r .= $render($value, false);
            } else {
                // value, with hyperlinked hyperlinks
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                if (is_object($value)) {
                    $value = method_exists($value, '__toString')
                        ? $value->__toString()
                        : 'new ' . get_class($value) . '()';
                }
                $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
                if (strpos($value, 'http://') === 0) {
                    $r .= '<a href="' . $value . '">' . $value . '</a>';
                } else {
                    $r .= $value;
                }
            }
            $r .= "</span></li>\n";
        }
    } elseif (is_bool($data)) {
        $r .= '<li>' . ($data ? 'true' : 'false') . '</li>';
    } elseif (is_object($data)) {
        if ($data instanceof ResponseInterface) {
            $r .= '<li>' . $data->getBody()->getContents() . '</li>';
        } else {
            $r .= '<li>' . get_class($data) . '</li>';
        }
    } else {
        $r .= "<li><strong>$data</strong></li>";
    }
    $r .= "</ul>\n";
    return $r;
};

$icon = '';
if (is_object($response)) {
    $convert = new Convert(new StaticProperties(Convert::class));
    $response = $convert->toArray($response);
}
if ($success && isset($api)) {
    $arguments = implode(', ', $api->parameters);
    $icon = "<icon class=\"success\"></icon>";
    $title = "{$api->className}::"
        . "{$api->methodName}({$arguments})";
} else {
    if (isset($response['error']['message'])) {
        $icon = '<icon class="denied"></icon>';
        $title = end(explode(':', $response['error']['message'], 2));
    } else {
        $icon = '<icon class="warning"></icon>';
        $title = 'No Matching Resource';
    }
}
$template_vars = $data->getArrayCopy();
unset($template_vars['response']);
unset($template_vars['api']);
unset($template_vars['request']);
unset($template_vars['restler']);
unset($template_vars['render']);

$requestHeaders = Dump::requestHeaders($container->get(RequestInterface::class));
$responseHeaders = 'HTTP/1.1 ' . $restler->responseCode . ' ' . HttpException::$codes[$restler->responseCode] . PHP_EOL;
foreach ($restler->responseHeaders as $k => $v) {
    $responseHeaders .= "$k: $v\r\n";
}
$version = Restler::VERSION;
$debugCss = file_get_contents(__DIR__ . '/debug.css');
return <<<TEMPLATE
<html>
    <head>
        <title>$title</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <style>
            $debugCss
        </style>
    </head>
    <body>
        <div id="breadcrumbs-one">
        </div>
        <header>
            <h1>$title</h1>
        </header>
        <article>
            <h2>Request:</h2>
            <pre class="header">$requestHeaders</pre>
        
            <h2>Response:
                <right>$icon</right>
            </h2>
            <pre class="header">$responseHeaders</pre>
            {$_('render', $response)}
            <h2>Additional Template Data:</h2>
            {$_('render', $template_vars)}
            <h2>Trace:</h2>
            {$_('render', $trace)}
            <p>Restler v{$version}</p>
        </article>
    </body>
</html>
TEMPLATE;

