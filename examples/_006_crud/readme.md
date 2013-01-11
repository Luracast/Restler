CRUD
----

Create, Retrieve, Update and Delete using 
HTTP methods POST, GET, PUT and DELETE respectively. 

**How the automatic routing is done?**

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

For simplicity and making it work out of the box this example is using
a session based fake database, thus depending on a client that
supports PHP Session Cookies. You may use 
[REST Console](https://chrome.google.com/webstore/detail/faceofpmfclkengnkgkgjkcibdbhemoc#)
an extension for Chrome or 
[RESTClient](https://addons.mozilla.org/en-US/firefox/addon/restclient/) 
a firefox extension. 

Alternatively you can use [cURL](http://en.wikipedia.org/wiki/CURL) on the command line. 

	curl -X POST http://help.luracast.com/restler/examples/_006_crud/index.php/author -H "Content-Type: application/json" -d '{"name": "Another", "email": "another@email.com"}'
	
	{
     "name": "Another",
     "email": "another@email.com",
     "id": 5
	}

But since the session wont be working, next request wont reflect the 
change done by previous request, anyway you get the idea. You may use any of the following files 
instead of db_session.php to get full functionality. 

> * db_serialized_file.php (File)
> * db_pdo_sqlite.php      (SQlite)
> * db_pdo_mysql.php      (MySQL)

by uncommenting the respective line in author.php and commenting others.
> This API Server is made using the following php files/folders

> * index.php      (gateway)
> * author.php      (api)
> * db_session.php      (helper)
> * restler.php      (framework)

This API Server exposes the following URIs

	GET    author     ⇠ Author::get()
	GET    author/:id ⇠ Author::get()
	POST   author     ⇠ Author::post()
	PUT    author     ⇠ Author::put()
	PUT    author/:id ⇠ Author::put()
	DELETE author     ⇠ Author::delete()
	DELETE author/:id ⇠ Author::delete()


Try the following links in your browser

GET [author](index.php/author)
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

GET [author/2](index.php/author/2)
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

	POST /examples/_006_crud/index.php/author?name=Another&email=another@email.com HTTP/1.1
	Host: restler2.dev
	Content-Length: 0
	Accept-Language: en
	X-Requested-With: XMLHttpRequest
	User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1
	Content-Type: application/x-www-form-urlencoded; charset=UTF-8
	Accept: */*
	Accept-Encoding: gzip,deflate,sdch
	Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3
	Cookie: PHPSESSID=dcdfec433e86c1a6730f75303187071f

**Using post vars**

	POST /examples/_006_crud/index.php/author HTTP/1.1
	Host: restler2.dev
	Content-Length: 36
	Accept-Language: en
	X-Requested-With: XMLHttpRequest
	User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1
	Content-Type: application/x-www-form-urlencoded; charset=UTF-8
	Accept: */*
	Accept-Encoding: gzip,deflate,sdch
	Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3
	Cookie: PHPSESSID=dcdfec433e86c1a6730f75303187071f

	name=Another&email=another@email.com

**Using JSON data**

	POST /examples/_006_crud/index.php/author HTTP/1.1
	Host: restler2.dev
	Content-Length: 46
	Origin: chrome-extension://faceofpmfclkengnkgkgjkcibdbhemoc
	Accept-Language: en
	X-Requested-With: XMLHttpRequest
	User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1
	Content-Type: application/json; charset=UTF-8
	Accept: */*
	Accept-Encoding: gzip,deflate,sdch
	Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3
	Cookie: PHPSESSID=dcdfec433e86c1a6730f75303187071f
	
	{"name":"Another","email":"another@email.com"}
	

and the response could be

	HTTP/1.1 200 OK
	Date: Fri, 19 Aug 2011 16:34:41 GMT
	Server: Apache/2.2.19 (Unix) mod_ssl/2.2.19 OpenSSL/0.9.8r DAV/2 PHP/5.3.6 with Suhosin-Patch
	X-Powered-By: Luracast Restler v2.0.0
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

*[db_pdo_sqlite.php]: _006_crud/db_pdo_sqlite.php
*[db_serialized_file.php]: _006_crud/db_serialized_file.php
*[db_pdo_mysql.php]: _006_crud/db_pdo_mysql.php

*[index.php]: _006_crud/index.php
*[author.php]: _006_crud/author.php
*[db_session.php]: _006_crud/db_session.php
*[restler.php]: ../restler/restler.php
