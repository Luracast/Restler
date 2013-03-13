<?php
/*
Title: Html Format
Tagline: rendering custom views
Tags: view,html
Requires: PHP >= 5.3
Description:
This example shows how to use vendor specific media types for versioning
*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->setSupportedFormats('HtmlFormat');
$r->addAPIClass('RichContent');
$r->handle();