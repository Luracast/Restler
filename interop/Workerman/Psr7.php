<?php

namespace Workerman\Protocols;


use GuzzleHttp\Psr7\ServerRequest;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\Utils\ClassName;
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
        /** @var ServerRequestInterface $request */
        $request = new $class(
            $r->method(), $r->uri(), $r->header(), $r->rawBody(), null, [
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
