<?php

use Swoole\Coroutine\Http\Client;

class SwooleHttpClient implements HttpClientInterface
{
    public static function request(
        string $method,
        string $uri,
        array $headers = [],
        string $body = '',
        callable $callback = null
    )
    {
        echo "$method $uri\n\n";
        go(function () use ($method, $uri, $headers, $body, $callback) {
            $parts = parse_url($uri);
            $client = new Client($parts['host'], $parts['port']);
            $client->setMethod($method);
            $client->setHeaders($headers);
            if (!empty($body)) {
                $client->setData($body);
            }
            $client->execute($uri);
            $response = new SimpleHttpResponse($client->body, $client->getHeaders() ?? []);
            $client->close();
            $client = null;
            if (is_callable($callback))
                $callback(null, $response);
        });
    }
}
