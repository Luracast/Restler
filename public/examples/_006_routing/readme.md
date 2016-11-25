## Routing 

 This example requires `PHP >= 5.3` and taggeed under `routing` `get` `post` `put` `delete` `patch`


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
parameters will be mapped to query string, required primitive types,
objects and arrays will be mapped to request body.

>**Note:-** Required primitive types used to be mapped to url path. This behavior
> has changed in favour of better and readable urls.

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

### Wildcard Routes

Wildcard routes allows our api methods to receive variable number of parameters
they are manual routes that end with a star as the last path segment

For example

    @url GET custom/path/*


Take a look at the api class used here and compare it with the routes below to
understand.

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Api.php      (api)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET  api/all/*                     ⇠ Api::allIsMine()
    POST api/method                    ⇠ Api::postMethod()
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

GET [api/all/1/2/3/4/5/6/7](index.php/api/all/1/2/3/4/5/6/7)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"you have called Api::allIsMine(1, 2, 3, 4, 5, 6, 7)"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [api/all](index.php/api/all)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
"you have called Api::allIsMine()"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




We expect the following behaviour from this example.

```gherkin

@example6 @routing
Feature: Testing Routing Example

  Scenario: Testing So Many Ways
    Given that "p2" is set to 2
    When I request "/examples/_006_routing/api/somanyways/1{?p2}"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::soManyWays()"

  Scenario: Testing So Many Ways with two params
    When I request "/examples/_006_routing/api/somanyways/1/2"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::soManyWays()"

  Scenario: Testing So Many Ways with three params
    When I request "/examples/_006_routing/api/somanyways/1/2/3"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::soManyWays()"

  Scenario: Testing So Many Ways with more params
    When I request "/examples/_006_routing/api/somanyways/1/2/3/4"
    Then the response status code should be 404
    And the response is JSON

  Scenario: Ignoring required parameter should throw 400
    When I request "/examples/_006_routing/api/what/ever/you/want"
    Then the response status code should be 400
    And the response is JSON

  Scenario: Testing Wildcard route with 7 parameters
    When I request "/examples/_006_routing/api/all/1/2/3/4/5/6/7"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::allIsMine(1, 2, 3, 4, 5, 6, 7)"

  Scenario: Testing Wildcard route with 0 parameters
    When I request "/examples/_006_routing/api/all"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "you have called Api::allIsMine()"

```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_006_routing.feature
```



*[index.php]: _006_routing/index.php
*[Api.php]: _006_routing/Api.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

