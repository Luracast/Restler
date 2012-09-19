<?php
/*
Title: Routing
Tagline: Ways to map api methods to url
Tags: routing, get, post, put, delete, patch
Requires: PHP >= 5.3
Description:

### Two ways of Routing

API Methods can be mapped to URI in two ways

1. Automatic Routing
2. Manual Routing

### How automatic routing is done?

**Role of Class Name**

When the api class is added without specifying the path prefix, name of the
class will be used as path prefix

**Mapping to HTTP Verbs (GET, POST, PUT, DELETE, PATCH)**

Restler uses *get, put, post, delete, and patch* as prefix to map PHP methods to
respective HTTP methods. When they are used method names with out any suffix
they map at the class level similar to *index*

    {HTTP_METHOD} class_name

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

It can also be set at the class level or method level with the following php doc
comment.

    @smart-auto-routing false

### Manual Routing

**Role of Class Name**

When you add the api class, use the second, optional parameter to specify the
url segment to map it to, you may choose to keep it blank to map it to the root

    $r->addAPIClass('MyClass','');

### Rest of the URI

rest of the uri can be created manually using php doc comment as shown below or
else automatically routed for public and protected methods unless they are
marked with `@access private`. We can specify as many routes as we want for a
single method. Comment structure is as shown below

    @url {HTTP_METHOD} path_segment_including_url_parameters

For example

    @url POST custom/path/{var1}/{var2}

Take a look at the api class used here and compare it with the routes below to
understand.

Example 1: GET api/somanyways/1?p2=something returns "you have called Api::soManyWays()"

Example 2: GET api/somanyways/1/2 returns "you have called Api::soManyWays()"

Example 3: GET api/somanyways/1/2/3 returns "you have called Api::soManyWays()"

Example 4: GET api/what/ever/you/want returns

{
  "error": {
    "code": 400,
    "message": "Bad Request: anything is missing."
  }
}

Example 5: GET api/what/ever/you/want?anything=something returns

"you have called Api::whatEver()"

*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Api');
$r->handle();

