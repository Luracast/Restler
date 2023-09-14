<?php


namespace Swoole\Http;


use GuzzleHttp\Psr7\ServerRequest;
use Luracast\Restler\Defaults;
use Luracast\Restler\Utils\ClassName;
use PHP_CodeSniffer\Tokenizers\PHP;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class Convert
{
    final public static function toPSR7(Request $request): ServerRequestInterface
    {
        $rawContent = (string)$request->rawcontent();
        $implementation = ClassName::get(ServerRequestInterface::class);
        /** @var ServerRequestInterface $instance */
        $_server = static::buildServerParams($request);
        $instance = new $implementation(
            $request->server['request_method'] ?? 'GET',
            $_server['REQUEST_SCHEME'].'://' . $_server['HTTP_HOST'] . ($request->server['request_uri'] ?? ''),
            $request->header ?? [],
            $rawContent,
            $_server['SERVER_PROTOCOL'] ?? '1.1',
            $_server
        );
        if ($request->get) {
            $instance = $instance->withQueryParams($request->get);
            $instance = $instance->withUri($instance->getUri()->withQuery(http_build_query($request->get)));
        }
        if ($request->post) {
            $instance = $instance->withParsedBody($request->post);
        }
        if ($request->cookie) {
            $instance = $instance->withCookieParams($request->cookie);
        }
        if ($request->files) {
            $instance = $instance->withUploadedFiles(ServerRequest::normalizeFiles($request->files));
        }
        return $instance;
    }

    private static function buildServerParams(Request $request): array
    {
        $server = $request->server ?? [];
        $headers = $request->header ?? [];
        $return['USER'] = get_current_user();
        if (function_exists('posix_getpwuid')) {
            $return['USER'] = posix_getpwuid(posix_geteuid())['name'];
        }
        $scheme = 'http';
        $port = $server['server_port'] ?? null;
        $host = $headers['x-forwarded-host'] ?? $headers['host'] ?? $headers['user-host'] ?? '';
        $portPos = strpos($host, ":");
        if ($portPos) {
            $port = (int)substr($host, $portPos + 1);
        }
        if (isset($headers['x-forwarded-proto'])) {
            $scheme = $headers['x-forwarded-proto'];
            if (!$portPos) {
                $port = $scheme == 'https' ? 443 : 80;
            }
        }
        $return['HTTP_CACHE_CONTROL'] = $headers['cache-control'] ?? '';
        $return['HTTP_UPGRADE_INSECURE_REQUESTS'] = $headers['upgrade-insecure-requests-control'] ?? '';
        $return['HTTP_CONNECTION'] = $headers['connection'] ?? '';
        $return['HTTP_DNT'] = $headers['dnt'] ?? '';
        $return['HTTP_ACCEPT_ENCODING'] = $headers['accept-encoding'] ?? '';
        $return['HTTP_ACCEPT_LANGUAGE'] = $headers['accept-accept-language'] ?? '';
        $return['HTTP_ACCEPT'] = $headers['accept'] ?? '';
        $return['HTTP_USER_AGENT'] = $headers['user-agent'] ?? '';
        $return['HTTP_HOST'] = $host;
        $return['SERVER_NAME'] = '_';
        $return['SERVER_PORT'] = $port;
        $return['SERVER_ADDR'] = $server['server_addr'] ?? '';
        $return['REMOTE_PORT'] = $server['remote_port'] ?? null;
        $return['REMOTE_ADDR'] = $server['remote_addr'] ?? '';
        $return['SERVER_SOFTWARE'] = $server['server_software'] ?? '';
        $return['GATEWAY_INTERFACE'] = $server['server_software'] ?? '';
        $return['REQUEST_SCHEME'] = $scheme;
        $return['HTTPS'] = $scheme === 'https' || $port == 443 ? 'on' : 'off';
        $return['SERVER_PROTOCOL'] = $server['server_protocol'] ?? null;
        $return['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../bin');
        $return['DOCUMENT_URI'] = '/';
        $return['REQUEST_URI'] = $server['request_uri'] ?? '';
        $return['SCRIPT_NAME'] = '/index_swoole.php';
        $return['CONTENT_LENGTH'] = $headers['content-length'] ?? null;
        $return['CONTENT_TYPE'] = $headers['content-type'] ?? null;
        $return['REQUEST_METHOD'] = $server['request_method'] ?? 'GET';
        $return['QUERY_STRING'] = $server['query_string'] ?? '';
        $return['SCRIPT_FILENAME'] = rtrim($return['DOCUMENT_ROOT'], '/') . '/' . ltrim($return['SCRIPT_NAME']);
        $return['PATH_INFO'] = $server['path_info'] ?? '';
        $return['FCGI_ROLE'] = 'RESPONDER';
        $return['PHP_SELF'] = $return['PATH_INFO'];
        $return['REQUEST_TIME_FLOAT'] = $server['request_time_float'] ?? '';
        $return['REQUEST_TIME'] = $server['request_time'] ?? '';
        if (isset($headers['x-forwarded-host'])) {
            $return['HTTP_X_FORWARDED_HOST'] = $headers['x-forwarded-host'];
        }
        if (isset($headers['x-forwarded-proto'])) {
            $return['HTTP_X_FORWARDED_PROTO'] = $headers['x-forwarded-proto'];
        }
        if (isset($headers['x-forwarded-for'])) {
            $return['HTTP_X_FORWARDED_FOR'] = $headers['x-forwarded-for'];
        }
        return $return;
    }


    final public static function fromPSR7(ResponseInterface $psr7Response, Response $response): void
    {
        $response->status($psr7Response->getStatusCode());
        static::populateHeaders($psr7Response, $response);
        $data = $psr7Response->getBody();
        if ($data instanceof StreamInterface) {
            $data = $data->detach();
        }
        if (is_resource($data)) {
            rewind($data);
            stream_set_read_buffer($data, Defaults::$responseBufferSize);
            while (!feof($data)) {
                @$response->write(fread($data, Defaults::$responseBufferSize));
            }
            fclose($data);
            $response->end('');
        } else {
            $response->end($data);
        }
    }

    private static function populateHeaders(ResponseInterface $psr7Response, Response $response): void
    {
        $headers = $psr7Response->getHeaders();
        foreach ($headers as $name => $values) {
            $name = ucwords($name, '-');
            if ($name === 'Set-Cookie') {
                $response->header($name, end($values));
                continue;
            }
            $response->header($name, implode(', ', $values));
        }
    }
}
