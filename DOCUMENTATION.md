# Restler v6 - Documentation Index

## 📚 Project Documentation

This document provides an overview of all documentation files for Restler v6 development.

### Getting Started

**Start here if you're new to the project:**

1. **[SETUP.md](SETUP.md)** - Development Environment Setup
   - PHP version requirements
   - Detailed explanation of each PHP extension and why it's needed
   - Installation instructions for macOS, Linux (Debian/Ubuntu/RHEL)
   - Running tests with the dev server
   - Troubleshooting common issues

2. **[DEVELOPER_CHECKLIST.md](DEVELOPER_CHECKLIST.md)** - Quick Reference
   - Step-by-step setup checklist
   - Common commands
   - File structure overview
   - Troubleshooting quick fixes

### Recent Updates (This Session)

**Understanding the changes made:**

3. **[UPDATE_SUMMARY.md](UPDATE_SUMMARY.md)** - Summary of All Changes
   - Overview of completed tasks
   - List of created/modified files
   - Key points about setup improvements
   - Testing notes

4. **[PHP85_COMPATIBILITY.md](PHP85_COMPATIBILITY.md)** - PHP 8.5 Compatibility
   - Explains the deprecation warnings fixed
   - Details all 7 nullable parameter type hint updates
   - Shows before/after code for each change
   - Backward compatibility notes

5. **[BEHAT_UPDATE.md](BEHAT_UPDATE.md)** - Behat Version & Configuration
   - Current Behat version info (v3.26.0)
   - PHP 8.5 compatibility status
   - Update instructions
   - Plans for future Behat 4.x migration

### Original Project Documentation

**Reference materials:**

- **[README.md](README.md)** - Project overview and features
- **[SECURITY.md](SECURITY.md)** - Security considerations
- **[STAGES.md](STAGES.md)** - Development stages and milestones
- **[MIGRATION.md](MIGRATION.md)** - Version migration guide
- **[CHANGELOG.md](CHANGELOG.md)** - Version history
- **[ANNOTATIONS.md](ANNOTATIONS.md)** - Documentation annotations
- **[PARAM.md](PARAM.md)** - Parameter documentation
- **[FORMS.md](FORMS.md)** - Forms handling
- **[COMPOSE.md](COMPOSE.md)** - Composition patterns

## 🔄 Quick Decision Tree

**I want to...**

### ...set up the project
→ Start with [SETUP.md](SETUP.md)

### ...understand recent changes
→ Read [UPDATE_SUMMARY.md](UPDATE_SUMMARY.md)

### ...fix PHP 8.5 deprecation warnings
→ See [PHP85_COMPATIBILITY.md](PHP85_COMPATIBILITY.md)

### ...update Behat
→ Check [BEHAT_UPDATE.md](BEHAT_UPDATE.md)

### ...quick command reference
→ Use [DEVELOPER_CHECKLIST.md](DEVELOPER_CHECKLIST.md)

### ...learn about Restler features
→ Read [README.md](README.md)

### ...check security guidelines
→ See [SECURITY.md](SECURITY.md)

## 📋 File Changes in This Update

### New Files Created
```
SETUP.md                          (260 lines) - Environment setup guide
BEHAT_UPDATE.md                   (100 lines) - Behat configuration notes
PHP85_COMPATIBILITY.md            (200 lines) - PHP 8.5 compatibility details
UPDATE_SUMMARY.md                 (150 lines) - Summary of changes
DEVELOPER_CHECKLIST.md            (200 lines) - Quick reference checklist
```

### Files Modified
```
composer.json                      - Removed ext-intl, updated Behat
src/Luracast/helpers.php          - Fixed nullable parameters (1 function)
src/Luracast/Core.php             - Fixed nullable parameters (3 methods)
src/Luracast/Data/Route.php       - Fixed nullable parameters (1 method)
src/Luracast/UI/Forms.php         - Fixed nullable parameters (2 methods)
```

## ✨ Key Improvements

### Documentation
- ✅ Clear explanation of why each PHP extension is needed
- ✅ OS-specific installation instructions
- ✅ Troubleshooting guide for common issues
- ✅ Quick reference checklist for developers
- ✅ Detailed PHP 8.5 compatibility documentation

### Code Quality
- ✅ All PHP 8.5 deprecation warnings resolved in core code
- ✅ Explicit nullable type hints for better IDE support
- ✅ No breaking changes - fully backward compatible

### Configuration
- ✅ Removed non-essential ext-intl requirement
- ✅ Updated Behat version constraint for flexibility
- ✅ Clear handling of platform requirements

## 🚀 Next Steps

1. **First Time**:
   - Read [SETUP.md](SETUP.md)
   - Follow [DEVELOPER_CHECKLIST.md](DEVELOPER_CHECKLIST.md)
   - Run the development server and tests

2. **Regular Development**:
   - Keep server running with: `composer serve`
   - Run tests with: `composer test`
   - Check changes against [UPDATE_SUMMARY.md](UPDATE_SUMMARY.md)

3. **For Reference**:
   - Use [DEVELOPER_CHECKLIST.md](DEVELOPER_CHECKLIST.md) for common commands
   - Refer to [PHP85_COMPATIBILITY.md](PHP85_COMPATIBILITY.md) for type hints
   - Check [BEHAT_UPDATE.md](BEHAT_UPDATE.md) for testing

## 📞 Support

All documentation is self-contained in markdown files within the project:
- Each guide is complete and standalone
- Cross-references link related documents
- Troubleshooting sections in each guide

For issues or questions:
1. Check the relevant documentation file
2. See the troubleshooting section
3. Review the code comments
4. Check project history in CHANGELOG.md

---

**Last Updated**: 2025-12-01
**PHP Version**: 8.5.0+
**Behat Version**: 3.26.0
**Documentation Status**: Complete ✅
