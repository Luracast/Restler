<?php
/*
Title: Rate Limiting
Tagline: Abuse no more
Tags: create, retrieve, read, update, delete, post, get, put, filter, throttle, rate-limiting
Requires: PHP >= 5.3
Description: How to Rate Limit API access using a Filter class that implements
`iFilter` interface.

This example also shows how to use Defaults class to customize defaults, how to create your own
iCache implementation, and how to make a hybrid filter class that behaves differently
when the user is Authenticated

[![Restler API Explorer](../resources/explorer1.png)](explorer/index.html#!/authors-v1)

Key in `r3rocks` as the API key in the Explorer to see how rate limit changes

We are progressively improving the Authors class from CRUD example 
to show Best Practices and Restler 3 Features.

Make sure you compare them to understand.

> **Note:-**
>
>  1. Using session variables as DB and Cache is useless for real life and wrong. We are using it
>     Only for demo purpose. Since API Explorer is browser based it works well with that.
>
>  2. We are using Author.php to document return type of `GET authors/{id}` using `@return` comment

If you have hit the API Rate Limit or screwed up the Authors DB, you can easily reset by deleting
PHP_SESSION cookie using the Developer Tools in your browser.

Helpers: Author

Footer:
*[Author.php]: _009_rate_limiting/Author.php
*/

use Luracast\Restler\Defaults;
use Luracast\Restler\Filter\RateLimit;
use Luracast\Restler\Restler;

require_once '../../../vendor/restler.php';
//reuse the SessionDB from CRUD Example
require_once '../_007_crud/DB/Session.php';

//used only for demo, comment the following line
Defaults::$cacheClass = 'SessionCache';
//set extreme value for quick testing
RateLimit::setLimit('hour', 10);

$r = new Restler();

$r->addAPIClass('ratelimited\\Authors');
$r->addAPIClass('Resources');
$r->addFilterClass('RateLimit');
$r->addAuthenticationClass('KeyAuth');
$r->handle();