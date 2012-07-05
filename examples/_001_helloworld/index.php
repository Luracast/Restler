<?php
/*
 Title: Hello World Example.
 Tagline: Let's say hello!.
 Description: Basic hello world example to get started with Restler 2.0.
 Example 1: GET say/hello returns "Hello world!".
 Example 2: GET say/hello/Restler2.0 returns "Hello Restler2.0!".
 Example 3: GET say/hello?to=R.Arul%20Kumaran returns "Hello R.Arul Kumaran!".
 */
require_once '../../restler/restler.php';
require_once 'say.php';
$r = new Restler();
$r->addAPIClass('Say');
$r->handle();