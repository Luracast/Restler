<?php
declare(strict_types=1);

use Luracast\Restler\Middleware\StaticFiles;
use Luracast\Restler\Restler;
use Workerman\Worker;

$port = require __DIR__ . '/../api/bootstrap.php';

//serve static files
Restler::$middleware[] = new StaticFiles(BASE . '/public');

$worker = new Worker('restler://0.0.0.0:' . $port);
$worker->count = 4;

$worker->onMessage = function ($connection, $msg) {
    //echo '% ' . $msg . PHP_EOL;
};
// run all workers
Worker::runAll();
