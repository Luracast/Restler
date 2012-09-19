<?php
/*
Title: Routing
Tagline: Ways to map api methods to url
Tags: routing, get, post, put, delete, patch
Requires: PHP >= 5.3
Description:

API Methods can be mapped to URI in two ways

1. Automatic Routing
2. Manual Routing

### How the automatic routing is done?

**Mapping to HTTP Verbs (GET, POST, PUT, DELETE, PATCH)**

Restler uses *get, put, post, and delete* as prefix to map PHP methods to
respective HTTP methods. When they are the only method names they map at
the class level similar to *index*

    GET/POST/PUT/DELETE class_name

GET is the default HTTP method so all public functions without any of
these prefixes will be mapped to GET request. This means functions
*getResult* and *result* will both be mapped to

    GET class_name/result

Similarly method *postSomething* will be mapped to

    POST class_name/something

**Smart Parameter Routing**

Starting from Restler 3, smart auto routes are created where optional
parameters will be mapped to query string, required primitive types will be
mapped to url path, objects ana array will be mapped to request body.

This helps build as few URIs as possible for the given API method,
reducing the ambiguity.

If the restler 2 ways of routing is preferred, where all parameters can be sent
using url path or query string or body, you can use the following code in the
index.php (gateway)

    Defaults::$smartAutoRouting = false;

It can also be set at the class level

Example 1: GET api/somanyways/1 returns "you have called Api::soManyWays()"

Example 2: GET api/somanyways/1/2 returns "you have called Api::soManyWays()"

Example 3: GET api/somanyways/1/2/3 returns "you have called Api::soManyWays()"

*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Api');
$r->handle();

