# Behat Update Guide

## Current Status

- **Current Version**: Behat 3.26.0 (latest 3.x stable, released 2025-10-29)
- **PHP Support**: Officially requires `php >=8.1 <8.5`
- **Project PHP Version**: 8.5.0 (requires `--ignore-platform-req=php` flag)

## Compatibility Update

The `composer.json` has been updated to:
- Remove the unused `ext-intl` requirement (optional, with polyfill fallback)
- Update Behat constraint from `~3` to `^3.13` (allows minor version updates)

### Changes Made

**File: `composer.json`**

```diff
"require-dev": {
  "ext-pdo": "*",
-  "ext-intl": "*",
  "ext-posix": "*",
-  "behat/behat": "~3",
+  "behat/behat": "^3.13",
```

## Update Steps

To apply the latest Behat updates, run:

```bash
composer update behat/behat --ignore-platform-req=php
```

Or to completely update all dependencies to their latest versions:

```bash
composer update --ignore-platform-req=php
```

## Test the Update

After updating, run the test suite:

```bash
# In one terminal
composer serve

# In another terminal
composer test
```

## Known Issues with PHP 8.5

Behat 3.26.0 officially requires `php >=8.1 <8.5`, but works with PHP 8.5 when ignoring platform requirements. You may see deprecation warnings about nullable parameters in dependencies - these are cosmetic and do not affect functionality.

### Deprecation Warnings Expected

These warnings are safe to ignore:
- `Implicitly marking parameter $X as nullable is deprecated` - from React, Symfony, and other dependencies
- `Case statements followed by a semicolon (;) are deprecated` - from React Promise library

These will be resolved as dependencies are updated for PHP 8.5 compatibility.

## Future Considerations

### Behat 4.x Compatibility

When Behat 4.x becomes available with PHP 8.5+ support, the following changes may be needed:

1. Update `composer.json`:
   ```json
   "behat/behat": "^4"
   ```

2. Review Behat 4.x migration guide for breaking changes

3. Test all feature files to ensure compatibility

4. Potential Context class updates if Behat's hook system changes

### PHP 8.5 Native Support

Once Behat officially supports PHP 8.5:
- Remove `--ignore-platform-req=php` flag from composer commands
- Suppress/resolve deprecation warnings in dependencies
- Update to versions with proper PHP 8.5 type hints
