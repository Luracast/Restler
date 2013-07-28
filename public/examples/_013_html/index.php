<?php
/*
Title: Html Format
Tagline: rendering custom views
Tags: view,html
Requires: PHP >= 5.3
Description:

Add a custom view to your data using view templates in various formats.
It currently supports

 - php (default)
 - twig (requires `"twig/twig"`)
 - mustache / handlebar (requires `"mustache/mustache"`)

> **Note:-** if you want your favourite template library to be supported
> submit a pull request, just follow the style of existing ones as a guide

When HtmlFormat is used with out defining a view it uses debug view to present
data and more information

*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->setSupportedFormats('JsonFormat','HtmlFormat');
$r->addAPIClass('Tasks');
$r->addAPIClass('Resources');
$r->handle();