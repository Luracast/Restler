<?php

namespace Workerman\Protocols;


use GuzzleHttp\Psr7\ServerRequest;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Utils\ClassName;
use Luracast\Restler\Utils\Dump;
use Psr\Http\Message\ServerRequestInterface;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

class Psr7 extends Http
{
    /**
     * Parse $_POST、$_GET、$_COOKIE.
     *
     * @param string $buffer
     * @param TcpConnection $connection
     * @return ServerRequestInterface
     * @throws HttpException
     */
    public static function decode($buffer, TcpConnection $connection)
    {
        $r = parent::decode($buffer, $connection);
        $class = ClassName::get(ServerRequestInterface::class);
        $scheme = 'http';
        $port = $connection->getLocalPort();
        $headers = $r->header();
        $host = $headers['x-forwarded-host'] ?? $headers['host'];

        $portPos = strpos($host, ":");
        if ($portPos) {
            $port = (int)substr($host, $portPos + 1);
            $host = substr($host, 0, $portPos);
        }
        if (isset($headers['x-forwarded-proto'])) {
            $scheme = $headers['x-forwarded-proto'];
            if (!$portPos) {
                $port = $scheme == 'https' ? 443 : 80;
            }
        }
        $uri = "$scheme://$host:$port{$r->uri()}";
        /** @var ServerRequestInterface $request */
        $request = new $class(
            $r->method(), $uri, $headers, $r->rawBody(), null,
            [
                'REQUEST_TIME' => time(),
                'REQUEST_TIME_FLOAT' => microtime(true),
                'REMOTE_ADDR' => $connection->getRemoteIp(),
                'REMOTE_PORT' => $connection->getRemotePort(),
                'SERVER_ADDR' => $connection->getLocalIp(),
                'SERVER_PORT' => $connection->getLocalPort(),
            ]
        );
        $request = $request
            ->withQueryParams($r->get())
            ->withCookieParams($r->cookie())
            ->withParsedBody($r->post());

        $files = $r->file();
        if (!empty($files)) {
            $files = ServerRequest::normalizeFiles($files);
            $request = $request->withUploadedFiles($files);
        }
        return $request;
    }
}
