<?php
namespace Luracast\Restler\Utils;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Dump
{
    public const CRLF = "\r\n";

    public static function request(ServerRequestInterface $request): string
    {
        $text = static::requestHeaders($request);
        $text .= static::CRLF;
        $text .= urldecode((string)$request->getBody()) . static::CRLF . static::CRLF;
        return $text;
    }

    public static function requestHeaders(ServerRequestInterface $request): string
    {
        $text = $request->getMethod() . ' ' . $request->getUri() . ' HTTP/' . $request->getProtocolVersion() . PHP_EOL;
        foreach ($request->getHeaders() as $k => $v) {
            $text .= ucwords($k) . ': ' . implode(', ', $v) . PHP_EOL;
        }
        return $text;
    }

    public static function response(
        ResponseInterface $response,
        bool $includeHeader = true,
        bool $headerAsString = true
    ): string {
        $text = $includeHeader ? self::responseHeaders($response, $headerAsString) : '';
        $text .= (string)$response->getBody();
        return $text;
    }

    /**
     * @param ResponseInterface $response
     * @param bool $headerAsString
     * @return string
     */
    public static function responseHeaders(ResponseInterface $response, bool $headerAsString): string
    {
        $http = sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
        $text = '';
        if ($headerAsString) {
            $text .= $http . static::CRLF;
            foreach ($response->getHeaders() as $k => $v) {
                $text .= ucwords($k) . ': ' . $response->getHeaderLine($k) . static::CRLF;
            }
            $text .= static::CRLF;
        } else {
            header($http, true, $response->getStatusCode());
            foreach ($response->getHeaders() as $name => $values) {
                if ('Date' == $name && PHP_SAPI == 'cli-server') {
                    continue;
                }
                $value = $response->getHeaderLine($name);
                header("$name: $value", true);
            }
        }
        return $text;
    }

    public static function backtrace(int $limit = 0): string
    {
        if ($limit) {
            $limit += 1;
        }
        $data = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
        $array = [];
        $stringer = function ($o) use (&$stringer) {
            if (is_string($o)) {
                return "'$o'";
            }
            if (is_scalar($o)) {
                return (string)$o;
            }
            if (is_object($o)) {
                return get_class($o);
            }
            if (is_array($o)) {
                return '[]';//' . implode(', ', array_map($stringer, $o)) . '
            }
        };
        foreach ($data as $index => $trace) {
            $parts = explode('\\', $trace['class'] ?? '');
            $file_parts = explode('/', $trace['file']);
            $array[$index] = array_pop($file_parts)
                . ':' . $trace['line'] . ' ' . array_pop($parts)
                . '::' . $trace['function'] . '(' . implode(', ', array_map($stringer, $trace['args'] ?? [])) . ')';
        }
        array_shift($array);
        return json_encode($array) . PHP_EOL;
    }
}
