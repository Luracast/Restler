<?php
/*
Title: Html Format
Tagline: rendering custom views
Tags: view,html
Requires: PHP >= 5.3
Description:

Add a custom view to your data using view templates in various formats
*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->setSupportedFormats('HtmlFormat');
$r->addAPIClass('RichContent');
$r->handle();