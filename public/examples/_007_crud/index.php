<?php
/*
 Title: CRUD
 Tagline: using POST, GET, PUT and DELETE
 Tags: create, retrieve, read, update, delete, post, get, put, routing
 Requires: PHP >= 5.3
 Description: Create, Retrieve, Update and Delete using
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

 Example 1: GET authors returns

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

 Example 2: GET authors/2 returns

{
  "id": 2,
  "name": "Arul Kumaran",
  "email": "arul@luracast.com"
}

 Usage:

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
Accept: *//*
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
Accept: *//*
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
Accept: *//*
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

 Helpers: DB\Session

 Content:

*[MySQL.php]: _007_crud//DB/PDO/MySQL.php
*[Sqlite.php]: _007_crud/DB/PDO/Sqlite.php
*[SerializedFile.php]: _007_crud/DB/SerializedFile.php

*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Authors');
$r->handle();

