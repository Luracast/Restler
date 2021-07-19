![Restler](public/examples/resources/restler.svg) Luracast Restler
==================================================================
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/Luracast/Restler)
[![Latest Stable Version](https://poser.pugx.org/luracast/restler/v/stable.png)](https://packagist.org/packages/luracast/restler)
[![Total Downloads](https://poser.pugx.org/luracast/restler/downloads.png)](https://packagist.org/packages/luracast/restler)
[![Latest Unstable Version](https://poser.pugx.org/luracast/restler/v/unstable.png)](https://packagist.org/packages/luracast/restler)
[![License](https://poser.pugx.org/luracast/restler/license.png)](https://packagist.org/packages/luracast/restler)

### Version 5

> upgraded from version 3 RC6 for latest PHP support

Restler is a simple and effective multi-format Web API Server written in PHP.

Just deal with your business logic in php, restler will take care of the REST!

### Restler - *Better APIs by Design*

* [Developer Home](https://luracast.com/products/restler/)
* [Documentation](https://restler5.luracast.com/) with live examples
* Updates on [Facebook](https://www.facebook.com/Luracast) and [Twitter](https://twitter.com/Luracast)
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
* Supports HTTP request methods HEAD, GET, POST, PUT, DELETE, OPTIONS and PATCH via header or request parameter (method)
* Supports both RESTful and Pragmatic REST API Design
* Clients can use X-HTTP-Method-Override header, supports Cross Origin Resource Sharing and JSONP
* Two-way format(media type) conversion both send and receive
    * Pluggable content Formatter framework and api
    * Comes with JSON, XML, Yaml, Amf, and Plist(both XML and Binary) format support
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
* Cache built-in
    * Client Side Caching support
    * Proxy Caching support
* API Features
    * Always supports URLEncoded format for simplified input (POST vars)
    * Automatic parameter validation and type conversion
    * API versioning support by URL and/or vendor specific MIME
    * API documentation and discovery using [Restler API Explorer](https://github.com/Luracast/Restler-API-Explorer)
    * Throttling and Performance tuning
* Management
    * Behavior Driven API testing using [Behat](http://behat.org/) and [Guzzle](https://github.com/guzzle/guzzle)
    * Command line Project Management using [Respect/Foundation](https://github.com/Respect/Foundation)
    * Dependency Management using [Composer](http://getcomposer.org/)
    * Source code distributed under LGPL

Git Repository and the Branches
-------------------------------

1. Most stable and recent version is at the `master` branch, previous versions are in the version branches such
   as `v4`, `v3`, `v2`, and `v1`.

2. Version branch with the current version such as `v5` is used for building up the next release. It's documentation may
   not be updated frequently and thus reserved for the daring ones.

3. Feature branches such as `features/html` and `features/router` are purely for experimentation purpose to try out a
   feature. They may be merged when ready.

Test Drive
----------

Install this repository to try out the examples.

> Make sure PHP 5.4 or above is available on your server. We recommended using the latest version for better performance.

### 1. Install Composer

Restler uses [Composer](http://getcomposer.org/) to manage its dependencies. First, download a copy of `composer.phar`.
It can be kept in your project folder or ideally in `usr/local/bin` to use it globally for all your projects. If you are
on Windows, you can use the composer
[windows installer](https://getcomposer.org/Composer-Setup.exe) instead.

### 2. Install Restler

#### Option 1. Using composer create-project

You may install Restler by running the create project command in your terminal. Replace {projectName} with your actual
project name. It will create a folder with that name and install Restler.

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

#### Option 2. Downloading from GitHub

After installing Composer, download the [latest version]() of the Restler framework and extract its contents into a
directory on your server. Next, in the root of your Restler project, run the `php composer.phar install`
(or `composer install`) command to install all the framework's dependencies. This process requires Git to be installed
on the server to successfully complete the installation.

If you want to update the Restler framework, you may issue the
`php composer.phar update` command.

> **Note:-** If you are not allowed to install composer and git on your server, you
> can install and run them on your development machine. The resulting files and
> folders can be uploaded and used on the server.

### 3. Configure

Ideally public folder should be mapped as your web root, It is optional, but recommended avoiding exposing unneeded
files and folders.

### 4. Try it out

Try the live examples in your localhost. 
> You may launch the PHP's built-in server with `composer serve` command.

### 5. Run some test

Update the base_url specified in `behat.yml` and then try the following command

```console

vendor/bin/behat

```
> alternatively you can run `composer test`

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

All set, Happy RESTling! :)

Quick Start Guide
-----------------

We have two options to create your own restler api server

 1. Most convenient option is using application templates such as [Restler Application](https://github.com/Luracast/Restler-Framework) 
    which has integrations with many packages to help us with the business logic as well. 
    If you choose this option, select a branch in that repository and proceed with 
    the instructions available there.
    
 2. Create a project from scratch so that you have full control over every aspect of your application. 
    If you choose this option, follow along with the steps below.
    - create a folder to hold your project and open it in the terminal.
    - run `composer init` and follow along to create `composer.json`
    - when it is asking for dependencies, type `restler/framework` and `^5` for the version constraint.
    - alternatively, you can leave it blank and create the composer.json first and then run `composer require restler/framework:^5`
    
> we are using `restler/framework` instead of `luracast/restler` to reduce the space required for the package. 
> It is coming from https://github.com/Luracast/Restler-Framework it contains only the contents of src folder here.
    
> Even when you are building from scratch, checking out the application templates will help with folder structure 
> decisions and finding other useful packages.

### 1. Write API

Create your **API classes** with all needed public and protected methods

### 2. Open the Gateway

Create the **gateway (public/index.php)** as follows

```php
<?php
require_once __DIR__.'/../vendor/autoload.php';

use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('YourApiClassNameHere'); // repeat for more
$r->handle(); //serve the response
```

### 3. Prettify URLs

**Enable URL Rewriting**

Make sure all the requests go to index.php by enabling URL Rewriting for your website

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

If you are on Nginx, you have to make sure you set the `server_name` and pass the PHP scripts to fast cgi (PHP-FPM)
listening on 127.0.0.1:9000

```
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
```

> **Note:-** This requires PHP, PHP-FPM to be properly installed and configured.
> Refer to [PHP FastCGI](http://wiki.nginx.org/PHPFcgiExample) example for more
> info.

### 4. Customise

**Fine tune to suit your needs**

```php
<?php
require_once __DIR__.'/../vendor/autoload.php';
use Luracast\Restler\Restler;
use Luracast\Restler\Defaults;
//set the defaults to match your requirements
Defaults::$throttle = 20; //time in milliseconds for bandwidth throttling
//setup restler
$r = new Restler();
$r->addAPIClass('YourApiClassNameHere'); // repeat for more
$r->addAPIClass('Explorer'); //from restler framework for API Explorer
$r->addFilterClass('RateLimit'); //Add Filters as needed
$r->handle(); //serve the response
```

Explore the api and try it out by openings `explorer/index.html` from the web root on your browser

Happy Exploring! :)

> **Note:-** Using eAccelerator can make restler to fail as it removes the
> comments. More info can be found [here](http://wildlyinaccurate.com/eaccelerator-and-doctrine-2)

### 5. Annotate

Restler supports annotations in the form of PHPDoc comments for API fine tuning

They are documented in detail under [Annotations](ANNOTATIONS.md)

### 6. Authorize

In order to protect your api, authenticate and allow valid users

```php
<?php
require_once '../restler.php';
use Luracast\Restler\Restler;
$r = new Restler();
$r->addAPIClass('YourApiClassNameHere'); // repeat for more
$r->addAuthenticationClass('CustomAuth'); //Add Authentication classes as needed
$r->handle(); //serve the response
```

### 7. Start Production

By default Restler runs in debug mode more fine tuned for API developer, by showing detailed error messages and
prettifying the api result to human readbale form

By turning on production mode you will gain some performance boost as it will cache the routes (comment parsing happens
only once instead of every api call), few other files and avoid giving out debug information

```php
<?php
require_once '../restler.php';
use Luracast\Restler\Restler;

//setup restler

$r = new Restler(true); //turn on production mode by passing true.
//If you are using file based cache (the default) make sure cache folder is
//writable. when you make changes to your code make sure you delete the
// routes.php inside the cache folder
//...
```
>*Note:-* When production mode is set to `true` it always uses the cache and does not detect 
> changes and new routes if any. Your continuous integration pipeline or your git hook should delete 
> this file during the deployment process. Alternatively you can pass second parameter to restler
> constructor to refresh the cache when changes need to be applied.

Change Log
----------

### Restler 5

* Semantic versioning to move forward
* Support for PHP 8
* Corrects the source path to be outside the vendor directory
* Adds php development server support with `composer serve` command.
* Ability to run the tests with `composer test` command after running the server 
  with `composer serve` in another window.

### Restler 3.0 RC6

#### What's new

* Adds PassThrough class to serve files outside your web root, including secure downloads
* Adds Explorer class (v1 swagger 1.2 spec, and v2 swagger 2.0 spec) as a potential 
  replacement to Resources class (swagger 1.1 spec)
    * Explorer comes bundled with the html, css, and assets. So that you need not manually download and configure it
    * Explorer combines the parameters that are expected in the request body to create a unique model for swagger
    * Since Restler Explorer comes bundled, you can map to it to your url of choice. 
      For example `$restler->addAPIClass("Luracast/Restler/Explorer", 'swagger')` maps it to `/swagger`.
    * Explorer metadata can be easily customized with ExplorerInfo class

#### Improvements

* Routes class improved to provide a findAll method to list all the routes for a specific version of the API excluding
  the specified paths and http methods.
* The magic properties utilized by routes when found, ignoring actual properties. 
  This is useful for Dynamic Model classes such as Eloquent.
* Routes now allow `@required` and `@properties` to be arrays when the parameter is an object. 
  This helps us to pick and choose the properties for each api method differently.
  Example `{@properties property1,property2,property3}` `{@required property1,property2}` makes an api to only look for
  3 properties and 2 of them are required.

* Optimized the Nav class. It now makes use of `Routes::findAll()`, along with Explorer class
* Restler class has setBaseUrls method to set acceptable base urls that can be set using `$_SERVER['HTTP_HOST']`.
  Read [this article](http://shiflett.org/blog/2006/mar/server-name-versus-http-host) to understand why. This is useful
  in the following cases when
    * PHP has trouble detecting the port correctly
    * multiple domains are pointing to the same server

* Restler class now allows overriding the status code by setting `$this->restler->responseCode` from the api method.
* Improved Forms class to send the embedded properties to emmet template. For example

```
/**
 * {@id form1}
 *
 * @param string $name
 * @param int $age
*/
```

  Generates the following form

    <form role="form" id="form1" method="POST" ...

  because the emmet template has id in it (see below)

    form[role=form id=$id# name=$name# method=$method# action=$action# enctype=$enctype#]

* Forms class uses embedded properties with `@param` comments to set html attributes (for example id, accept etc) easily
* FormStyles improved.
* Validator is now initialized by scope so that we can set its properties with `@class` comment. **Example:
  -**  `@class Validator {@holdException}` makes the validator to hold the exceptions instead of throwing
* Improved Form validation with error messages for individual fields.
* Forms example updated to show validation errors with bootstrap based themes.
* CommentParser is now able to parse `@property`, `@property-read`, `@property-write` to support documenting the dynamic
  properties.
* CommentParser supports short array syntax such as `string[]`, `DateTime[]`
* Scope adds support for external DI Container of your choice with `Scope::$resolver` property.
* Renamed `String` class to `Text` for PHP 7 support (String is a reserved keyword in php7)
* Flash now implements ArrayAccess so that we can access flash variables just like an array
* **composer.json**: removed many dependencies from require-dev. Will prompt the developers to install them individually
  when they need them.
* newrelic support added.
* Memcache support added.
