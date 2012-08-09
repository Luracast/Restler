<?php
/*
 Title: Hello World Example.
 Tagline: Let's say hello!.
 Tags: basic.
 Description: Basic hello world example to get started with Restler 3.
 Example 1: GET say/hello returns "Hello world!".
 Example 2: GET say/hello?to=R.Arul%20Kumaran returns "Hello R.Arul Kumaran!".
 Example 3: GET say/hi/restler3.0 returns "Hi Restler3.0!".

class A {
function b(B $c, array $d, $e) {
}
}
class B {
}

$refl = new ReflectionClass('A');
$par = $refl->getMethod('b')->getParameters();

var_dump($par[0]->getClass()->getName());  // outputs B
var_dump($par[1]->getClass());  // note that array type outputs NULL
var_dump($par[2]->getClass());  // outputs NULL
 */

require_once '../../restler/restler.php';
require_once '../../restler/iformat.php';
require_once '../../restler/debugformat/debugformat.php';
require_once '../../restler/jsonserializable.php';
require_once '../../restler/ivalueobject.php';
require_once '../../restler/valueobject.php';
require_once 'AnotherCustom.php';
$r = new Restler(true, true);
$r->addAPIClass('Validate');
$r->setSupportedFormats('DebugFormat', 'JsonFormat');
$r->handle();