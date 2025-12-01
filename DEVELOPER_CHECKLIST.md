# Development Setup Checklist

## ✅ Complete Setup Instructions

### 1. PHP Environment (One-time setup)

- [ ] Verify PHP version: `php --version` (requires 8.0+, recommended 8.3 or 8.4)
- [ ] Check PHP extensions: `php -m`
- [ ] Ensure ext-json, ext-posix, ext-pdo are present
- [ ] (Optional) Install ext-intl if needed for Excel export features

**See**: `SETUP.md` for detailed installation instructions by OS

### 2. Project Dependencies

- [ ] Install Composer dependencies:
  ```bash
  composer install --ignore-platform-req=php
  ```
  
  Or if you don't have intl extension:
  ```bash
  composer install --ignore-platform-req=php --ignore-platform-req=ext-intl
  ```

- [ ] Verify installation: `composer show | wc -l` should show 47+ packages

### 3. Start Development Server

**In Terminal 1:**
```bash
# Start the PHP development server
composer serve

# OR manually:
php -S 0.0.0.0:8080 -t public server.php
```

The server should respond at: `http://localhost:8080/examples/_001_helloworld/say/hello`

### 4. Run Tests

**In Terminal 2:**
```bash
# Run full test suite
composer test

# OR run specific tests
./behat features/examples/_001_helloworld.feature
./behat --tags=@example1
```

## 📋 Quick Reference

### Common Commands

```bash
# Install/update dependencies
composer install

# Run all tests
composer test

# Start development server
composer serve

# Interactive PHP shell
php psysh

# Code analysis
./phpstan

# Auto-format code
./ecs fix

# Check for rector opportunities
./rector process --dry-run
```

### File Structure
```
restler-v6/
├── api/                  # Example APIs
├── features/            # Behat test features
├── src/                 # Framework source code
├── tests/               # PHP unit tests
├── public/              # Web root
├── composer.json        # Dependencies (now optimized)
├── SETUP.md            # NEW: Environment setup guide
├── PHP85_COMPATIBILITY.md # NEW: PHP 8.5 compatibility details
├── BEHAT_UPDATE.md      # NEW: Behat configuration notes
└── UPDATE_SUMMARY.md    # NEW: Summary of all changes
```

## 🐛 Troubleshooting

### Issue: "Unable to load dynamic library 'intl'"
**Solution**: The ext-intl extension is optional. Disable it:
```bash
# Edit php.ini and comment out: extension=intl
# OR use:
composer install --ignore-platform-req=ext-intl --ignore-platform-req=php
```

### Issue: "Cannot connect to localhost:8080"
**Solution**: Make sure the PHP server is running:
```bash
# Check if port is in use
lsof -i :8080

# Kill any existing server
killall php

# Start fresh
composer serve
```

### Issue: Tests skip all scenarios
**Solution**: The Behat tests require the PHP development server to be running:
```bash
# Terminal 1:
composer serve

# Terminal 2:
composer test
```

### Issue: Deprecation warnings about nullable parameters
**Solution**: These are expected warnings from PHP 8.5 about dependencies. They don't affect functionality and will disappear as dependencies update. Core Restler code has been updated to be compliant.

## 📚 Documentation

| Document | Purpose |
|----------|---------|
| `SETUP.md` | Complete environment setup with extension explanations |
| `BEHAT_UPDATE.md` | Behat version info and PHP 8.5 compatibility notes |
| `PHP85_COMPATIBILITY.md` | Detailed list of nullable parameter type hint fixes |
| `UPDATE_SUMMARY.md` | Summary of all changes made in this update |
| `README.md` | Original project README |
| `SECURITY.md` | Security considerations |
| `STAGES.md` | Development stages and milestones |

## 🔍 What Was Changed

### PHP 8.5 Compliance
- Added explicit nullable type hints to 7 methods/functions
- Removed non-essential ext-intl requirement
- Updated Behat version constraints

### Documentation
- Created comprehensive setup guide (SETUP.md)
- Added PHP 8.5 compatibility notes (PHP85_COMPATIBILITY.md)
- Added Behat update information (BEHAT_UPDATE.md)
- Created this checklist (UPDATE_SUMMARY.md)

### No Breaking Changes
All changes are backward compatible and focus on PHP 8.5 compliance.

## 🚀 Next Steps

1. **First-time setup**: Follow steps 1-4 above
2. **Regular development**: 
   - Keep server running in Terminal 1
   - Run tests in Terminal 2 as needed
   - Make code changes and verify with tests
3. **Before committing**:
   - Run full test suite: `composer test`
   - Check code quality: `./phpstan` and `./ecs`
   - Verify no new warnings are introduced

## 📞 Support

For issues with the development environment, refer to:
- `SETUP.md` - Setup and installation issues
- `PHP85_COMPATIBILITY.md` - PHP 8.5 related issues
- `BEHAT_UPDATE.md` - Testing framework issues
- Project documentation files (README.md, SECURITY.md, etc.)
