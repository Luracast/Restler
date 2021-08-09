<?php


use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\HttpClient\Client;
use React\HttpClient\Response;

class ReactHttpClient implements HttpClientInterface
{
    static protected $loop;

    public static function setLoop(LoopInterface $loop)
    {
        static::$loop = $loop;
    }

    public static function request(
        string $method,
        string $uri,
        array $headers = [],
        string $body = '',
        callable $callback = null
    ) {
        if (!static::$loop) {
            throw new Error('Please call ReactHttpClient::setLoop before calling ReactHttpClient::request');
        }
        $browser = new Browser(static::$loop);
        $headers['Content-Length'] = strlen($body);
        $req = $browser->request($method, $uri, $headers, $body);
        $req->then(function (ResponseInterface $response) use ($callback) {
            $callback(null, new SimpleHttpResponse((string)$response->getBody(), $response->getHeaders()));
        }, function (Exception $exception) use ($callback) {
            $callback($exception);
        });
    }
}
