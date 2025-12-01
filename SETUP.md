# Installation & Setup Guide

This guide explains how to install Restler v6 and set up your environment, including required PHP extensions and optional async server support.

## PHP Version Requirements

Restler v6 requires **PHP 8.0+**. The current recommended version is **PHP 8.3** or **PHP 8.4**.

### Checking Your PHP Version

```bash
php --version
```

## PHP Extensions

### Required Extensions for Development

The following extensions are required for development and testing:

#### 1. **ext-json** (Production + Development)
- **Purpose**: JSON encoding/decoding - core REST API functionality
- **Installation**: Usually built-in, check with `php -m | grep json`
- **Status**: ✅ Built-in, enabled by default

#### 2. **ext-posix** (Development only)
- **Purpose**: POSIX system calls needed by Behat testing framework
- **Installation**: Usually available in standard PHP builds
- **Check**: `php -m | grep posix`
- **Status**: ✅ Built-in, enabled by default

#### 3. **ext-pdo** (Development only)
- **Purpose**: Database abstraction layer for OAuth2 server examples and testing
- **Variants**: 
  - `ext-pdo_sqlite`: For in-memory database testing
  - `ext-pdo_mysql`: For MySQL examples
  - `ext-pdo_pgsql`: For PostgreSQL examples
- **Check**: `php -m | grep pdo`
- **Status**: ✅ Built-in, enabled by default (at least pdo_sqlite)

#### 4. **ext-intl** (Development only - Optional)
- **Purpose**: Internationalization functions
- **Why included**: Some development packages (like `avadim/fast-excel-writer`) may require it
- **Note**: Polyfills (`symfony/polyfill-intl-*`) provide most functionality if unavailable
- **Installation**: Via Homebrew (macOS) or package manager
- **Check**: `php -m | grep intl`
- **Status**: ⚠️ Optional - can be skipped if not needed for your workflow

### Checking Enabled Extensions

List all enabled extensions:
```bash
php -m
```

Check PHP configuration files:
```bash
php --ini
```

## Optional Extensions for Enhanced Features

### High-Performance Async Server Support

Restler supports alternative async servers. Install only ONE of these:

```json
{
  "ext-swoole": "For high-performance async server support",
  "ext-openswoole": "Alternative to ext-swoole (don't install both)"
}
```

### SQLite for Examples

For OAuth2 server examples and session storage:
```bash
# Check if available
php -m | grep pdo_sqlite
```

## Installation Instructions by OS

### macOS (using Homebrew)

1. **Check installed PHP and extensions:**
   ```bash
   php --version
   php -m
   ```

2. **If using Shivammathur's PHP tap:**
   ```bash
   brew tap shivammathur/php
   brew install shivammathur/php/php@8.4
   ```

3. **Enable extensions in php.ini:**
   ```bash
   # Find your PHP configuration file
   php --ini
   
   # Edit the config file (example for PHP 8.4)
   # Uncomment these lines if needed:
   # extension=posix
   # extension=pdo
   # extension=intl (optional)
   ```

4. **Verify extensions:**
   ```bash
   php -m | grep -E 'json|posix|pdo'
   ```

### Linux (Debian/Ubuntu)

```bash
# Install PHP and extensions
sudo apt-get install php php-json php-posix php-pdo php-sqlite3

# Or for a specific version (e.g., PHP 8.4)
sudo apt-get install php8.4 php8.4-json php8.4-posix php8.4-pdo php8.4-sqlite3
```

### Linux (RHEL/CentOS)

```bash
# Install PHP and extensions
sudo dnf install php php-pecl-json php-posix php-pdo php-sqlite

# Or using yum
sudo yum install php php-posix php-pdo php-sqlite
```

## Composer Dependencies

Install all development dependencies:

```bash
composer install
```

Or if you need to skip platform requirements (e.g., during development):
```bash
composer install --ignore-platform-req=ext-intl --ignore-platform-req=php
```

## Running Tests

### Prerequisites

1. Ensure all dependencies are installed:
   ```bash
   composer install
   ```

2. Start the PHP development server in a separate terminal:
   ```bash
   composer serve
   ```
   
   Or manually:
   ```bash
   php -S 0.0.0.0:8080 -t public server.php
   ```

   The server should be running at: `http://localhost:8080`

3. Verify the server is running:
   ```bash
   curl http://localhost:8080/examples/_001_helloworld/say/hello
   ```

### Running Full Test Suite

In another terminal, with the server running:

```bash
composer test
```

This runs Behat tests with the default profile and stops on first failure.

### Running Specific Tests

By tag:
```bash
./behat --tags=@example1
```

By feature file:
```bash
./behat features/examples/_001_helloworld.feature
```

By scenario name:
```bash
./behat --name="Saying Hello world"
```

## Troubleshooting

### "Unable to load dynamic library 'intl'"

The `ext-intl` extension is listed in require-dev but is optional. To skip it:
- Comment out the line `extension=intl` in `php.ini`, OR
- Use: `composer install --ignore-platform-req=ext-intl`

The project provides polyfills for most intl functionality via Symfony packages.

### "Command 'composer' not found"

Install Composer globally:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Server Not Responding

Ensure the PHP server is running and port 8080 is available:
```bash
# Check if port is in use
lsof -i :8080

# Check server logs
tail -f /tmp/restler-server.log
```

### Tests Skip All Scenarios

Make sure the server is running before running tests:
```bash
composer serve  # In one terminal
composer test   # In another terminal
```

## Development Workflow

1. Start the server:
   ```bash
   composer serve
   ```

2. In another terminal, run tests:
   ```bash
   composer test
   ```

3. Make code changes and run tests again

4. Optional: Use `--profile=fpm` for FPM-based testing:
   ```bash
   ./behat --profile=fpm
   ```

## Performance Note on PHP 8.5

PHP 8.5 may show deprecation warnings for nullable parameters. These are cosmetic warnings from dependencies and do not affect functionality. They will be resolved as dependencies are updated to be compatible with PHP 8.5's stricter type handling.
