<?php

/*
$extensions = explode(',', 'php');
$mimes = explode(',', 'PHP/Well,THP\\dull');

$count = max(count($extensions), count($mimes));
$extensions += array_fill(0, $count, end($extensions));
$mimes += array_fill(0, $count, end($mimes));
print_r($extensions);
print_r($mimes);
print_r(array_combine($mimes, $extensions));
exit;
 Title: Hello World Example.
 Tagline: Let's say hello!.
 Description: Basic hello world example to get started with Restler 2.0.
 Example 1: GET say/hello returns "Hello world!".
 Example 2: GET say/hello/restler2.0 returns "Hello Restler2.0!".
 Example 3: GET say/hello?to=R.Arul%20Kumaran returns "Hello R.Arul Kumaran!".
 */

/*
set_include_path('../../vendor'.PATH_SEPARATOR.get_include_path());
spl_autoload_register(function ($className) {
$className = ltrim($className, "\\");
preg_match('/^(.+)?([^\\\\]+)$/U', $className, $match);
$className = str_replace("\\", "/", $match[1])
. str_replace(array("\\", "_"), "/", $match[2])
. ".php";
include_once $className;
});
 */

//require_once '../../restler/restler.php';
//require_once '../../restler/debugformat/debugformat.php';
require_once '../../vendor/restler.php';
//require_once 'say.php';
use Luracast\Restler\Restler;
use Luracast\Restler\Format\DebugFormat;
use Luracast\Restler\Events;

Events::listen('onRoute', function($url)
{
    trace("onRoute: $url");
});
$r = new Restler(TRUE, TRUE);
$r->addAPIClass('Say');
$r->setSupportedFormats('Luracast\Restler\Format\DebugFormat');
$r->handle();

