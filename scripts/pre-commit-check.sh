#!/bin/bash

# Pre-commit Check Script
# Runs basic validation checks before committing code
# Usage: ./scripts/pre-commit-check.sh

set -e  # Exit on error

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
SERVER_DIR="$PROJECT_ROOT/server"

echo "========================================"
echo "  Pre-Commit Checks"
echo "========================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Track overall status
CHECKS_PASSED=0
CHECKS_FAILED=0

# Function to print success message
success() {
    echo -e "${GREEN}✓${NC} $1"
    ((CHECKS_PASSED++))
}

# Function to print error message
error() {
    echo -e "${RED}✗${NC} $1"
    ((CHECKS_FAILED++))
}

# Function to print warning message
warning() {
    echo -e "${YELLOW}!${NC} $1"
}

echo "1. Checking PHP syntax..."
echo "----------------------------------------"
if cd "$SERVER_DIR" && find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \; > /dev/null 2>&1; then
    success "PHP syntax check passed"
else
    error "PHP syntax errors found"
    echo "   Run: cd server && find . -name \"*.php\" -not -path \"./vendor/*\" -exec php -l {} \\;"
fi
echo ""

echo "2. Validating composer.json..."
echo "----------------------------------------"
if cd "$SERVER_DIR" && composer validate --strict --no-check-publish > /dev/null 2>&1; then
    success "Composer validation passed"
else
    error "Composer validation failed"
    echo "   Run: cd server && composer validate --strict"
fi
echo ""

echo "3. Checking for debugging code..."
echo "----------------------------------------"
FOUND_DEBUG=0
if grep -r "var_dump\|print_r\|dd(" --include="*.php" "$SERVER_DIR/src/" > /dev/null 2>&1; then
    warning "Found debugging statements (var_dump, print_r, dd)"
    echo "   Run: grep -rn \"var_dump\\|print_r\\|dd(\" --include=\"*.php\" server/src/"
    FOUND_DEBUG=1
else
    success "No debugging statements found"
fi
echo ""

echo "4. Checking for .env file..."
echo "----------------------------------------"
if [ -f "$PROJECT_ROOT/.env" ]; then
    success ".env file exists"
    # Check if .env is in .gitignore
    if git check-ignore "$PROJECT_ROOT/.env" > /dev/null 2>&1; then
        success ".env is properly ignored by git"
    else
        error ".env is NOT in .gitignore!"
        echo "   Add '.env' to your .gitignore file"
    fi
else
    warning ".env file not found (may be expected for CI)"
fi
echo ""

echo "5. Checking SQL files..."
echo "----------------------------------------"
SQL_VALID=1
for sql_file in "$SERVER_DIR/database"/*.sql; do
    if [ -f "$sql_file" ]; then
        # Basic SQL syntax check (just verify file is readable and not empty)
        if [ -s "$sql_file" ]; then
            success "$(basename "$sql_file") is valid"
        else
            error "$(basename "$sql_file") is empty"
            SQL_VALID=0
        fi
    fi
done
echo ""

echo "6. Checking file permissions..."
echo "----------------------------------------"
# Check for executable PHP files (should not be executable)
FOUND_EXEC=0
while IFS= read -r -d '' file; do
    if [ -x "$file" ]; then
        warning "PHP file is executable: $file"
        FOUND_EXEC=1
    fi
done < <(find "$SERVER_DIR/src" -name "*.php" -print0 2>/dev/null)

if [ $FOUND_EXEC -eq 0 ]; then
    success "File permissions are correct"
fi
echo ""

echo "7. Checking for large files..."
echo "----------------------------------------"
LARGE_FILES=$(find "$PROJECT_ROOT" -type f -size +5M -not -path "*/.git/*" -not -path "*/vendor/*" -not -path "*/node_modules/*" 2>/dev/null)
if [ -z "$LARGE_FILES" ]; then
    success "No large files (>5MB) found"
else
    warning "Found large files (>5MB):"
    echo "$LARGE_FILES"
fi
echo ""

echo "8. Checking Docker configuration..."
echo "----------------------------------------"
if command -v docker-compose &> /dev/null; then
    if cd "$PROJECT_ROOT" && docker-compose config > /dev/null 2>&1; then
        success "Docker Compose configuration is valid"
    else
        error "Docker Compose configuration has errors"
        echo "   Run: docker-compose config"
    fi
else
    warning "docker-compose not found (skipping check)"
fi
echo ""

echo "9. Checking for TODO/FIXME comments..."
echo "----------------------------------------"
TODO_COUNT=$(grep -r "TODO\|FIXME" --include="*.php" "$SERVER_DIR/src/" 2>/dev/null | wc -l || echo "0")
if [ "$TODO_COUNT" -gt 0 ]; then
    warning "Found $TODO_COUNT TODO/FIXME comments"
    echo "   Run: grep -rn \"TODO\\|FIXME\" --include=\"*.php\" server/src/"
else
    success "No TODO/FIXME comments found"
fi
echo ""

echo "10. Checking git status..."
echo "----------------------------------------"
if git diff --cached --quiet; then
    warning "No files staged for commit"
else
    STAGED_FILES=$(git diff --cached --name-only | wc -l)
    success "$STAGED_FILES files staged for commit"
fi
echo ""

# Summary
echo "========================================"
echo "  Summary"
echo "========================================"
echo ""
echo -e "Checks passed: ${GREEN}$CHECKS_PASSED${NC}"
echo -e "Checks failed: ${RED}$CHECKS_FAILED${NC}"
echo ""

if [ $CHECKS_FAILED -gt 0 ]; then
    echo -e "${RED}Some checks failed. Please fix the issues before committing.${NC}"
    exit 1
else
    echo -e "${GREEN}All critical checks passed! Ready to commit.${NC}"
    if [ $FOUND_DEBUG -eq 1 ]; then
        echo -e "${YELLOW}Note: Debugging statements found. Consider removing them.${NC}"
    fi
    exit 0
fi

