<?php

use Luracast\Restler\Defaults;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\Utils\Dump;
use RingCentral\Psr7\ServerRequest;

include __DIR__ . "/../vendor/autoload.php";

class Home
{
    function index()
    {
        return true;
    }
}

Defaults::$crossOriginResourceSharing = true;
Routes::addAPI(Home::class, '');

$h = new Restler();

$request = new ServerRequest('OPTIONS', 'http://localhost:4000', [
    'Access-Control-Request-Method' => 'GET',
    'Origin' => 'http://localhost:3000',
    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
    'Access-Control-Request-Headers' => 'authorization,content-type',
    'Accept' => '*/*',
    'DNT' => 1,
    'Referer' => 'http://localhost:3000/login',
    'Accept-Encoding' => 'gzip, deflate, br',
    'Accept-Language' => 'en-US,en;q=0.9,ta;q=0.8'
], '');

$response = $h->handle($request);

echo Dump::response($response, true);


