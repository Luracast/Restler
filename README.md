# ![Restler](public/examples/resources/restler.svg) Luracast Restler

[![Latest Stable Version](https://poser.pugx.org/luracast/restler/v/stable.png)](https://packagist.org/packages/luracast/restler)
[![Total Downloads](https://poser.pugx.org/luracast/restler/downloads.png)](https://packagist.org/packages/luracast/restler)
[![License](https://poser.pugx.org/luracast/restler/license.png)](https://packagist.org/packages/luracast/restler)

### Version 6.0 - Production Ready 🚀

> Major upgrade with PHP 8+ support, async capabilities, and modern server support

Restler is a simple and effective multi-format Web API Server framework written in PHP.

Just deal with your business logic in PHP, Restler will take care of the REST!

### Restler - *Better APIs by Design*

* [Features](#features)
* [What's New in v6](#whats-new-in-v6)
* [Installation](#installation)
* [Quick Start Guide](#quick-start-guide)
* [Migration from v5](#migration-from-v5)
* [Documentation](#documentation)
* [Change Log](#change-log)

---

## What's New in V6

### 🚀 Modern PHP Support
* **PHP 8.0+** required - takes full advantage of modern PHP features
* **PHP 8 Attributes** support for cleaner API definitions
* **Named Arguments** support
* **Union Types** and **Nullable Types**
* **PSR-7** HTTP message interfaces
* **PSR-11** Container implementation

### ⚡ Async & Performance
* **ReactPHP** HTTP server support for async operations
* **Swoole / OpenSwoole** server support for high performance (both supported)
* **Workerman** server support
* **AWS Lambda** support via Bref
* **Chunked streaming** responses for large datasets
* **Generator-based** data processing

### 🔒 Enhanced Security
* Improved session handling with secure serialization
* JSONP callback validation
* Better CORS support with configurable headers
* Template rendering hardening

### 🎯 Developer Experience
* Modern dependency injection container
* Better error messages and debugging
* Improved type safety and validation
* GraphQL support
* Excel file export support
* Enhanced HTML template rendering (Blade, Twig, Mustache, PHP)

### 🌐 Multi-Format Support
* JSON (default)
* XML
* YAML
* CSV with streaming support
* AMF
* Plist (XML and Binary)
* HTML with multiple template engines
* Excel (XLSX)

---

## Features

* **Zero Learning Curve** - If you know PHP, you know Restler
* **Light weight** - Minimal dependencies
* **Flexible** - Adapt to your needs
* **Highly Customizable** - Everything can be overridden
* **Production Ready** - Used in production environments
* **Comprehensive Examples** - Try them on your localhost

### HTTP Support
* Supports HTTP methods: HEAD, GET, POST, PUT, DELETE, OPTIONS, PATCH
* Method override via header or request parameter
* Cross Origin Resource Sharing (CORS)
* JSONP support with validation

### Routing
* **Manual Routing** - Via annotations `@url GET my/custom/url/{param}`
* **Auto Routing** - URL to Method mapping via reflection
* **Smart Routing** - Intelligent parameter mapping
* **Versioning** - API versioning by URL and/or vendor MIME types

### Content Negotiation
* Two-way format conversion (send and receive)
* Pluggable content formatter framework
* Multiple template engines for HTML responses

### Security & Auth
* Pluggable authentication schemes
* OAuth 2 Server support
* Session management
* API Rate Limiting
* Access control

### Caching
* Client-side caching support
* Proxy caching support
* Route caching in production mode

### Validation & Types
* Automatic parameter validation
* Type conversion and coercion
* Custom validation rules
* Request body mapping

### Documentation & Testing
* OpenAPI 3.0 (Swagger) support
* API Explorer integration
* Behavior Driven Development with Behat
* Comprehensive test coverage

### Deployment Options
* PHP Built-in Server (development)
* Apache / Nginx (traditional)
* ReactPHP (async)
* Swoole / OpenSwoole (high performance)
* Workerman (event-driven)
* AWS Lambda (serverless)

---

## Installation

### Requirements

* **PHP 8.0** or higher
* **ext-json** extension
* Composer

### Install via Composer

```bash
composer require luracast/restler:^6.0
```

### Or Clone and Install

```bash
git clone -b v6 https://github.com/Luracast/Restler.git
cd Restler
composer install
```

---

## Quick Start Guide

### 1. Create Your API Class

```php
<?php
// api/Hello.php

class Hello
{
    /**
     * Say hello
     *
     * @param string $name Name of the person {@from path}
     * @return string greeting message
     */
    public function sayHello(string $name = 'World'): string
    {
        return "Hello $name!";
    }

    /**
     * Get user information
     *
     * @param int $id User ID {@from path}
     * @return array user data
     */
    public function getUser(int $id): array
    {
        return [
            'id' => $id,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];
    }
}
```

### 2. Create the Gateway (public/index.php)

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Hello');
$r->handle();
```

### 3. Configure URL Rewriting

#### Apache (.htaccess)

```apache
DirectoryIndex index.php
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ index.php [QSA,L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### PHP Built-in Server (Development)

```bash
composer serve
# or
php -S localhost:8080 -t public server.php
```

### 4. Try It Out

```bash
# Say hello
curl http://localhost:8080/hello/sayHello/Restler

# Get user
curl http://localhost:8080/hello/getUser/123
```

---

## Advanced Usage

### Production Mode

```php
<?php
use Luracast\Restler\Restler;
use Luracast\Restler\Defaults;

// Configure defaults
Defaults::$throttle = 20; // bandwidth throttling in ms
Defaults::$cacheDirectory = __DIR__ . '/../cache';

// Enable production mode
$r = new Restler(true); // true = production mode
$r->addAPIClass('Hello');
$r->handle();
```

**Important:** In production mode, routes are cached. Delete `cache/routes.php` after code changes.

### Multiple Formats

```php
<?php
$r = new Restler();
$r->addAPIClass('Hello');
$r->handle();
```

Now your API automatically supports:

```bash
# JSON (default)
curl http://localhost:8080/hello/getUser/1

# XML
curl http://localhost:8080/hello/getUser/1.xml

# YAML
curl http://localhost:8080/hello/getUser/1.yaml

# CSV
curl http://localhost:8080/hello/getUsers.csv
```

### Authentication

```php
<?php
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAuthenticationClass('CustomAuth');
$r->addAPIClass('ProtectedAPI');
$r->handle();
```

```php
<?php
// CustomAuth.php
use Luracast\Restler\Contracts\AuthenticationInterface;

class CustomAuth implements AuthenticationInterface
{
    public function __isAuthenticated(): bool
    {
        // Your auth logic here
        return isset($_SERVER['HTTP_AUTHORIZATION']);
    }
}
```

### Rate Limiting

```php
<?php
$r = new Restler();
$r->addFilterClass('RateLimit');
$r->addAPIClass('Hello');
$r->handle();
```

### API Explorer

```php
<?php
use Luracast\Restler\OpenApi3\Explorer;

$r = new Restler();
$r->addAPIClass('Hello');
$r->addAPIClass(Explorer::class, 'explorer'); // Maps to /explorer
$r->handle();
```

Then visit: `http://localhost:8080/explorer`

---

## Alternative Server Options

### ReactPHP (Async)

```bash
composer react-server
# or
php public/index_react.php
```

### Swoole / OpenSwoole (High Performance)

Restler supports both **Swoole** and **OpenSwoole** (they're interchangeable - use either one):

```bash
# Install Swoole extension (choose one)
pecl install swoole
# OR
pecl install openswoole

# Start server
composer swoole-server
# or
php public/index_swoole.php
```

**Note**: OpenSwoole is a fork of Swoole with the same API. Install only one, not both.

### Workerman (Event-Driven)

```bash
composer workerman-server
# or
php public/index_workerman.php start
```

### AWS Lambda (Serverless)

See `public/index_lambda.php` for Lambda integration example.

---

## Migration from v5

### Breaking Changes

1. **PHP Version**: Minimum PHP 8.0 (was PHP 7.4 in v5)
2. **Namespaces**: Some classes reorganized under `Luracast\Restler\`
3. **Type Hints**: Stricter type enforcement
4. **PSR Compliance**: PSR-7, PSR-11 now required

### Quick Migration Steps

1. **Update composer.json**:
   ```json
   {
     "require": {
       "luracast/restler": "^6.0"
     }
   }
   ```

2. **Update PHP version** to 8.0+

3. **Update type hints** in your API classes:
   ```php
   // v5
   public function getUser($id) { }

   // v6
   public function getUser(int $id): array { }
   ```

4. **Test thoroughly** - Run your test suite

See [MIGRATION.md](MIGRATION.md) for detailed migration guide.

---

## Documentation

* [Annotations Reference](ANNOTATIONS.md) - All supported PHPDoc annotations
* [Parameter Handling](PARAM.md) - @param and @var attributes
* [Request/Response Stages](STAGES.md) - Understanding the request lifecycle
* [Forms](FORMS.md) - Working with HTML forms
* [Composer Integration](COMPOSE.md) - Advanced composer usage
* [Security](SECURITY.md) - Security best practices

---

## Testing

### Run Test Suite

```bash
# Start server
composer serve

# In another terminal
composer test
```

### Example Test (Behat)

```gherkin
Feature: Testing Hello API

  Scenario: Say hello
    When I request "hello/sayHello/Restler"
    Then the response status code should be 200
    And the response is JSON
    And the value equals "Hello Restler!"
```

---

## Examples

The repository includes 18+ working examples:

* **_001_helloworld** - Basic API
* **_002_minimal** - Minimal setup
* **_003_multiformat** - Multiple formats
* **_004_error_response** - Error handling
* **_005_protected_api** - Authentication
* **_006_routing** - Custom routing
* **_007_crud** - CRUD operations
* **_008_documentation** - API docs
* **_009_rate_limiting** - Rate limits
* **_010_access_control** - Access control
* **_011_versioning** - API versioning
* **_012_vendor_mime** - Vendor MIME types
* **_013_html** - HTML responses
* **_014_oauth2_client** - OAuth2 client
* **_015_oauth2_server** - OAuth2 server
* **_016_forms** - HTML forms
* **_017_navigation** - Navigation
* **_018_graphql** - GraphQL support

Try them at: `http://localhost:8080/examples/`

---

## Change Log

### Restler 6.0.0

#### Major Features
* **PHP 8.0+** required with full PHP 8 support
* **PSR-7** HTTP message interfaces
* **PSR-11** Container interface
* **Async support** via ReactPHP, Swoole/OpenSwoole, Workerman
* **AWS Lambda** support
* **GraphQL** integration
* **Excel** export support
* **Chunked streaming** for large datasets

#### Security Improvements
* Secure session serialization (JSON instead of unserialize)
* JSONP callback validation
* Template rendering hardening (EXTR_SKIP)
* Improved CORS handling

#### Performance
* Route caching improvements
* Generator-based streaming
* Async server support
* Better memory management

#### Developer Experience
* Modern DI container
* Better error messages
* Improved type safety
* Enhanced debugging
* Comprehensive examples

#### Bug Fixes
* Fixed deprecated PHP 8 reflection methods
* Fixed missing interface errors
* Fixed hardcoded CORS values
* Numerous stability improvements

See [full changelog](CHANGELOG.md) for details.

---

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development

```bash
git clone https://github.com/Luracast/Restler.git
cd Restler
git checkout v6
composer install
composer serve
```

---

## Support

* **Issues**: [GitHub Issues](https://github.com/Luracast/Restler/issues)
* **Discussions**: [GitHub Discussions](https://github.com/Luracast/Restler/discussions)
* **Security**: See [SECURITY.md](SECURITY.md)

---

## License

Restler is open-source software licensed under the [MIT license](LICENSE).

---

## Credits

Created and maintained by [Luracast](https://luracast.com)

Special thanks to all [contributors](https://github.com/Luracast/Restler/graphs/contributors)!

---

**Happy RESTling!** 🎉
