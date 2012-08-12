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
require_once '../../restler/restler.php';
require_once 'say.php';
$r = new Restler();
$r->addAPIClass('Say');
$r->handle();