<?php


class ForkedCurlHttpClient implements HttpClientInterface
{

    public static function request(
        string $method,
        string $uri,
        array $headers = [],
        string $body = '',
        callable $callback = null
    ) {
        $pid = pcntl_fork();
        switch ($pid) {
            case 0: //Child
                $func = function ($error, $result) use ($callback) {
                    //ob_get_clean();
                    $callback($error, $result);
                };
                CurlHttpClient::request($method, $uri, $headers, $body, $func);
                break;
            case -1: //Error
                if (is_callable($callback)) {
                    $callback(new Error('Failed to Fork the process'), null);
                }
                break;
            default: //Parent
                // make sure the parent outlives the child process
                //pcntl_wait($status);
                die();
        }
    }
}