<?php


namespace Swoole\Http;


use GuzzleHttp\Psr7\ServerRequest;
use Luracast\Restler\Utils\ClassName;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Convert
{
    final public static function toPSR7(Request $request): ServerRequestInterface
    {
        $rawContent = (string)$request->rawcontent();
        $implementation = ClassName::get(ServerRequestInterface::class);
        /** @var ServerRequestInterface $instance */
        $instance = new $implementation(
            $request->server['request_method'] ?? 'GET',
            'http://' . $request->header['host'] . ($request->server['request_uri'] ?? ''),
            $request->header ?? [],
            $rawContent,
            $request->server['server_protocol'] ?? '1.1',
            static::buildServerParams($request)
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
        $header = $request->header ?? [];
        $return['USER'] = get_current_user();
        if (function_exists('posix_getpwuid')) {
            $return['USER'] = posix_getpwuid(posix_geteuid())['name'];
        }
        $return['HTTP_CACHE_CONTROL'] = $header['cache-control'] ?? '';
        $return['HTTP_UPGRADE_INSECURE_REQUESTS'] = $header['upgrade-insecure-requests-control'] ?? '';
        $return['HTTP_CONNECTION'] = $header['connection'] ?? '';
        $return['HTTP_DNT'] = $header['dnt'] ?? '';
        $return['HTTP_ACCEPT_ENCODING'] = $header['accept-encoding'] ?? '';
        $return['HTTP_ACCEPT_LANGUAGE'] = $header['accept-accept-language'] ?? '';
        $return['HTTP_ACCEPT'] = $header['accept'] ?? '';
        $return['HTTP_USER_AGENT'] = $header['user-agent'] ?? '';
        $return['HTTP_HOST'] = $header['user-host'] ?? '';
        $return['SERVER_NAME'] = '_';
        $return['SERVER_PORT'] = $server['server_port'] ?? null;
        $return['SERVER_ADDR'] = $server['server_addr'] ?? '';
        $return['REMOTE_PORT'] = $server['remote_port'] ?? null;
        $return['REMOTE_ADDR'] = $server['remote_addr'] ?? '';
        $return['SERVER_SOFTWARE'] = $server['server_software'] ?? '';
        $return['GATEWAY_INTERFACE'] = $server['server_software'] ?? '';
        $return['REQUEST_SCHEME'] = 'http';
        $return['SERVER_PROTOCOL'] = $server['server_protocol'] ?? null;
        $return['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../bin');
        $return['DOCUMENT_URI'] = '/';
        $return['REQUEST_URI'] = $server['request_uri'] ?? '';
        $return['SCRIPT_NAME'] = '/index_swoole.php';
        $return['CONTENT_LENGTH'] = $header['content-length'] ?? null;
        $return['CONTENT_TYPE'] = $header['content-type'] ?? null;
        $return['REQUEST_METHOD'] = $server['request_method'] ?? 'GET';
        $return['QUERY_STRING'] = $server['query_string'] ?? '';
        $return['SCRIPT_FILENAME'] = rtrim($return['DOCUMENT_ROOT'], '/') . '/' . ltrim($return['SCRIPT_NAME']);
        $return['PATH_INFO'] = $server['path_info'] ?? '';
        $return['FCGI_ROLE'] = 'RESPONDER';
        $return['PHP_SELF'] = $return['PATH_INFO'];
        $return['REQUEST_TIME_FLOAT'] = $server['request_time_float'] ?? '';
        $return['REQUEST_TIME'] = $server['request_time'] ?? '';
        return $return;
    }

    final public static function fromPSR7(ResponseInterface $psr7Response, Response $response): void
    {
        $response->status($psr7Response->getStatusCode());
        static::populateHeaders($psr7Response, $response);
        $response->end((string)$psr7Response->getBody());
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
