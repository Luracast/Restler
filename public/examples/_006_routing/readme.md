Routing <requires>PHP >= 5.3</requires>
-------
<tag>routing</tag> <tag>get</tag> <tag>post</tag> <tag>put</tag> <tag>delete</tag> <tag>patch</tag> 

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

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Api.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    POST api/method/{param1}           ⇠ Api::postMethod()
    POST api/method2                   ⇠ Api::whatEver()
    POST api/method2/{anything}        ⇠ Api::whatEver()
    GET  api/somanyways                ⇠ Api::soManyWays()
    GET  api/somanyways/{p1}           ⇠ Api::soManyWays()
    GET  api/somanyways/{p1}/{p2}      ⇠ Api::soManyWays()
    GET  api/somanyways/{p1}/{p2}/{p3} ⇠ Api::soManyWays()
    GET  api/what/ever/you/want        ⇠ Api::whatEver()






Try the following links in your browser

GET [api/somanyways/1?p2=something](index.php/api/somanyways/1?p2=something)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"you have called Api::soManyWays()"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [api/somanyways/1/2](index.php/api/somanyways/1/2)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"you have called Api::soManyWays()"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [api/somanyways/1/2/3](index.php/api/somanyways/1/2/3)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"you have called Api::soManyWays()"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [api/what/ever/you/want](index.php/api/what/ever/you/want)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "error": {
    "code": 400,
    "message": "Bad Request: anything is missing."
  }
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [api/what/ever/you/want?anything=something](index.php/api/what/ever/you/want?anything=something)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"you have called Api::whatEver()"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~





*[index.php]: _006_routing/index.php
*[Api.php]: _006_routing/Api.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

