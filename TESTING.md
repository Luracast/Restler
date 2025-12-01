# Running Tests Properly

## Correct Test Execution Procedure

### Prerequisites
1. Ensure dependencies are installed:
   ```bash
   composer install --ignore-platform-req=php
   ```

### Step 1: Start the Development Server (Terminal 1)

**IMPORTANT**: The server MUST be running in a separate terminal/process before running tests.

```bash
cd /Users/arul/Projects/restler-v6

# Start the PHP built-in development server
php -S 0.0.0.0:8080 -t public server.php
```

You should see output like:
```
[Mon Dec  1 12:47:30 2025] PHP 8.5.0 Development Server (http://0.0.0.0:8080) started
```

**Verify the server is running:**
```bash
# In another terminal, test an endpoint
curl http://localhost:8080/examples/_001_helloworld/say/hello
# Expected response: "Hello world!"
```

### Step 2: Run Tests (Terminal 2)

**WHILE the server is still running** in Terminal 1, open a new terminal and run:

```bash
cd /Users/arul/Projects/restler-v6

# Run the full test suite
composer test
```

Or run specific tests:
```bash
# Run a specific feature
./behat features/examples/_001_helloworld.feature

# Run tests with a specific tag
./behat --tags=@example1

# Run tests by scenario name
./behat --name="Saying Hello world"
```

## Expected Test Results

### Current Status
When running `composer test`, you should see:
- Scenario: "Saying Hello world" - **PASSING** ✅
- Scenario: "Saying Hello Restler" - **CURRENTLY FAILING** (known issue)
- Overall: Multiple scenarios with skipped tests due to missing optional features

### Test Output Format
```
@example1 @helloworld
Feature: Testing Helloworld Example

  Scenario: Saying Hello world
    When I request "examples/_001_helloworld/say/hello"
    Then the response status code should be 200
    And the response is JSON
    And the type is "string"
    And the value equals "Hello world!"
    
[PASS/FAIL status for each scenario]

X scenarios (Y passed, Z failed)
A steps (B passed, C failed, D skipped)
```

## Troubleshooting Failed Tests

### Issue: "Connection refused" or "Failed to connect"
**Cause**: Server is not running or port 8080 is not accessible

**Solution**:
1. Verify server is running in Terminal 1
2. Check port availability: `lsof -i :8080`
3. Kill conflicting processes: `killall php`
4. Restart server: `php -S 0.0.0.0:8080 -t public server.php`

### Issue: "All scenarios are skipped"
**Cause**: Server was not running when tests started

**Solution**:
1. Stop tests (Ctrl+C in Terminal 2)
2. Ensure server is running in Terminal 1
3. Restart tests in Terminal 2

### Issue: Tests run but fail with no clear error
**Cause**: Server may have crashed or become unresponsive

**Solution**:
1. Check server logs in Terminal 1
2. Look for PHP errors or exceptions
3. Restart server: Kill Terminal 1, start fresh
4. Re-run tests

## Advanced Test Options

### Verbose Output
```bash
./behat -v features/examples/_001_helloworld.feature
```

### Stop on First Failure
```bash
./behat --stop-on-failure features/examples/_001_helloworld.feature
```

### Format Options
```bash
# Pretty format (default)
./behat --format=pretty

# Progress format
./behat --format=progress

# JUnit format (for CI/CD)
./behat --format=junit --out=/tmp/junit.xml
```

### Filter Tests
```bash
# By tag
./behat --tags=@example1

# By scenario name
./behat --name="Saying"

# Exclude tags
./behat --tags="~@skip"
```

## Server Lifecycle Management

### Starting the Server
```bash
# Foreground (see all output)
php -S 0.0.0.0:8080 -t public server.php

# Background with output redirect
php -S 0.0.0.0:8080 -t public server.php > /tmp/server.log 2>&1 &

# Using composer command
composer serve
```

### Stopping the Server
```bash
# Kill all PHP processes
killall php

# Kill specific port
lsof -i :8080 | grep LISTEN | awk '{print $2}' | xargs kill -9
```

### Monitoring Server Output
```bash
# While server is running, in another terminal:
tail -f /tmp/server.log
```

## Continuous Testing Workflow

### Development Loop
1. **Terminal 1 - Start Server** (keep running):
   ```bash
   php -S 0.0.0.0:8080 -t public server.php
   ```

2. **Terminal 2 - Run Tests** (as needed):
   ```bash
   # Run specific test while developing
   ./behat --name="Saying Hello world"
   
   # Run full suite when ready to commit
   composer test
   ```

3. **Terminal 3 - Code Editing** (optional):
   ```bash
   # Edit files as needed
   # Tests in Terminal 2 will pick up changes immediately
   ```

## Known Issues & Workarounds

### "Saying Hello Restler" Test Fails
**Status**: Known issue, pre-existing (not related to recent changes)
**Symptom**: Test with URI template expansion fails silently
**Workaround**: Other tests pass fine; this specific test needs investigation

## CI/CD Integration

For automated testing environments:

```bash
#!/bin/bash
# Start server in background
php -S 0.0.0.0:8080 -t public server.php > /tmp/server.log 2>&1 &
SERVER_PID=$!

# Wait for server to start
sleep 3

# Run tests
composer test
TEST_RESULT=$?

# Cleanup
kill $SERVER_PID

exit $TEST_RESULT
```

## Summary

**Remember**: 
- ✅ Server runs in one process/terminal
- ✅ Tests run as a separate client in another process/terminal
- ✅ Server must be ready BEFORE tests start
- ✅ Keep server running while testing
- ✅ Monitor both terminals for issues
