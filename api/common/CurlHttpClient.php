<?php

class CurlHttpClient implements HttpClientInterface
{
    protected static $options = [
        'debug' => false,
        'http_port' => '80',
        'user_agent' => 'CurlHttpClient',
        'timeout' => 20,
        'curlopts' => null,
        'verifyssl' => true,
    ];

    public static function setOptions(array $options)
    {
        static::$options = array_merge(static::$options, $options);
    }

    public static function request(
        string $method,
        string $uri,
        array $headers = [],
        string $body = '',
        callable $callback = null
    )
    {
        $headers['Content-Length'] = strlen($body);
        static::$options['http_port'] = parse_url($uri, PHP_URL_PORT) ?? 80;
        $responseHeaders = [];
        $catchHeaders = function ($curl, $headerLine) use (&$responseHeaders) {
            $parts = explode(': ', rtrim($headerLine));
            if (count($parts) > 1)
                $responseHeaders[$parts[0]] = $parts[1];
            return strlen($headerLine);
        };
        $curlOptions = [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body];
        switch ($method) {
            case 'POST':
                break;
            case 'PUT':
            case 'DELETE':
            case 'PATCH':
                $curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
                break;
            default:
                unset($curlOptions[CURLOPT_POST]);
                unset($curlOptions[CURLOPT_POSTFIELDS]);
        }
        $h = [];
        foreach ($headers as $k => $v) {
            $h[] = is_string($k) ? "$k:$v" : $v;
        }
        $curlOptions += [
            CURLOPT_HEADERFUNCTION => $catchHeaders,
            CURLOPT_URL => $uri,
            CURLOPT_PORT => static::$options['http_port'],
            CURLOPT_USERAGENT => static::$options['user_agent'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => static::$options['timeout'],
            CURLOPT_HTTPHEADER => $h,
            CURLOPT_SSL_VERIFYPEER => static::$options['verifyssl'],
        ];
        if (ini_get('open_basedir') == '' && ini_get('safe_mode') != 'On') {
            $curlOptions[CURLOPT_FOLLOWLOCATION] = true;
        }
        if (is_array(static::$options['curlopts'])) {
            $curlOptions += static::$options['curlopts'];
        }
        if (isset($options['proxy'])) {
            $curlOptions[CURLOPT_PROXY] = static::$options['proxy'];
        }
        $curl = curl_init();

        curl_setopt_array($curl, $curlOptions);

        $responseBody = curl_exec($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);
        if (is_callable($callback)) {
            if ($errorNumber) {
                $callback(new Error($errorMessage ?? "Curl error $errorNumber"), null);
            } else {
                $callback(null, new SimpleHttpResponse($responseBody, $responseHeaders));
            }
        }
    }
}
