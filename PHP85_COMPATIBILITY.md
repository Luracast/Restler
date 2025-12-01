# PHP 8.5 Compatibility Updates

## Overview

This document tracks the changes made to ensure full PHP 8.5 compatibility by adding proper nullable type hints to function parameters that have default null values.

## PHP 8.5 Deprecation Issue

PHP 8.5 deprecated implicit nullable parameters. When a parameter has a default value of `null` but isn't explicitly typed as nullable (using `?`), PHP 8.5 shows a deprecation warning:

```
Deprecated: Implicitly marking parameter $X as nullable is deprecated, 
the explicit nullable type must be used instead
```

## Changes Made

### 1. `src/Luracast/helpers.php` - Line 22

**Function**: `instance()`

**Before**:
```php
function instance(string $make = null, array $parameters = [], ServerRequestInterface $request = null)
```

**After**:
```php
function instance(?string $make = null, array $parameters = [], ?ServerRequestInterface $request = null)
```

**Why**: The `$make` and `$request` parameters have default `null` values and need explicit nullable type hints.

---

### 2. `src/Luracast/Core.php` - Line 93

**Method**: `Core::__construct()`

**Before**:
```php
public function __construct(ContainerInterface $container = null, &$config = [])
```

**After**:
```php
public function __construct(?ContainerInterface $container = null, &$config = [])
```

**Why**: The `$container` parameter has default `null` value and needs explicit nullable type hint.

---

### 3. `src/Luracast/Core.php` - Line 149

**Method**: `Core::handle()` (abstract)

**Before**:
```php
abstract public function handle(ServerRequestInterface $request = null): PromiseInterface;
```

**After**:
```php
abstract public function handle(?ServerRequestInterface $request = null): PromiseInterface;
```

**Why**: The `$request` parameter has default `null` value and needs explicit nullable type hint.

---

### 4. `src/Luracast/Core.php` - Line 306

**Method**: `Core::make()`

**Before**:
```php
public function make($className, Route $route = null, bool $recreate = false)
```

**After**:
```php
public function make($className, ?Route $route = null, bool $recreate = false)
```

**Why**: The `$route` parameter has default `null` value and needs explicit nullable type hint.

---

### 5. `src/Luracast/Data/Route.php` - Line 420

**Method**: `Route::call()`

**Before**:
```php
public function call(array $arguments, bool $authenticated = false, bool $validate = true, callable $maker = null)
```

**After**:
```php
public function call(array $arguments, bool $authenticated = false, bool $validate = true, ?callable $maker = null)
```

**Why**: The `$maker` parameter has default `null` value and needs explicit nullable type hint.

---

### 6. `src/Luracast/UI/Forms.php` - Line 117

**Method**: `Forms::get()`

**Before**:
```php
public function get(
    string $method = 'POST',
    string $action = null,
    bool $dataOnly = false,
    string $prefix = '',
    string $indent = '    '
)
```

**After**:
```php
public function get(
    string $method = 'POST',
    ?string $action = null,
    bool $dataOnly = false,
    string $prefix = '',
    string $indent = '    '
)
```

**Why**: The `$action` parameter has default `null` value and needs explicit nullable type hint.

---

### 7. `src/Luracast/UI/Forms.php` - Line 457

**Method**: `Forms::key()`

**Before**:
```php
public function key(string $method = 'POST', string $action = null): string
```

**After**:
```php
public function key(string $method = 'POST', ?string $action = null): string
```

**Why**: The `$action` parameter has default `null` value and needs explicit nullable type hint.

---

## Additional Change: `composer.json`

**Removed requirement**: `"ext-intl": "*"` from require-dev

**Reason**: The intl extension is optional - it's only used by development packages like `avadim/fast-excel-writer`. The project provides polyfills via Symfony packages (`symfony/polyfill-intl-*`), so the extension is not strictly required. Users can install it if needed, but it shouldn't be enforced.

**Updated constraint**: Changed Behat from `"behat/behat": "~3"` to `"behat/behat": "^3.13"` to allow minor version updates while remaining on the stable 3.x branch.

## Testing

After these changes, PHP 8.5 no longer shows deprecation warnings related to implicit nullable parameters in the Restler framework code.

Note: Warnings may still appear from third-party dependencies, which is expected as they update their PHP 8.5 support.

## Validation Steps

1. Run tests with PHP 8.5 to confirm no new errors are introduced
2. Verify all existing tests still pass
3. Check that the deprecation warnings are eliminated for Restler code

```bash
# Install dependencies
composer install --ignore-platform-req=php

# Run tests
composer test
```

## Backward Compatibility

These changes are fully backward compatible. Adding explicit nullable type hints does not change the behavior of the code - it only makes the implicit nullability explicit, which is required for PHP 8.5 compliance.
