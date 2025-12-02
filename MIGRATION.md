# Migration Guide: Restler v5 to v6

This guide helps you migrate your Restler v5 application to v6.

---

## Overview

Restler v6 is a major upgrade with significant improvements:
* PHP 8.0+ required
* Modern async server support (ReactPHP, Swoole, Workerman)
* Enhanced security
* Better performance
* Improved type safety

---

## Breaking Changes

### 1. PHP Version Requirement

**v5**: PHP 7.4+
**v6**: PHP 8.0+

**Action Required:**
* Upgrade your PHP version to 8.0 or higher
* Update server configuration

### 2. Composer Package Updates

**Update composer.json:**

```json
{
  "require": {
    "luracast/restler": "^6.0",
    "php": "^8.0"
  }
}
```

Then run:
```bash
composer update
```

### 3. Type Declarations

v6 enforces stricter type safety.

**v5 Code:**
```php
class UserAPI
{
    public function getUser($id)
    {
        return ['id' => $id];
    }
}
```

**v6 Code:**
```php
class UserAPI
{
    public function getUser(int $id): array
    {
        return ['id' => $id];
    }
}
```

**Action Required:**
* Add parameter type hints
* Add return type declarations
* Ensure types match method signatures

### 4. Session Handling

Session serialization changed from `serialize/unserialize` to `json_encode/json_decode`.

**Impact:** Existing sessions will need to be regenerated.

**Action Required:**
* Plan for session regeneration on deployment
* Or implement a migration script:

```php
// Migration script (one-time)
session_start();
if (isset($_SESSION['old_data'])) {
    // Re-save session data to trigger new JSON encoding
    $_SESSION['old_data'] = $_SESSION['old_data'];
}
```

### 5. Reflection API Changes

Internal reflection code updated for PHP 8 compatibility.

**Impact:** Should be transparent unless you extended internal classes.

**Action Required:**
* Test thoroughly if you extended `Container`, `Routes`, or related classes

---

## New Features to Adopt

### 1. PHP 8 Attributes (Optional)

v6 supports modern PHP 8 attributes alongside traditional PHPDoc annotations.

**Traditional (still supported):**
```php
/**
 * @url GET users/{id}
 * @access protected
 */
public function getUser(int $id): array { }
```

**New Attribute Style (optional):**
```php
#[Get('users/{id}')]
#[Protected]
public function getUser(int $id): array { }
```

### 2. Async Server Support

Choose your server runtime:

**Traditional PHP-FPM:**
```php
// public/index.php
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;

Routes::mapApiClasses([UserAPI::class]);
(new Restler)->handle();
```

**ReactPHP (Async):**
```bash
php public/index_react.php
```

**Swoole (High Performance):**
```bash
php public/index_swoole.php
```

**Workerman (Event-Driven):**
```bash
php public/index_workerman.php start
```

### 3. Enhanced CORS Support

More granular CORS configuration:

```php
use Luracast\Restler\Defaults;

Defaults::$accessControlAllowOrigin = '*';
Defaults::$accessControlAllowCredentials = true;
Defaults::$accessControlMaxAge = 3600; // Now properly configurable
Defaults::$accessControlExposeHeaders = 'X-Custom-Header';
```

### 4. Improved Error Handling

Better error messages with more context:

```php
try {
    $r->handle();
} catch (\Throwable $e) {
    // v6 provides better stack traces and error details
    error_log($e->getMessage());
}
```

---

## Step-by-Step Migration

### Step 1: Update Dependencies

```bash
# Update composer.json
composer require luracast/restler:^6.0

# Install dependencies
composer update
```

### Step 2: Fix Type Declarations

Run through your API classes and add type hints:

```bash
# Use PHP_CodeSniffer or similar to find missing types
vendor/bin/phpcs --standard=PSR12 src/
```

**Common patterns:**

```php
// Before
public function create($data) { }
public function list($page = 1) { }
public function delete($id) { }

// After
public function create(array $data): array { }
public function list(int $page = 1): array { }
public function delete(int $id): bool { }
```

### Step 3: Update PHPDoc Comments

Ensure PHPDoc comments are accurate:

```php
/**
 * Create a new user
 *
 * @param array $data User data
 * @return array Created user
 *
 * @url POST users
 * @access protected
 */
public function create(array $data): array
{
    // Implementation
}
```

### Step 4: Test Thoroughly

```bash
# Run your test suite
vendor/bin/phpunit

# Or use Behat if you have BDD tests
vendor/bin/behat
```

### Step 5: Update Error Handling

v6 has improved error handling. Update catch blocks:

```php
// Before (v5)
catch (RestException $e) {
    // Handle
}

// After (v6)
catch (HttpException $e) {
    // More specific exception types
}
```

### Step 6: Production Deployment

1. **Test in staging** environment first
2. **Clear cache** directory: `rm -rf cache/*`
3. **Regenerate routes** in production mode
4. **Monitor** for any runtime errors
5. **Plan for session migration** (users may need to re-login)

---

## Common Issues and Solutions

### Issue: "Class not found" errors

**Cause:** Autoloader cache not updated

**Solution:**
```bash
composer dump-autoload
```

### Issue: Type errors in API methods

**Cause:** Missing type declarations

**Solution:** Add proper type hints to all parameters and return types

### Issue: Sessions not working

**Cause:** Session format changed from serialize to JSON

**Solution:** Clear session storage or implement migration

### Issue: CORS not working

**Cause:** CORS configuration changed

**Solution:** Update Defaults configuration:
```php
Defaults::$accessControlAllowOrigin = 'your-domain.com';
Defaults::$accessControlAllowCredentials = true;
```

### Issue: Routes not found after deployment

**Cause:** Production mode cache not cleared

**Solution:**
```bash
rm cache/routes.php
```

---

## Performance Tuning for v6

### 1. Use Production Mode

```php
$r = new Restler(true); // Enable production mode
```

### 2. Use OPcache

```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

### 3. Consider Async Servers

For high-traffic APIs, consider Swoole or ReactPHP:

```bash
# Swoole can handle 10x more requests
php public/index_swoole.php
```

### 4. Enable Response Compression

```php
Defaults::$compressionEnabled = true;
```

---

## Compatibility Matrix

| Feature | v5 | v6 |
|---------|----|----|
| PHP 7.4 | ✅ | ❌ |
| PHP 8.0 | ✅ | ✅ |
| PHP 8.1 | ✅ | ✅ |
| PHP 8.2 | ⚠️ | ✅ |
| PHP 8.3 | ❌ | ✅ |
| Attributes | ❌ | ✅ |
| ReactPHP | ⚠️ | ✅ |
| Swoole | ⚠️ | ✅ |
| Workerman | ❌ | ✅ |
| AWS Lambda | ❌ | ✅ |
| GraphQL | ❌ | ✅ |
| Excel Export | ❌ | ✅ |

---

## Need Help?

* **Issues**: [GitHub Issues](https://github.com/Luracast/Restler/issues)
* **Discussions**: [GitHub Discussions](https://github.com/Luracast/Restler/discussions)
* **Documentation**: See other .md files in this repository

---

## Rollback Plan

If you need to rollback to v5:

```bash
# In composer.json
{
  "require": {
    "luracast/restler": "^5.0"
  }
}

composer update
```

Clear cache and restart servers.

---

**Note:** We recommend thorough testing in a staging environment before migrating production systems.
