<?php declare(strict_types=1);


use LogicalSteps\Async\Async;

include __DIR__ . "/../api/bootstrap.php";

/*
$loop = React\EventLoop\Factory::create();

ReactHttpClient::setLoop($loop);

Async::await(['ReactHttpClient::request', 'GET', 'http://localhost/', [], ''])->then(function ($result) {
    var_dump($result);
});

$loop->run();

Async::await(['CurlHttpClient::request', 'GET', 'http://localhost/', [], ''])->then(function ($result) {
    var_dump($result);
});
*/
Async::await([
    'CurlHttpClient::request',
    'POST',
    'http://localhost/restler4/examples/_015_oauth2_server/grant',
    ['Content-Type' => 'application/x-www-form-urlencoded'],
    http_build_query([
        "grant_type" => "authorization_code",
        "code" => "6a1708c42583e45b9370bba610194c294e6a3331",
        "client_id" => "demoapp",
        "client_secret" => "demopass",
        "redirect_uri" => "http://localhost:8080/examples/_014_oauth2_client/authorized",
    ])
])->then(function ($result) {
    var_dump($result);
},function ($error) {
    var_dump($error->getMessage());
});
