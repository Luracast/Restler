<?php


interface HttpClientInterface
{
    public static function request(
        string $method,
        string $uri,
        array $headers = [],
        string $body = '',
        callable $callback = null
    );
}
