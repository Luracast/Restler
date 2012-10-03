CRUD <requires>PHP >= 5.3</requires>
----
<tag>create</tag> <tag>retrieve</tag> <tag>read</tag> <tag>update</tag> <tag>delete</tag> <tag>post</tag> <tag>get</tag> <tag>put</tag> <tag>routing</tag> 

Create, Retrieve, Update and Delete using
 HTTP methods POST, GET, PUT and DELETE respectively.


Restler uses *get, put, post, and delete* as prefix to map PHP methods to
respective HTTP methods. When they are the only method names they map at
the class level similar to *index*

> **Note:-**
>
> 1. Read the [Routes](../_006_routing/readme.html) example for better understanding.
> 2. When we want the entire data that is sent to the API,
>    we need to use `$request_data` as the name of the parameter any other name
>    will only get partial data under the specified key

For simplicity and making it work out of the box this example is using
 a session based fake database, thus depending on a client that
 supports PHP Session Cookies. You may use
 [REST Console](https://chrome.google.com/webstore/detail/faceofpmfclkengnkgkgjkcibdbhemoc#)
 an extension for Chrome or
 [RESTClient](https://addons.mozilla.org/en-US/firefox/addon/restclient/)
 a firefox extension.

 Alternatively you can use [cURL](http://en.wikipedia.org/wiki/CURL) on the command line.

```bash
curl -X POST http://restler3.phpfogapp.com/examples/_007_crud/index.php/authors -H "Content-Type: application/json" -d '{"name": "Another", "email": "another@email.com"}'

{
  "name": "Another",
  "email": "another@email.com",
  "id": 5
}
```

But since the session wont be working, next request wont reflect the
change done by previous request, anyway you get the idea. You may use any of the following files
instead of Session.php to get full functionality.

> * SerializedFile.php (helper)
> * Sqlite.php (helper)
> * MySQL.php (helper)

by un-commenting the respective line in Authors.php and commenting others.

> This API Server is made using the following php files/folders
> 
> * index.php      (gateway)
> * Authors.php      (api)
> * Session.php      (helper)
> * restler.php      (framework)
> * JsonFormat.php      (format)

This API Server exposes the following URIs

    GET    authors      ⇠ Authors::index()
    GET    authors/{id} ⇠ Authors::get()
    POST   authors      ⇠ Authors::post()
    PUT    authors/{id} ⇠ Authors::put()
    DELETE authors/{id} ⇠ Authors::delete()


*[MySQL.php]: _007_crud//DB/PDO/MySQL.php
*[Sqlite.php]: _007_crud/DB/PDO/Sqlite.php
*[SerializedFile.php]: _007_crud/DB/SerializedFile.php



Try the following links in your browser

GET [authors](index.php/authors)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
[
  {
    "id": 1,
    "name": "Jac Wright",
    "email": "jacwright@gmail.com"
  },
  {
    "id": 2,
    "name": "Arul Kumaran",
    "email": "arul@luracast.com"
  }
]
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

GET [authors/2](index.php/authors/2)
:    
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{
  "id": 2,
  "name": "Arul Kumaran",
  "email": "arul@luracast.com"
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



###Creating new Author

 Typical post request to create a new author will be any of the following

**Using query parameters**

```http
POST /examples/_007_crud/index.php/authors?name=Another&email=another@email.com HTTP/1.1
Host: restler3.dev
Content-Length: 0
Accept-Language: en
X-Requested-With: XMLHttpRequest
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
Accept: /*
Accept-Encoding: gzip,deflate,sdch
Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3
Cookie: PHPSESSID=dcdfec433e86c1a6730f75303187071f
```

**Using post vars**

```http
POST /examples/_007_crud/index.php/authors HTTP/1.1
Host: restler3.dev
Content-Length: 36
Accept-Language: en
X-Requested-With: XMLHttpRequest
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
Accept: /*
Accept-Encoding: gzip,deflate,sdch
Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3
Cookie: PHPSESSID=dcdfec433e86c1a6730f75303187071f

name=Another&email=another@email.com
```

**Using JSON data**

```http
POST /examples/_007_crud/index.php/authors HTTP/1.1
Host: restler3.dev
Content-Length: 46
Origin: chrome-extension://faceofpmfclkengnkgkgjkcibdbhemoc
Accept-Language: en
X-Requested-With: XMLHttpRequest
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1
Content-Type: application/json; charset=UTF-8
Accept: /*
Accept-Encoding: gzip,deflate,sdch
Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3
Cookie: PHPSESSID=dcdfec433e86c1a6730f75303187071f

{"name":"Another","email":"another@email.com"}
```
and the response for all the above could be

```http
HTTP/1.1 200 OK
Date: Tue, 25 Sep 2012 10:05:06 GMT
Server: Apache/2.2.19 (Unix) mod_ssl/2.2.19 OpenSSL/0.9.8r DAV/2 PHP/5.3.6 with Suhosin-Patch
X-Powered-By: Luracast Restler v3.0.0
Expires: 0
Cache-Control: no-cache, must-revalidate
Pragma: no-cache
Content-Length: 66
Content-Type: application/json

{
  "name": "Another",
  "email": "another@email.com",
  "id": 7
}
```
We expect the following behaviour from this example.

```gherkin

@example7 @crud
Feature: Testing CRUD Example

  Scenario: Creating new Author by POSTing vars
    Given that I want to make a new "Author"
    And his "name" is "Chris"
    And his "email" is "chris@world.com"
    When I request "/examples/_007_crud/authors"
    Then the response status code should be 200
    And the response should be JSON
    And the response has a "id" property

  Scenario: Creating new Author with JSON
    Given that I want to make a new "Author"
    And his "name" is "Chris"
    And his "email" is "chris@world.com"
    And the request is sent as JSON
    When I request "/examples/_007_crud/authors"
    Then the response status code should be 200
    And the response should be JSON
    And the response has a "id" property

  Scenario: Updating Author with JSON
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "/examples/_007_crud/authors/{id}"
    Then the response status code should be 200
    And the response should be JSON
    And the response has a "id" property

  Scenario: Failing to update Author with JSON
    Given that I want to update "Author"
    And his "name" is "Jac"
    And his "email" is "jac@wright.com"
    And his "id" is 1
    And the request is sent as JSON
    When I request "/examples/_007_crud/authors"
    Then the response status code should be 404

  Scenario: Deleting Author
    Given that I want to delete an "Author"
    And his "id" is 1
    When I request "/examples/_007_crud/authors/{id}"
    Then the response status code should be 200
    And the response should be JSON
    And the response has an "id" property
```

It can be tested by running the following command on terminal/command line
from the project root (where the vendor folder resides). Make sure `base_url`
in `behat.yml` is updated according to your web server.

```bash
bin/behat  features/examples/_007_crud.feature
```



*[index.php]: _007_crud/index.php
*[Authors.php]: _007_crud/Authors.php
*[Session.php]: _007_crud/DB/Session.php
*[restler.php]: ../../vendor/restler.php
*[JsonFormat.php]: ../../vendor/Luracast/Restler/Format/JsonFormat.php

