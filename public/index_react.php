<?php

declare(strict_types=1);

require __DIR__ . '/../api/bootstrap.php';

use Luracast\Restler\Defaults;
use Luracast\Restler\Middleware\StaticFiles;
use Luracast\Restler\Restler;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Server;

//serve static files
Restler::$middleware[] = new StaticFiles(BASE . '/public');

$loop = React\EventLoop\Factory::create();

ReactHttpClient::setLoop($loop);
Defaults::$implementations[HttpClientInterface::class] = [ReactHttpClient::class];

$server = new Server($loop,
    new React\Http\Middleware\StreamingRequestMiddleware(),
    new React\Http\Middleware\LimitConcurrentRequestsMiddleware(100), // 100 concurrent buffering handlers
    new React\Http\Middleware\RequestBodyBufferMiddleware(16 * 1024 * 1024), // 16 MiB per request
    new React\Http\Middleware\RequestBodyParserMiddleware(),
    function (ServerRequestInterface $request) {
        echo '      ' . $request->getMethod() . ' ' . $request->getUri()->getPath() . PHP_EOL;
        return (new Restler)->handle($request);
    }
);

$server->on(
    'error',
    function (Exception $e) {
        echo 'Error: ' . $e->getMessage() . PHP_EOL;
    }
);


$socket = new React\Socket\Server('0.0.0.0:8080', $loop);
$server->listen($socket);

echo "Server running at http://127.0.0.1:8080\n";

$loop->run();
