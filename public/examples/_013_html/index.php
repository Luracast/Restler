<?php
/*
Title: Html Format
Tagline: rendering custom views
Tags: view,html,twig,mustache,handlebar,php,template
Requires: PHP >= 5.3
Description: A special format that lets us render a template with the api result

It currently supports the following template libraries/formats

 - php (default)
 - mustache / handlebar (requires `"mustache/mustache"`)
 - twig (requires `"twig/twig"`)
 - Laravel 4 blade templates (requires `"illuminate/view"`)

When HtmlFormat is used with out defining a view it uses debug view to present
data and more information

[![Debug View](../resources/debug_view.jpg)](tasks/24)

View can be defined directly by setting `HtmlFormat::$view` and
`HtmlFormat::$format` where view defines the template file and format determines
the template type. When the view is defined with an extension such as `my.twig`
the extension will override the format

`HtmlFormat::$errorView` which defaults to the debug view defines the view when
the api call fails. When set to null `HtmlFormat::$view` is used for rendering
error response as well

`HtmlFormat::$view` can be set with a api method comment in the following format

    @view  folder/name.extension

When the extension is omitted `HtmlFormat::$format` will be used

HtmlFormat will look at `views` folder that resides parallel to `vendor` directory
for the template files and can be changed by setting `HtmlFormat::$viewPath` to
full path of a folder

Content:

In this example, we are building tasks api and also a single page application
using jQuery and the templates

[![Single Page App](../resources/html_view.png)](tasks)

Our api response is sent to the template under the name `response` along with other
information such as `basePath`, `baseUrl`, `request` parameters.

You can send custom data yourself by setting key value pairs in
`HtmlFormat::$data` array

If you do not want all the information and want to keep your template simple, you
can use `{@data key.innerkey}` comment as shown below

    @view todo/list {@data response}

This calls the list template with key value pairs defined at the response array
directly accessible as the variable and value inside the template

This example also show cases the heredoc syntax based simple templating system
which is Supported without any external dependencies

Just to show that it is possible to come up with API as well as an App using the
same resource and url, you can try the json version of the tasks api using the
API Explorer [here](explorer/index.html)

*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;
use Luracast\Restler\Format\HtmlFormat;

//Un-comment one of the following lines to try a different template engine
//  HtmlFormat::$template = 'handlebar'; //Mustache
//  HtmlFormat::$template = 'twig'; //Symfony 2 Twig
//  HtmlFormat::$template = 'blade'; //Laravel Views

$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'HtmlFormat');
$r->addAPIClass('Tasks');
$r->addAPIClass('Resources');
$r->handle();