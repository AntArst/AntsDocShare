#!/bin/bash

# API Testing Script
# Tests the main API endpoints
# Usage: ./scripts/test-api.sh [base-url]

BASE_URL="${1:-http://localhost:8080}"
TEMP_FILE=$(mktemp)

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo "========================================"
echo "  API Testing Script"
echo "========================================"
echo ""
echo "Base URL: $BASE_URL"
echo ""

# Track test results
PASSED=0
FAILED=0

# Function to test an endpoint
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local expected_code=$4
    local description=$5
    local auth_token=$6

    echo -e "${BLUE}Testing:${NC} $description"
    echo "  $method $endpoint"
    
    # Build curl command
    local curl_cmd="curl -s -w '\n%{http_code}' -X $method '$BASE_URL$endpoint'"
    
    if [ -n "$data" ]; then
        curl_cmd="$curl_cmd -H 'Content-Type: application/json' -d '$data'"
    fi
    
    if [ -n "$auth_token" ]; then
        curl_cmd="$curl_cmd -H 'Authorization: Bearer $auth_token'"
    fi
    
    # Execute request
    RESPONSE=$(eval $curl_cmd)
    HTTP_CODE=$(echo "$RESPONSE" | tail -n 1)
    BODY=$(echo "$RESPONSE" | sed '$d')
    
    # Check result
    if [ "$HTTP_CODE" -eq "$expected_code" ]; then
        echo -e "  ${GREEN}✓ PASSED${NC} (HTTP $HTTP_CODE)"
        ((PASSED++))
        
        # Pretty print JSON response if available
        if command -v jq &> /dev/null; then
            echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
        else
            echo "$BODY"
        fi
    else
        echo -e "  ${RED}✗ FAILED${NC} (Expected $expected_code, got $HTTP_CODE)"
        ((FAILED++))
        echo "  Response: $BODY"
    fi
    echo ""
    
    # Return the response body for further use
    echo "$BODY" > "$TEMP_FILE"
}

# Get auth token for use in subsequent requests
get_auth_token() {
    echo -e "${YELLOW}Authenticating...${NC}"
    
    RESPONSE=$(curl -s -X POST "$BASE_URL/api/auth/login" \
        -H "Content-Type: application/json" \
        -d '{"username":"admin","password":"changeme"}')
    
    if command -v jq &> /dev/null; then
        TOKEN=$(echo "$RESPONSE" | jq -r '.data.token' 2>/dev/null)
    else
        # Fallback if jq is not available
        TOKEN=$(echo "$RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
    fi
    
    if [ -n "$TOKEN" ] && [ "$TOKEN" != "null" ]; then
        echo -e "${GREEN}✓ Authentication successful${NC}"
        echo ""
    else
        echo -e "${RED}✗ Authentication failed${NC}"
        echo "Response: $RESPONSE"
        echo ""
        TOKEN=""
    fi
}

echo "Starting API tests..."
echo ""

# Test 1: Health check / Root endpoint
test_endpoint "GET" "/" "" 200 "Root endpoint"

# Test 2: Login endpoint
test_endpoint "POST" "/api/auth/login" '{"username":"admin","password":"changeme"}' 200 "Login with valid credentials"

# Get auth token
get_auth_token

if [ -n "$TOKEN" ]; then
    # Test 3: Get sites (requires auth)
    test_endpoint "GET" "/api/sites" "" 200 "Get sites list (authenticated)" "$TOKEN"
    
    # Test 4: Get CSV template
    test_endpoint "GET" "/api/template/csv" "" 200 "Download CSV template" "$TOKEN"
    
    # Test 5: Unauthorized request
    test_endpoint "GET" "/api/sites" "" 401 "Get sites without authentication"
else
    echo -e "${YELLOW}Skipping authenticated tests (no auth token)${NC}"
    echo ""
fi

# Test 6: Invalid login
test_endpoint "POST" "/api/auth/login" '{"username":"invalid","password":"wrong"}' 401 "Login with invalid credentials"

# Test 7: Malformed JSON
test_endpoint "POST" "/api/auth/login" '{invalid json}' 400 "Malformed JSON request"

# Clean up
rm -f "$TEMP_FILE"

# Summary
echo "========================================"
echo "  Test Summary"
echo "========================================"
echo ""
echo -e "Passed: ${GREEN}$PASSED${NC}"
echo -e "Failed: ${RED}$FAILED${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed.${NC}"
    exit 1
fi

