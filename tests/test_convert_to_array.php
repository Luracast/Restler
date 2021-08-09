<?php declare(strict_types=1);

use Luracast\Restler\StaticProperties;
use Luracast\Restler\Utils\Convert;

include __DIR__ . "/../vendor/autoload.php";

$data = [
    'error' =>
        [
            'code' => 404,
            'message' => 'Not Found',
        ],
    'debug' =>
        [
            'source' => 'Router.php:731',
            'trace' =>
                [
                    0 =>
                        [
                            'file' => 'Core.php:283',
                            'function' => 'Router::find',
                            'args' =>
                                [
                                    0 => '',
                                    1 => 'GET',
                                    2 => 1,
                                    3 =>
                                        [
                                        ],
                                ],
                        ],
                    1 =>
                        [
                            'file' => 'Restler.php:162',
                            'function' => 'Core->route',
                            'args' =>
                                [
                                ],
                        ],
                    2 =>
                        [
                            'file' => 'Restler.php:134',
                            'function' => 'Restler->_handle',
                            'args' =>
                                [
                                    0 => 'new React\\Http\\Io\\ServerRequest()',
                                ],
                        ],
                    3 =>
                        [
                            'file' => 'Restler.php:29',
                            'function' => 'Restler->handle',
                            'args' =>
                                [
                                    0 => 'new React\\Http\\Io\\ServerRequest()',
                                ],
                        ],
                    4 =>
                        [
                            'file' => 'TcpConnection.php:658',
                            'function' => 'Restler::decode',
                            'args' =>
                                [
                                    0 => 'GET / HTTP/1.1
Content-Type: application/json
Host: localhost:8080
Connection: close
User-Agent: Paw/3.1.8 (Macintosh; OS X/10.14.2) GCDHTTPRequest',
                                    1 => 'new Workerman\\Connection\\TcpConnection()',
                                ],
                        ],
                    5 =>
                        [
                            'file' => 'StreamSelectLoop.php:238',
                            'function' => 'TcpConnection->baseRead',
                            'args' =>
                                [
                                    0 => null,
                                ],
                        ],
                    6 =>
                        [
                            'file' => 'StreamSelectLoop.php:205',
                            'function' => 'StreamSelectLoop->waitForStreamActivity',
                            'args' =>
                                [
                                    0 => null,
                                ],
                        ],
                    7 =>
                        [
                            'file' => 'Base.php:252',
                            'function' => 'StreamSelectLoop->run',
                            'args' =>
                                [
                                ],
                        ],
                    8 =>
                        [
                            'file' => 'Base.php:135',
                            'function' => 'Base->run',
                            'args' =>
                                [
                                ],
                        ],
                    9 =>
                        [
                            'file' => 'Worker.php:2321',
                            'function' => 'Base->loop',
                            'args' =>
                                [
                                ],
                        ],
                ],
        ],
];

$convert = new Convert(new StaticProperties(Convert::class));
$result = $convert->toArray($data);
//var_export($result);

$json = json_encode($result, 192);

echo $json;

