![Restler](public/examples/resources/Restler.png) Luracast Restler
==================================================================
[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/Luracast/Restler?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Latest Stable Version](https://poser.pugx.org/luracast/restler/v/stable.png)](https://packagist.org/packages/luracast/restler) [![Total Downloads](https://poser.pugx.org/luracast/restler/downloads.png)](https://packagist.org/packages/luracast/restler) [![Latest Unstable Version](https://poser.pugx.org/luracast/restler/v/unstable.png)](https://packagist.org/packages/luracast/restler) [![License](https://poser.pugx.org/luracast/restler/license.png)](https://packagist.org/packages/luracast/restler)

### Version 3.0 Release Candidate 5

Restler is a simple and effective multi-format Web API Server written in PHP.

Just deal with your business logic in php, restler will take care of the REST!

> if you do not have PHP >= 5.3.2 on your server and wont be able to upgrade you may
> use [Restler 2](https://github.com/Luracast/Restler/tree/v2) instead

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
* Supports HTTP request methods HEAD, GET, POST, PUT, DELETE, OPTIONS and PATCH
  via header or request parameter (method)
* Supports both RESTful and Pragmatic REST API Design
* Clients can use X-HTTP-Method-Override header, supports Cross Origin Resource
  Sharing and JSONP
* Two way format(media type) conversion both send and receive
    * Pluggable content Formatter framework and api
    * Comes with JSON, XML, Yaml, Amf, and Plist(both XML and Binary) format
      support
* Pluggable Authentication schemes
    * OAuth 2 Server
* Pluggable Filters to effectively manage API usage
    * API Rate Limiting Filter
* Routing
    * Manual Routing (Annotation)
        * Using `@url GET my/custom/url/{param}` PHPDoc comments
    * Auto Routing (Reflection)
        * URL to Method mapping
        * URL part to Method parameter mapping
        * Query parameters to Method parameter mapping
        * Request body to Method parameter mapping
        * `[planned]` Header to Method parameter mapping
* Cache built-in
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
    * Throttling and Performance tuning
* Management
    * `[planned]` Unit Testing using [PHPUnit](https://github.com/sebastianbergmann/phpunit/)
    * Behavior Driven API testing using [Behat](http://behat.org/) and [Guzzle](https://github.com/guzzle/guzzle)
    * Command line Project Management using [Respect/Foundation](https://github.com/Respect/Foundation)
    * Dependency Management using [Composer](http://getcomposer.org/)
    * Source code distributed under LGPL


Git Repository and the Branches
-------------------------------

1. Most stable and recent version is maintained at the `master` branch, previous
   versions are kept in the branches such as `v1` and `v2`

2. Version branch with the current version such as `v3` is used for building up
   the next release. It's documentation may not be updated frequently and thus
   reserved for the daring ones.

3. Feature branches such as `features/html` and `features/router` are purely
   for experimentation purpose to try out a feature


Installation
------------

Make sure PHP 5.3.2 or above (at least 5.3.4 recommended to avoid potential bugs)
is available on your server

### 1. Install Composer

Restler uses [Composer](http://getcomposer.org/) to manage its dependencies.
First, download a copy of `composer.phar`. It can be kept in your project folder
or ideally in `usr/local/bin` to use it globally for all your projects. If you
are on Windows, you can use the composer
[windows installer](https://getcomposer.org/Composer-Setup.exe) instead.

### 2. Install Restler

#### Option 1. Using composer create-project

You may install Restler by running the create project command in your terminal.
Replace {projectName} with your actual project name. It will create a folder
with that name and install Restler.

```console
php composer.phar create-project luracast/restler {projectName}
```
> **Note:-**
>
> 1. If you do not want the additional formats and BDD tools you can include
>    `--no-dev` to enforce exclusion of dev packages.
>
> 2. If you want to try the bleading edge v3 branch or any of the feature
>    branches include `3.x-dev` or `dev-features/html` in the above command

#### Option 2. Downloading from github

Once Composer is installed, download the [latest version]() of the Restler
framework and extract its contents into a directory on your server. Next, in the
root of your Restler project, run the `php composer.phar install`
(or `composer install`) command to install all of the framework's dependencies.
This process requires Git to be installed on the server to successfully complete
the installation.

If you want to update the Restler framework, you may issue the
`php composer.phar update` command.

> **Note:-** If are not allowed to install composer and git on your server, you
> can install and run them on your development machine. The resulting files and
> folders can be uploaded and used on the server.


### 3. Configure

Ideally public folder should be mapped as your web root, It is optional, but
recommended to avoid exposing unneeded files and folders.

### 4. Try it out

Try the live examples in your localhost

### 5. Run some test

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

Once you have got restler installed with the above steps, you can quickly create
your application by following these steps

### 1. Write API

Create your **API classes** with all needed public and protected methods


### 2. Open the Gateway

Create the **gateway (index.php)** as follows

```php
<?php
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('YourApiClassNameHere'); // repeat for more
$r->handle(); //serve the response
```

### 3. Prettify URLs

**Enable URL Rewriting**

Make sure all the requests are routed to index.php by enabling URL Rewriting for
your website

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

> **Note:-** This requires `AllowOverride` to be set to `All` instead of `None`
> in the `httpd.conf` file, and might require some tweaking on some server
> configurations. Refer to [mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html)
> documentation for more info.

If you are on Nginx, you have to make sure you set the `server_name` and pass the
PHP scripts to fast cgi (PHP-FPM) listening on 127.0.0.1:9000

    server {
            listen        80;
            server_name   api.luracast.com; //change it to match your server name

            //... other stuff

            location ~ \.php$ {
                root           /var/www/html;
                fastcgi_pass   127.0.0.1:9000;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME  /var/www/html/$fastcgi_script_name;
                include        fastcgi_params;
            }

            //... other stuff

    }

> **Note:-** This requires PHP, PHP-FPM to be properly installed and configured.
> Refer to [PHP FastCGI](http://wiki.nginx.org/PHPFcgiExample) example for more
> info.


### 4. Customise

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
[Restler API Explorer](https://github.com/Luracast/Restler-API-Explorer) installed
in `vendor/Luracast/explorer` folder. Create a symbolic link of
`vendor/Luracast/explorer/dist` or copy the folder and name it as `explorer`

Place the explorer in the same folder as the `index.php`

Explore the api and try it out by openings `explorer/index.html` from the web
root on your browser

Happy Exploring! :)

> **Note:-** Using eAccelerator can make restler to fail as it removes the
> comments. More info can be found [here](http://wildlyinaccurate.com/eaccelerator-and-doctrine-2)

### 5. Annotate

Restler supports annotations in the form of PHPDoc comments for API fine tuning

They are documented in detail under [Annotations](ANNOTATIONS.md)

### 6. Authorize

In order to protect your api, authenticate and allow valid users

```php
<?php
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;
$r = new Restler();
$r->addAPIClass('YourApiClassNameHere'); // repeat for more
$r->addAuthenticationClass('CustomAuth'); //Add Authentication classes as needed
$r->handle(); //serve the response
```

### 7. Start Production

By default Restler runs in debug mode more fine tuned for API developer, by
showing detailed error messages and prettifying the api result to human readbale
form

By turning on production mode you will gain some performance boost as it will
cache the routes (comment parsing happens only once instead of every api call),
few other files and avoid giving out debug information

```php
<?php
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

//setup restler

$r = new Restler(true); //turn on production mode by passing true.
//If you are using file based cache (the default) make sure cache folder is
//writable. when you make changes to your code make sure you delete the
// routes.php inside the cache folder
//...
```


Change Log
----------
### Restler 3.0 RC5

 * Scope (an dependency injection container) is added. It's register method allows adding api classes that has some dependencies.
 * Improves HtmlFormat to support blade templates and makes it easily extendable to add your own templates.
 * HtmlFormat::$format has been renamed as HtmlFormat::$template for better clarrity
 * HtmlFormat now supports auto templating to load relevant template for an API method based on the mapped url.
 * Tag, a utility class for generating html tags in object oriented way.
 * Emmet class that extends a subset of emmet to create a a template engine.
 * Forms class to auto generate forms for any API method prebuilt in HTML5, Twitter Bootstrap 3, Zurb Foundation formats.
 * Validator improved to allow suppressing validation errors from throwing exceptions immediatly, so that API call can reach the API method
 * Validator improved to be form validation friendly.
 * Nav class creating html navigation interface.
 * OAuth examles updgrated to use version 1.0 of OAuth2 library.
 * Many bug fixes and improvements.


### Restler 3.0 RC4

 * `$reques_data` in api method parameters and `getRequestData()` on restler
   instance now excludes `$_GET` parameters.
 * Returning null from api method now excludes the response body. This behaviour
   can be changed by setting `Defaults::$emptyBodyForNullResponse` to false.
 * Added many api examples under tests folder for testing feature by feature
   using BDD
 * Support for custom class parameters and array of custom class parameters
 * Ability to pass the parameter directly as the body of the request when it is
   the only parameter
 * Fixes to composer.json and publish stable release as composer package on
   packagist.
 * New Routes class with improved routing, including wild card routes and smart
   routing based on paramter type.
 * Possibility to use any autoloader including composer's autoloader for maximum
   interoperability
 * Moved to using the [rodneyrehm/plist](https://packagist.org/packages/rodneyrehm/plist)
   package for CFPropertyList.
 * Removed required packages as they are not technically "required" per se,
   Restler works out of the box.
 * Created supported packages as require-dev instead which will be installed via
   `composer install --dev`
 * Added suggested section for all the supported packages.
 * Added keywords to package descriptor
 * Added branch alias to indicate that v3 is the snapshot for v3.0.x-dev
 * Released Restler as package on packagist.

### Restler 3.0 RC3

* Added Defaults::$cacheDirectory to set cache directory in one central place
* Added JSONP support with JsFormat class by extending JsonFormat.
* Fixes fatal error when the JSON sent in the request body is not an object or
  array
* Improves inline comment parsing by array conversion when delimiter is found
  and tag is not @pattern
* RateLimit class re-written to support all range of time units
  second|minute|hour|day|week|month to have fine grained control
* Resources class improved to include description for body parameters
* Fixes Resources not to include namespace when the return type is array of
  custom class
* Fixed Resource not to include the API of another class when the current api
  name is a begins with part of the other API
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
* Added support for JSONP via jsFormat extension of JsonFormat
* Fixes a bug in unicode un-escaping for JsonFormat in PHP < 5.4
* Fixes the order so that responseFormat->setCharset is called before encoding
  the response
* Documentation improvements and minor bug fixes

### Restler 3.0 RC2

* Filter classes can use authentication status and respond differently for
  authenticated users by implementing iUseAuthentication interface
* `RateLimit` class added to rate limit the api usage
* Fixed a bug with setCompatibilityMode
* Resources updated to use only paths for resource identification instead of
  class name
    * Enabled Access Control for Documentation
* Fixed CommentParser to ignore repeated white space so that it parses comments
  correctly
* Fixed comment parsing for @status and @expires tags
* Added the following Examples
    * Documentation
    * Rate Limit
    * Access Control
* CRUD example updated to include PATCH support



### Restler 3.0

**Restler 3.0** is completely rewritten from Restler 2.0 with best practices in
mind for

* PHP Coding
* RESTfulness and/or Pragmatic REST
* API Design

**Restler 3.0**

* uses namespaces, Late Static Bindings, and Closures and thus it is **PHP 5.3+**
  only (if you need **PHP 5.0+** support use [Restler 2](https://github.com/Luracast/Restler/tree/v2))
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
* has improved `CommentParser` which adds support for embedded data in multiple
  formats
    * inline doc comments `{@name value}`
    * query string params \`\`\` param1=value&param2=value2\`\`\`
    * json \`\`\` {"param1": value, "param2": value2}\`\`\` which can be placed
      in multi-lines
* has `Defaults` class with static properties that can be changed to suit the
  needs
* iAuthenticate is now using `__isAllowed` method instead of `__isAuthenticated`
  so that same
  class can be used for Authentication or Filtering
* iUseAuthentication interface added to help hybrid access api methods and
  filters to find out about user authentication status
* iFilter interface updated to provide charset support
* ...(more to follow)

### Restler 2.0

Restler 2.0 is a major rewrite to use convention over configuration and it is
optimized for performance. Here are some of the major changes and improvements

* PHPDoc comments to map a method to URI are now optional.
* All public methods that does not begin with an underscore are mapped
  automatically to the method name (`gateway\classname\methodname\param1\...`)
* If we do not specify the second parameter for
  `$restler->addAPIClass` it will be mapped to the class name instead of mapping
   it to the root
* Restler 2 is written for PHP 5.3 and above but it make use of compat.php and
  work on any version of PHP starting from PHP 5.0
