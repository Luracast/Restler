<?php declare(strict_types=1);

use Luracast\Restler\Utils\CommentParser;

include __DIR__ . "/../vendor/autoload.php";

$url = new \RingCentral\Psr7\Uri('https://0doubyd3yf.execute-api.ap-southeast-1.amazonaws.com/dev/baseurl');
$scriptName = '/dev/index.php';

$core  =  new \Luracast\Restler\Restler();

//$core->getPath($url, $scriptName);
$updated = (string)$core->baseUrl;
var_dump($updated);
