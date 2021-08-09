<?php declare(strict_types=1);

use Luracast\Restler\Defaults;
use Luracast\Restler\Restler;
use Psr\Http\Message\ResponseInterface;
use Swoole\Constant as C;
use Swoole\Http\Convert;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

require __DIR__ . '/../api/bootstrap.php';

Swoole\Runtime::enableCoroutine(true, SWOOLE_HOOK_ALL );

Defaults::$implementations[HttpClientInterface::class] = [SwooleHttpClient::class];

$http = new Server("0.0.0.0", 8080);

$http->set([
    C::OPTION_WORKER_NUM => 1, // The number of worker processes
    C::OPTION_DAEMONIZE => false, // Whether start as a daemon process
    C::OPTION_BACKLOG => 128, // TCP backlog connection number
    //Adds support for GZIP compression
    C::OPTION_HTTP_COMPRESSION => true,
    C::OPTION_HTTP_COMPRESSION_LEVEL => 5,
    //Enable static files from public folder
    C::OPTION_DOCUMENT_ROOT => BASE . '/public',
    C::OPTION_ENABLE_STATIC_HANDLER => true,
    /*
    C::OPTION_STATIC_HANDLER_LOCATIONS => [
        '/examples',
    ],
    */
]);

$http->on('start', function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:8080\n";
});

$http->on('request', function (Request $req, Response $res) {
    $request = Convert::toPSR7($req);
    echo '      ' . $request->getMethod() . ' ' . $request->getUri()->getPath() . PHP_EOL;
    (new Restler)->handle($request)->then(function (ResponseInterface $response) use ($res) {
        Convert::fromPSR7($response, $res);
    });
});

$http->start();
