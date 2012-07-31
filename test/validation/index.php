<?php
/*
 Title: Hello World Example.
 Tagline: Let's say hello!.
 Tags: basic.
 Description: Basic hello world example to get started with Restler 3.
 Example 1: GET say/hello returns "Hello world!".
 Example 2: GET say/hello?to=R.Arul%20Kumaran returns "Hello R.Arul Kumaran!".
 Example 3: GET say/hi/restler3.0 returns "Hi Restler3.0!".
 */
require_once '../../restler/ivalueobject.php';
require_once '../../restler/valueobject.php';
require_once 'validate.php';
require_once 'custom.php';


$ins = Custom::__set_state(array('name'=>'arul', 'age'=>36));

var_dump($ins);

exit;
require_once '../../restler/restler.php';
$r = new Restler(true, true);
$r->addAPIClass('Validate');
$r->setSupportedFormats('DebugFormat', 'JsonFormat');
$r->handle();