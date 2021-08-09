<?php


class SimpleHttpClient implements HttpClientInterface
{

    public static function request(string $method, string $uri, array $headers = [], string $body = '', callable $callback = null)
    {
        $headerText = '';
        foreach ($headers as $key => $value)
            $headerText .= "$key: $value\r\n";
        $context = stream_context_create([
            "http" => [
                "method" => $method,
                "header" => $headerText,
                "content" => $body,
                "ignore_errors" => true,
            ]]);

        $responseBody = @file_get_contents($uri, false, $context);
        if (false === $responseBody) {
            $error = error_get_last();
            if (is_callable($callback)) {
                $callback(new Error($error['message']), null);
            }
            return;
        }
        $responseHeaderLines = $http_response_header;
        $status_line = array_shift($responseHeaderLines);
        preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
        $status = 2 == count($match) ? $match[1] : 0;

        $responseHeaders = [];
        foreach ($responseHeaderLines as $value) {
            $value = explode(': ', $value, 2);
            $responseHeaders[$value[0]] = $value[1];
        }
        if (is_callable($callback)) {
            $callback(null, new SimpleHttpResponse($responseBody, $responseHeaders));
        }
    }
}
