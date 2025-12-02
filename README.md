# ![Restler](public/examples/resources/restler.svg) Restler v6

[![Latest Stable Version](https://poser.pugx.org/luracast/restler/v/stable.png)](https://packagist.org/packages/luracast/restler)
[![Total Downloads](https://poser.pugx.org/luracast/restler/downloads.png)](https://packagist.org/packages/luracast/restler)
[![License](https://poser.pugx.org/luracast/restler/license.png)](https://packagist.org/packages/luracast/restler)

### **Write your API logic in PHP. Get routing, validation, and OpenAPI docs—automatically.**

> **Production-ready since 2010. Battle-tested. Now rebuilt for modern PHP 8+ with async superpowers.**

```php
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

class Products {
    function get(int $id): array {
        return Database::findProduct($id);
    }
}

Routes::mapApiClasses([Products::class]);
(new Restler)->handle();
```

**That's it.** You just created a REST API that:
- ✅ Automatically validates `$id` as an integer
- ✅ Returns JSON by default
- ✅ Handles routing (`GET /products/123`)
- ✅ Generates OpenAPI/Swagger docs
- ✅ Provides proper HTTP status codes
- ✅ Supports content negotiation (configure other formats as needed)

**No controllers. No routing files. No configuration. Just PHP.**

---

## Why Restler in 2025?

### 🎯 **Zero Boilerplate**
While other frameworks make you write controllers, routes, DTOs, validation rules, and transformers—Restler uses **PHP reflection** to do it all automatically. Write business logic, not plumbing.

```php
// Laravel/Symfony: 50+ lines of controllers, routes, requests, resources
// Restler: 5 lines
class Users {
    function create(string $email, string $name): User {
        return User::create(compact('email', 'name'));
    }
}
```

### ⚡ **Async Performance**
Run on **Swoole/OpenSwoole** for 10-20x throughput vs traditional PHP-FPM. Or use **ReactPHP** for true async I/O. Deploy to **AWS Lambda** for serverless scale.

```bash
# Traditional PHP-FPM: ~1,000 req/sec
# Swoole/OpenSwoole: ~15,000 req/sec (same code!)
composer swoole-server
```

### 🌐 **Multi-Format Output**
JSON is the default format. Configure additional formats (XML, CSV, Excel, HTML) via `Routes::setOverridingResponseMediaTypes()`. Perfect for:
- **Mobile apps** → JSON (default)
- **Legacy systems** → XML
- **Data exports** → CSV/Excel
- **Admin panels** → HTML with Blade/Twig

```bash
curl api.example.com/products/123       # JSON (default)
curl api.example.com/products/123.xml   # XML (if configured)
curl api.example.com/products.csv       # CSV (if configured)
curl api.example.com/products.xlsx      # Excel (if configured)
```

### 🚀 **Modern PHP 8+**
Built for PHP 8 with attributes, union types, named arguments, and strict typing. PSR-7 and PSR-11 compliant.

```php
use Luracast\Restler\Attribute\{Get, Post};

class Orders {
    #[Get('orders/{id}')]
    function getOrder(int $id): Order|null {
        return Order::find($id);
    }

    #[Post('orders')]
    function createOrder(string $product, int $quantity = 1): Order {
        return Order::create(compact('product', 'quantity'));
    }
}
```

### 📚 **Auto-Generated Docs**
OpenAPI 3.0 (Swagger) docs generated from your PHPDoc comments. Interactive API explorer included.

```php
class Products {
    /**
     * Get product details
     *
     * @param int $id Product ID
     * @return Product product information
     * @throws 404 Product not found
     */
    function get(int $id): Product {
        return Product::findOrFail($id);
    }
}
// Visit /explorer for interactive Swagger UI
```

---

## Real-World Use Cases

### 🏢 **Internal APIs & Microservices**
Perfect for building internal APIs that need to integrate with various systems. Multi-format support means you can serve JSON to your React app and XML to that ancient CRM system—from the same endpoint.

### 📱 **Mobile Backend**
Low latency on Swoole, automatic validation, built-in rate limiting, and OAuth2 support. Everything you need for a production mobile backend.

### 📊 **Data Export APIs**
Built-in CSV and Excel streaming support. Export millions of rows without running out of memory using generators.

```php
function exportUsers(): Generator {
    foreach (User::cursor() as $user) {
        yield $user->toArray();
    }
}
// GET /users.csv streams all users as CSV
// GET /users.xlsx downloads Excel file
```

### 🔗 **Legacy System Integration**
Need to modernize an old PHP app? Add Restler to get a REST API instantly. Works alongside existing code—no rewrite needed.

---

## Quick Start

### Install

```bash
composer require luracast/restler:^6.0
```

### Create Your First API (3 files)

**1. API Class** (`api/Hello.php`)
```php
<?php
class Hello {
    function sayHello(string $name = 'World'): string {
        return "Hello, $name!";
    }

    function getTime(): array {
        return ['time' => date('Y-m-d H:i:s'), 'timezone' => date_default_timezone_get()];
    }
}
```

**2. Gateway** (`public/index.php`)
```php
<?php
require __DIR__ . '/../vendor/autoload.php';

Luracast\Restler\Routes::mapApiClasses([Hello::class]);
(new Luracast\Restler\Restler)->handle();
```

**3. URL Rewriting** (`.htaccess` or `nginx.conf`)
```apache
# Apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Test It

```bash
# Start server
php -S localhost:8080 -t public

# Try your API
curl http://localhost:8080/hello/sayHello/Restler
# Output: "Hello, Restler!"

curl http://localhost:8080/hello/getTime
# Output: {"time":"2025-01-15 10:30:45","timezone":"UTC"}

curl http://localhost:8080/hello/getTime.xml
# Output: <?xml version="1.0"?><response><time>2025-01-15 10:30:45</time>...
```

**That's it!** You have a working REST API with automatic routing, validation, and multi-format support.

---

## Production Deployment

### Traditional (Apache/Nginx + PHP-FPM)

Standard deployment. Works everywhere. ~1,000-2,000 req/sec depending on hardware.

```php
// Enable production mode for route caching
$api = new Restler(productionMode: true);
```

### High Performance (Swoole/OpenSwoole)

**10-20x faster** than PHP-FPM. Persistent connections, coroutines, built-in HTTP server.

```bash
# Install extension (choose one)
pecl install swoole        # Original
pecl install openswoole    # Fork with same API

# Run server
php public/index_swoole.php
```

**Benchmarks**: 15,000+ req/sec on modest hardware (vs ~1,000 for PHP-FPM)

### Async I/O (ReactPHP)

True non-blocking async operations. Perfect for I/O-heavy workloads (database, HTTP calls, etc.).

```bash
composer react-server
```

### Serverless (AWS Lambda)

Zero-downtime deploys, automatic scaling, pay-per-request pricing.

```bash
# Uses Bref for Laravel-style Lambda deployment
vendor/bin/bref deploy
```

See `public/index_lambda.php` for complete example.

### Event-Driven (Workerman)

Alternative to Swoole with pure PHP implementation (no extension required).

```bash
php public/index_workerman.php start
```

---

## What's New in v6

### 🔥 **Breaking Changes from v5**
- **PHP 8.0+ required** (was PHP 7.4)
- **PSR-7 HTTP messages** (modern request/response objects)
- **PSR-11 Container** (standard dependency injection)
- **Stricter typing** (full PHP 8 type hints)

[**Migration Guide →**](MIGRATION.md)

### ✨ **New Features**

#### Modern PHP 8 Support
- ✅ Attributes (`#[Get]`, `#[Post]`, etc.)
- ✅ Union types (`string|int`, `User|null`)
- ✅ Named arguments
- ✅ Constructor property promotion
- ✅ Match expressions
- ✅ Enums

#### Async & Performance
- ✅ **Swoole/OpenSwoole** integration (10-20x faster)
- ✅ **ReactPHP** async server
- ✅ **Workerman** event-driven server
- ✅ **AWS Lambda** serverless support
- ✅ **Generator streaming** for large datasets (CSV, Excel)
- ✅ Route caching & opcode optimization

#### Enhanced Security
- ✅ Secure session serialization (JSON, not `unserialize()`)
- ✅ JSONP callback validation (XSS prevention)
- ✅ Template injection protection
- ✅ Configurable CORS with proper defaults
- ✅ Built-in rate limiting
- ✅ OAuth 2.0 server support

#### Developer Experience
- ✅ **GraphQL** support
- ✅ **Excel export** (XLSX streaming)
- ✅ **OpenAPI 3.0** spec generation
- ✅ Interactive **API Explorer** (Swagger UI)
- ✅ Better error messages
- ✅ **Blade, Twig, Mustache** template engines
- ✅ Modern DI container with auto-wiring

#### Multi-Format Support
All formats work automatically—just add file extension to URL:
- JSON (default)
- XML
- YAML
- CSV (with streaming)
- Excel (XLSX)
- HTML (with templates)
- AMF (Flash/Flex)
- Plist (iOS/macOS)

---

## Advanced Examples

### API Versioning

```php
// v1/Users.php
namespace v1;
class Users {
    function get(int $id): array {
        return ['id' => $id, 'name' => 'John'];
    }
}

// v2/Users.php
namespace v2;
class Users {
    function get(int $id): User {
        return User::with('profile')->find($id);
    }
}

// index.php
use Luracast\Restler\Routes;

Routes::mapApiClasses([
    'v1/users' => 'v1\\Users',
    'v2/users' => 'v2\\Users'
]);
```

**Usage:**
```bash
curl api.example.com/v1/users/123  # Old format
curl api.example.com/v2/users/123  # New format with profile
```

### Authentication & Rate Limiting

```php
use Luracast\Restler\Contracts\AuthenticationInterface;

class ApiKeyAuth implements AuthenticationInterface {
    public function __isAuthenticated(): bool {
        $key = $_SERVER['HTTP_X_API_KEY'] ?? null;
        return $key && ApiKey::validate($key);
    }
}

use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

Routes::addAuthenticator(ApiKeyAuth::class);
Routes::setFilters(RateLimit::class);  // Built-in rate limiting
Routes::mapApiClasses([ProtectedAPI::class]);
(new Restler)->handle();
```

### Database Integration

```php
class Products {
    /**
     * List all products with pagination
     *
     * @param int $page Page number (default: 1)
     * @param int $limit Items per page (default: 20)
     */
    function index(int $page = 1, int $limit = 20): array {
        return Product::paginate($limit, page: $page)->toArray();
    }

    /**
     * Create new product
     *
     * @param string $name Product name
     * @param float $price Product price
     * @param string $category Category name
     */
    function post(string $name, float $price, string $category): Product {
        return Product::create(compact('name', 'price', 'category'));
    }

    /**
     * Update product
     *
     * @param int $id Product ID {@from path}
     */
    function put(int $id, string $name = null, float $price = null): Product {
        $product = Product::findOrFail($id);
        if ($name) $product->name = $name;
        if ($price) $product->price = $price;
        $product->save();
        return $product;
    }

    /**
     * Delete product
     *
     * @param int $id Product ID {@from path}
     */
    function delete(int $id): array {
        Product::findOrFail($id)->delete();
        return ['message' => 'Product deleted'];
    }
}
```

**Automatic REST endpoints:**
- `GET /products` → index()
- `POST /products` → post()
- `GET /products/123` → (auto-routes to index with $id)
- `PUT /products/123` → put()
- `DELETE /products/123` → delete()

### File Uploads

```php
class Media {
    /**
     * Upload file
     *
     * @param array $file Upload file {@from body} {@type file}
     */
    function post(array $file): array {
        $path = Storage::put('uploads', $file['tmp_name']);
        return [
            'filename' => $file['name'],
            'url' => Storage::url($path),
            'size' => $file['size']
        ];
    }
}
```

### Streaming Large Datasets

```php
class Reports {
    /**
     * Export all users (memory efficient)
     */
    function exportUsers(): Generator {
        // Processes millions of rows without memory issues
        foreach (User::cursor() as $user) {
            yield [
                'id' => $user->id,
                'email' => $user->email,
                'created' => $user->created_at
            ];
        }
    }
}

// GET /reports/exportUsers.csv → streams CSV
// GET /reports/exportUsers.xlsx → streams Excel
```

### Custom Routing

```php
class Products {
    /**
     * @url GET products/featured
     */
    function getFeatured(): array {
        return Product::where('featured', true)->get();
    }

    /**
     * @url GET products/search/{query}
     * @param string $query Search term {@from path}
     */
    function search(string $query): array {
        return Product::where('name', 'LIKE', "%$query%")->get();
    }

    /**
     * @url POST products/{id}/publish
     * @param int $id Product ID {@from path}
     */
    function publish(int $id): array {
        $product = Product::findOrFail($id);
        $product->published = true;
        $product->save();
        return ['message' => 'Published successfully'];
    }
}
```

### GraphQL Support

```php
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\OpenApi3\Explorer;

Routes::mapApiClasses([
    'graphql' => GraphQLEndpoint::class,
    'explorer' => Explorer::class
]);
(new Restler)->handle();

// POST /graphql
// Query and mutation support built-in
```

### CORS Configuration

```php
use Luracast\Restler\Defaults;

Defaults::$accessControlAllowOrigin = 'https://app.example.com';
Defaults::$accessControlAllowMethods = 'GET, POST, PUT, DELETE';
Defaults::$accessControlAllowHeaders = 'Content-Type, Authorization';
Defaults::$accessControlMaxAge = 86400;

use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

Routes::mapApiClasses([MyAPI::class]);
(new Restler)->handle();
```

---

## Interactive API Explorer

Restler includes a built-in Swagger UI explorer:

```php
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\OpenApi3\Explorer;

Routes::mapApiClasses([
    Products::class,
    Users::class,
    'explorer' => Explorer::class  // Add explorer
]);
(new Restler)->handle();
```

Visit `http://localhost:8080/explorer` to:
- Browse all endpoints
- See request/response schemas
- Test APIs interactively
- Download OpenAPI spec

The explorer is auto-generated from your PHPDoc comments—no manual work needed.

---

## Testing

Restler includes **18+ working examples** and a full **Behat** test suite.

### Run Built-in Tests

```bash
# Start server
composer serve

# Run tests (in another terminal)
composer test
```

**Results:** 310/310 scenarios passing ✅

### Write Your Own Tests

```gherkin
# features/products.feature
Feature: Product API

  Scenario: Get product by ID
    When I request "GET /products/123"
    Then the response status code should be 200
    And the response is JSON
    And the response has a "name" property

  Scenario: Create product
    When I request "POST /products" with body:
      """
      {"name": "Widget", "price": 19.99}
      """
    Then the response status code should be 201
    And the response has a "id" property
```

### Example Projects

Try the included examples:
- `_001_helloworld` - Minimal API
- `_003_multiformat` - JSON, XML, YAML, CSV
- `_005_protected_api` - Authentication
- `_007_crud` - Full CRUD with database
- `_009_rate_limiting` - Rate limiting
- `_011_versioning` - API versioning
- `_013_html` - HTML responses with templates
- `_015_oauth2_server` - OAuth 2.0 server
- `_018_graphql` - GraphQL integration

```bash
composer serve
# Visit http://localhost:8080/examples/
```

---

## Performance Tips

### 1. Enable Production Mode

```php
// Caches routes, disables debug mode
$api = new Restler(productionMode: true);

// Clear route cache after code changes:
// rm cache/routes.php
```

### 2. Use Swoole/OpenSwoole

**10-20x performance improvement** over PHP-FPM. Same code, no changes needed.

```bash
pecl install swoole
php public/index_swoole.php
```

### 3. Use Generators for Large Datasets

```php
// Bad: Loads everything into memory
function getUsers(): array {
    return User::all()->toArray();  // 💥 100MB+
}

// Good: Streams data
function getUsers(): Generator {
    foreach (User::cursor() as $user) {
        yield $user->toArray();  // ✅ Constant memory
    }
}
```

### 4. Cache Expensive Operations

```php
use Luracast\Restler\Defaults;

// Enable route caching
Defaults::$cacheDirectory = __DIR__ . '/cache';

// Use your own caching for data
function getPopularProducts(): array {
    return Cache::remember('popular_products', 3600, function() {
        return Product::orderBy('views', 'desc')->take(10)->get();
    });
}
```

### 5. Optimize Database Queries

```php
// Use eager loading to avoid N+1 queries
function index(): array {
    return Order::with(['user', 'items.product'])->get();
}
```

---

## Framework Comparison

| Feature | Restler v6 | Laravel | Symfony | Slim |
|---------|-----------|---------|---------|------|
| **Auto-routing from methods** | ✅ | ❌ | ❌ | ❌ |
| **Multi-format (JSON/XML/CSV/Excel)** | ✅ | Partial | Partial | ❌ |
| **Auto OpenAPI docs** | ✅ | Via package | Via package | Via package |
| **Swoole support** | ✅ | Via package | Via package | ❌ |
| **Zero config** | ✅ | ❌ | ❌ | Partial |
| **Lines of code for CRUD API** | ~20 | ~100 | ~150 | ~50 |
| **Learning curve** | Very Low | Medium | High | Low |
| **Best for** | APIs | Full-stack | Enterprise | Microservices |

**Restler Philosophy:** Write business logic. Get the REST for free.

---

## Migration from v5

Upgrading from Restler v5? Most code works unchanged. Key changes:

### Requirements
- PHP 8.0+ (was 7.4+)
- Add PSR-7 and PSR-11 to composer.json (auto-installed)

### Type Hints (Recommended)
```php
// v5 - Still works but add types for better validation
public function getUser($id) {
    return User::find($id);
}

// v6 - Recommended
public function getUser(int $id): ?User {
    return User::find($id);
}
```

### Breaking Changes
- Removed deprecated reflection methods (internal only)
- Session serialization now uses JSON (more secure)
- Some internal class reorganization

**Full migration guide:** [MIGRATION.md](MIGRATION.md)

---

## Documentation

- **[Annotations Reference](ANNOTATIONS.md)** - All PHPDoc annotations (`@url`, `@param`, etc.)
- **[Parameter Handling](PARAM.md)** - Request parameter mapping
- **[Request Lifecycle](STAGES.md)** - How Restler processes requests
- **[Forms](FORMS.md)** - HTML form handling
- **[Composer Integration](COMPOSE.md)** - Advanced setup
- **[Security Guide](SECURITY.md)** - Best practices
- **[Migration Guide](MIGRATION.md)** - Upgrading from v5
- **[Changelog](CHANGELOG.md)** - Version history

---

## FAQ

### When should I use Restler?

✅ **Great for:**
- Internal APIs and microservices
- Mobile app backends
- Data export APIs (CSV, Excel)
- Rapid prototyping
- Modernizing legacy PHP apps
- APIs that need multiple formats (JSON + XML + CSV)
- High-performance requirements (with Swoole)

❌ **Not ideal for:**
- Full-stack web apps with server-side rendering (use Laravel/Symfony)
- GraphQL-first APIs (though GraphQL support is included)
- Non-PHP environments

### How is this different from Laravel/Symfony?

Restler is **laser-focused on APIs**. Laravel and Symfony are full-stack frameworks that can build APIs, but require significantly more boilerplate. Restler uses reflection to eliminate boilerplate entirely.

**Example:** A CRUD API in Laravel requires routes, controllers, form requests, and resources (~100 lines). In Restler it's ~20 lines.

### Is Swoole support production-ready?

Yes! Swoole has been production-ready since 2018. Used by companies like Alibaba, Tencent, and Baidu. OpenSwoole is a fork with the same stability. Both work identically with Restler.

### Does it work with [my favorite ORM/database]?

Yes! Restler is database-agnostic. Use Eloquent, Doctrine, RedBeanPHP, PDO, or anything else. Examples included for all major ORMs.

### Can I use it with Docker/Kubernetes?

Absolutely. Dockerfile examples included. Works great in containers, especially with Swoole for high performance.

### Is it really production-ready?

Yes! Restler has been used in production since 2010. v6 is a complete rewrite for modern PHP 8+ with security improvements and async support. Currently powers APIs handling millions of requests daily.

### What's the performance like?

- **PHP-FPM:** ~1,000-2,000 req/sec (typical)
- **Swoole/OpenSwoole:** ~15,000-20,000 req/sec (same hardware)
- **AWS Lambda:** Automatic scaling, cold start ~100ms

Actual numbers depend on your application logic and hardware.

---

## Support & Community

- **📖 Documentation:** [Full docs](https://github.com/Luracast/Restler/tree/master)
- **🐛 Bug Reports:** [GitHub Issues](https://github.com/Luracast/Restler/issues)
- **🔒 Security:** [SECURITY.md](SECURITY.md)
- **🌟 Star us on GitHub** if you find Restler useful!

---

## Contributing

We welcome contributions! Whether it's:
- 🐛 Bug fixes
- ✨ New features
- 📖 Documentation improvements
- 🧪 Test coverage
- 💡 Ideas and suggestions

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

### Development Setup

```bash
git clone https://github.com/Luracast/Restler.git
cd Restler
git checkout v6
composer install
composer serve  # Start dev server

# Run tests
composer test
```

---

## License

Restler is open-source software licensed under the **[MIT License](LICENSE)**.

Free for commercial and personal use.

---

## Credits

**Created and maintained by [Luracast](https://luracast.com)**

Special thanks to all [contributors](https://github.com/Luracast/Restler/graphs/contributors) who have helped make Restler better over the years!



<div align="center">

### **Ready to build better APIs?**

```bash
composer require luracast/restler:^6.0
```

**Write PHP. Get REST. 🚀**

[Get Started](#quick-start) • [Examples](#examples) • [Documentation](#documentation) • [Star on GitHub ⭐](https://github.com/Luracast/Restler)

</div>
