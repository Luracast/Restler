#!/bin/bash
# Restler v6 Test Runner Script
# Run this script in a terminal while the server is running in another terminal

cd /Users/arul/Projects/restler-v6

echo "================================================"
echo "Restler v6 Test Suite"
echo "================================================"
echo ""
echo "Server should be running at: http://localhost:8080"
echo "Verifying connectivity..."
echo ""

# Try to connect to server
if ! curl -s http://localhost:8080/examples/_001_helloworld/say/hello > /dev/null 2>&1; then
    echo "❌ ERROR: Cannot connect to server at localhost:8080"
    echo ""
    echo "Make sure the server is running:"
    echo "  composer serve"
    echo ""
    exit 1
fi

echo "✅ Server is responding!"
echo ""
echo "Running test suite..."
echo "================================================"
echo ""

# Run the full test suite
composer test

# Capture exit code
TEST_EXIT_CODE=$?

echo ""
echo "================================================"

if [ $TEST_EXIT_CODE -eq 0 ]; then
    echo "✅ ALL TESTS PASSED!"
else
    echo "❌ Some tests failed (exit code: $TEST_EXIT_CODE)"
fi

echo "================================================"

exit $TEST_EXIT_CODE
