Luracast Restler 3.0 RC3
========================

Restler is a simple and effective multi-format Web API Server written in PHP.

Just deal with your business logic in php, restler will take care of the REST!

### Restler 3 - *Better APIs by Design*

* [Developer Home](http://luracast.com/products/restler/)
* [Live Examples](http://bit.ly/Restler3LiveExamples)
* Updates on [Facebook](https://www.facebook.com/Luracast) and [Twitter](http://twitter.com/Luracast)
* [Features](#features)
* [Installation](#installation)
* [Quick Start Guide](#quick-start-guide)
* [Change Log](#change-log)

Features
--------

* No Learning Curve
* Light weight
* Flexible
* Highly Customizable
* Many Examples that can be tried on your localhost to get started
* Supports HTTP request methods  GET, POST, PUT, DELETE, and PATCH
* Supports both RESTful and Pragmatic REST API Design
* Clients can use X-HTTP-Method-Override header
* Two way format(media type) conversion
    * Pluggable Formatters
    * Comes with JSON, XML, Yaml, Amf, and Plist(both XML and Binary) formats
* Pluggable Authentication schemes
    * `[planned]` OAuth 2
* Pluggable Filters to effectively manage API usage
    * API Rate Limiting Filter
* Routing
    * Manual Routing
        * Using `@url GET my/custom/url/{param}` PHPDoc comments
    * Auto Routing
        * URL to Method mapping
        * URL part to Method parameter mapping
        * Query parameters to Method parameter mapping
        * Request body to Method parameter mapping
        * `[planned]` Header to Method parameter mapping
* Cache
    * Client Side Caching support
    * Proxy Caching support
    * Server Side Caching
        * `[planned]` ETag, If-None-Match support
        * `[planned]` Last-Modified, If-Modified-Since support
* API Features
    * Always supports URLEncoded format for simplified input (POST vars)
    * Automatic parameter validation and type conversion
    * API versioning support by URL and/or vendor specific MIME
    * API documentation and discovery using [Restler API Explorer](https://github.com/Luracast/Restler-API-Explorer)
    * Throttling
* Management
    * `[planned]` Unit Testing using [PHPUnit](https://github.com/sebastianbergmann/phpunit/)
    * Behavior Driven API testing using [Behat](http://behat.org/) and [Guzzle](https://github.com/guzzle/guzzle)
    * Command line Project Management using [Respect/Foundation](https://github.com/Respect/Foundation)
    * Dependency Management using [Composer](http://getcomposer.org/)
    * Source code distributed under LGPL

Installation
------------
Installation is a two step process. Do the following in the folder where you want Restler to be setup.

Before proceeding make sure your server has **PHP 5.3** or higher.

### 1.
[Download](https://github.com/Luracast/Restler/zipball/v3) and unpack Restler 3,
or Checkout using Terminal/Commandline

```console

git clone git://github.com/Luracast/Restler.git -b v3 ./

```
This creates the following folder structure

```
.
├── features
│   ├── bootstrap
│   ├── examples
│   └── restler
├── public
│   └── examples
│       ├── _001_helloworld
│       ├── _002_minimal
│       ├── _003_multiformat
│       ├── _004_error_response
│       ├── _005_protected_api
│       ├── _006_routing
│       ├── _007_crud
│       ├── _008_documentation
│       ├── _009_rate_limiting
│       ├── _010_access_control
│       └── resources
└── vendor
    └── Luracast
        └── Restler
```

### 2.
Download the dependencies using make at the Terminal/Commandline.
This step is only needed if you want to use Restler API Explorer or complex formats such as Plist, Yaml, and AMF as they depend on some third party libraries.
Otherwise you may safely skip this step.

```console

make composer-install

```
This expands the vendor folder as shown below

```
.
│
│
└── vendor
    ├── ChiperSoft
    │   └── CFPropertyList
    ├── Luracast
    │   ├── Restler
    │   └── explorer
    ├── behat
    │   ├── behat
    │   └── gherkin
    ├── composer
    ├── guzzle
    │   └── guzzle
    ├── symfony
    │   └── yaml
    └── zendframework
        └── zendamf
```

Now the vendor folder will have all dependencies.

### 3.
Ideally public folder should be mapped as your web root (optional, but recommended)

### 4.
Try the examples in your localhost

### 5.
Update the base_url specified in `behat.yml` and then try the following command

```console

bin/behat

```

This will test the examples against the behaviors expected, for example

```gherkin

Feature: Testing CRUD Example
    Scenario: Creating new Author with JSON
        Given that I want to make a new "Author"
        And his "name" is "Chris"
        And his "email" is "chris@world.com"
        And the request is sent as JSON
        When I request "/examples/_007_crud/authors"
        Then the response status code should be 200
        And the response should be JSON
        And the response has a "id" property

```

All set, Happy Restling! :)

Quick Start Guide
-----------------

Once you have got restler installed with the above steps, you can quickly create your application by following these steps

### 1.

Create your **API classes** with all needed public and protected methods



### 2.

Create the **gateway (index.php)** as follows

```php
<?php
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('YourApiClassNameHere'); // repeat for more
$r->handle(); //serve the response
```

### 3.

**Enable URL Rewriting**

Make sure all the requests are routed to index.php by enabling URL Rewriting for your website

For example:-

If you are on Apache, you can use an .htaccess file such as

```apache
DirectoryIndex index.php
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteRule ^$ index.php [QSA,L]
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
<IfModule mod_php5.c>
	php_flag display_errors On
</IfModule>
```

> **Note:-** This requires `AllowOverride` to be set to `All` instead of `None` in the `httpd.conf` file


### 4.

**Fine tune to suit your needs**


```php
<?php
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;
use Luracast\Restler\Defaults;
//set the defaults to match your requirements
Defaults::$throttle = 20; //time in milliseconds for bandwidth throttling
//setup restler
$r = new Restler();
$r->addAPIClass('YourApiClassNameHere'); // repeat for more
$r->addAPIClass('Resources'); //from restler framework for API Explorer
$r->addFilterClass('RateLimit'); //Add Filters as needed
$r->handle(); //serve the response
```

If you have successfully completed Installation Step 2, you should have
[Restler API Explorer](https://github.com/Luracast/Restler-API-Explorer) installed in `vendor/Luracast/explorer` folder
Create a symbolic link of `vendor/Luracast/explorer/dist` or copy the folder and name it as `explorer`

Place the explorer in the same folder as the `index.php`

Explore the api and try it out by openings `explorer/index.html` from the web root on your browser

Happy Exploring! :)

You may use the following php doc comments to annotate your methods.
All tags except @url can also be defined at the class level.

<table>
    <tr>
		<th>Tag</th>
		<th>Description</th>
	</tr>
	<tr>
		<td>@url</td>
		<td>
		Syntax:
		<pre>@url GET|POST|PUT|PATCH|DELETE custom/{dynamic}/route</pre>
		Example:
		<pre>@url POST authors/{id}/books</pre>
		Overrides auto routes and creates manual routes. use as many as you need
		</td>
	</tr>
	<tr>
        <td>@access</td>
        <td>
        Syntax:
        <pre>@access private|public|protected|hybrid</pre>
        Example:
        <pre>POST authors/{id}/books</pre>
        Access control for api methods. PHPDoc only supports private and public,
        Restler adds <b>protected</b> for api that needs authentication,
        <b>hybrid</b> for api that enhances resulting data for authenticated users.
        </td>
    </tr>
	<tr>
		<td>@smart-auto-routing</td>
		<td>
		Syntax:
        <pre>@smart-auto-routing true|false</pre>
        Example:
        <pre>@smart-auto-routing false</pre>
        Smart auto routing is enabled by default. Avoids creating multiple
        routes that can increase the ambiguity when set to true. when a method
        parameter is optional it is not mapped to the url and should only be
        used in request body or as query string `/resource?id=value`.
        When a parameter is required and is  scalar, it will be mapped as
        part of the url `/resource/{id}`
        </td>
	</tr>
	<tr>
		<td>@class</td>
		<td>
        Syntax:
        <pre>@class ClassName {@propertyName value}</pre>
        Example:
        <pre>@class AccessControl {@requires user} {@level 5}</pre>
        Inject property of the specified class with specified value
        </td>
	</tr>
	<tr>
		<td>@cache</td>
		<td>
        Syntax:
        <pre>@cache headerCacheControlValue</pre>
        Example:
        <pre>@cache max-age={expires}, must-revalidate</pre>
        Specify value to set CacheControl Header, it can use @expires value as
        shown in the example
        </td>
	</tr>
	<tr>
		<td>@expires</td>
		<td>
        Syntax:
        <pre>@expires numberOfSeconds</pre>
        Example:
        <pre>@expires 30</pre>
        Sets the content to expire immediately when set to zero alternatively
        you can specify the number of seconds the content will expire
        </td>
	</tr>
	<tr>
		<td>@throttle</td>
		<td>
        Syntax:
        <pre>@throttle numberOfMilliSeconds</pre>
        Example:
        <pre>@throttle 3000</pre>
        Sets the time in milliseconds for bandwidth throttling, which will
        become the minimum response time for each API request.
        </td>
	</tr>
	<tr>
		<td>@status</td>
		<td>
		Syntax:
        <pre>@status httpStatusCode</pre>
        Example:
        <pre>@status 201</pre>
        Sets the HTTP Status code for the successful response.
		</td>
	</tr>
	<tr>
		<td>@header</td>
		<td>
		Syntax:
        <pre>@header httpHeader</pre>
        Example:
        <pre>@header Link: &lt;meta.rdf>; rel=meta</pre>
        Sets or overrides the specific HTTP Header.
		</td>
	</tr>
	<tr>
		<td>@param</td>
		<td>
		Syntax:
        <pre>@param [type] Name [Description] {@name value}</pre>
        Example:
        <pre>@param int $num1 increment value {@min 5} {@max 100}</pre>
        Sets the HTTP Status code for the successful response.
		</td>
	</tr>
	<tr>
		<td>@throws</td>
		<td>
		Syntax:
        <pre>@throws httpStatusCode [Reason]</pre>
        Example:
        <pre>@throws 404 No Author for specified id</pre>
        Documents possible error responses for the API call.
		</td>
	</tr>
	<tr>
		<td>@return</td>
		<td>
		Syntax:
        <pre>@return type [Description]</pre>
        Example:
        <pre>@return Author an instance of iValueObject</pre>
        Documents the structure of success response, user defined classes must
        extend iValueObject.
		</td>
	</tr>
</table>

### 5.

Protect your api, authenticate and allow valid users

```php
<?php
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;
$r = new Restler();
$r->addAPIClass('YourApiClassNameHere'); // repeat for more
$r->addAuthenticationClass('CustomAuth'); //Add Authentication classes as needed
$r->handle(); //serve the response
```

### 6.

Turn on production mode

```php
<?php
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

//setup restler
$r = new Restler(true); //turns on production mode. make sure cache folder is writable
//...
```


Change Log
----------

### Changes from Restler 3.0 RC2

* Added Defaults::$cacheDirectory to set cache directory in one central place
* Added JSONP support with JsFormat class by extending JsonFormat.
* Fixes fatal error when the JSON sent in the request body is not an object or array
* Improves inline comment parsing by array conversion when delimiter is found and tag is not @pattern
* RateLimit class re-written to support all range of time units
  second|minute|hour|day|week|month to have fine grained control
* Resources class improved to include description for body parameters
* Fixes Resources not to include namespace when the return type is array of custom class
* Fixed Resource not to include the API of another class when the current api name is a begins with part of the other API
* Added two more ways to exclude API's from explorer/documentation
    * `Resources::$excludedHttpMethods` (array)
    * `Resources::$excludedPaths` (array)
* Fixes unescaped unicode bug in PHP < 5.4
* Fixes a bug with ValidationInfo parsing @choice inline comment
* Added Charset support
* Added Language (basic) support
* Updated the BDD tests to include new features
* Fixes a bug in Restler class which affects $_GET overriding `Defaults`
* Fixes a bug in XmlFormat parsing XML content to array
* Fixes a bug in unicode un-escaping for JsonFormat in PHP < 5.4
* Fixes the order so that responseFormat->setCharset is called before encoding the response



### Changes from Restler 3.0 RC1

* Filter classes can use authentication status and respond differently for
  authenticated users by implementing iUseAuthentication interface
* `RateLimit` class added to rate limit the api usage
* Fixed a bug with setCompatibilityMode
* Resources updated to use only paths for resource identification instead of class name
    * Enabled Access Control for Documentation
* Fixed CommentParser to ignore repeated white space so that it parses comments correctly
* Fixed comment parsing for @status and @expires tags
* Added the following Examples
    * Documentation
    * Rate Limit
    * Access Control
* CRUD example updated to include PATCH support



### Changes from Restler 2.0

**Restler 3.0** is completely rewritten from Restler 2.0 with best practices in mind for

* PHP Coding
* RESTfulness and/or Pragmatic REST
* API Design

**Restler 3.0**

* uses namespaces, Late Static Bindings, and Closures and thus it is **PHP 5.3+** only
  (if you need **PHP 5.0+** support use [Restler 2](https://github.com/Luracast/Restler/tree/v2))
* provides backward compatibility for Restler 1 and 2.
    Use `$r->setCompatibilityMode($version);`
* supports hybrid api which provides extended data to authenticated users
  Use `@access hybrid` PHPDoc comment
* uses smart auto routing by default where API method parameters that
  have default values are no longer mapped to the URL, instead they are
  mapped to query strings to reduce ambiguity in the url.
* supports `suppress_response_codes` as query string, when set to true;
  all http responses will be returned with HTTP OK with the errors in the
  body to accommodate mobile and less privileged clients.
* has improved `CommentParser` which adds support for embedded data in multiple formats
    * inline doc comments `{@name value}`
    * query string params \`\`\` param1=value&param2=value2\`\`\`
    * json \`\`\` {"parm1": value, "param2": value2}\`\`\` which can be placed in multi-lines
* has `Defaults` class with static properties that can be changed to suit the needs
* iAuthenticate is now using `__isAllowed` method instead of `__isAuthenticated` so that same
  class can be used for Authentication or Filtering
* iUseAuthentication interface added to help hybrid access api methods and filters to
  find out about user authentication status
* iFilter interface updated to provide charset support
* ...(more to follow)

### Changes from Restler 1.0

Restler 2.0 is a major rewrite to use convention over configuration and it is optimized 
for performance. Here are some of the major changes and improvements

* PHPDoc comments to map a method to URI are now optional.
* All public methods that does not begin with an underscore are mapped
  automatically to the method name (`gateway\classname\methodname\param1\...`)
* If we do not specify the second parameter for
  `$restler->addAPIClass` it will be mapped to the class name instead of mapping it to the root
* Restler 2 is written for PHP 5.3 and above but it make use of compat.php and work on
  any version of PHP starting from PHP 5.0