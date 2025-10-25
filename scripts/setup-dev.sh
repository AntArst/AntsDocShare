#!/bin/bash

# Development Environment Setup Script
# Sets up the development environment from scratch
# Usage: ./scripts/setup-dev.sh

set -e  # Exit on error

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo "========================================"
echo "  Development Environment Setup"
echo "========================================"
echo ""

# Function to print step
step() {
    echo -e "${BLUE}▶${NC} $1"
}

# Function to print success
success() {
    echo -e "${GREEN}✓${NC} $1"
}

# Function to print error
error() {
    echo -e "${RED}✗${NC} $1"
}

# Check prerequisites
echo "Checking prerequisites..."
echo ""

# Check Docker
step "Checking Docker..."
if command -v docker &> /dev/null; then
    DOCKER_VERSION=$(docker --version)
    success "Docker found: $DOCKER_VERSION"
else
    error "Docker not found. Please install Docker first."
    echo "  Visit: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check Docker Compose
step "Checking Docker Compose..."
if command -v docker-compose &> /dev/null; then
    COMPOSE_VERSION=$(docker-compose --version)
    success "Docker Compose found: $COMPOSE_VERSION"
else
    error "Docker Compose not found. Please install Docker Compose first."
    echo "  Visit: https://docs.docker.com/compose/install/"
    exit 1
fi

# Check PHP (optional for local development)
step "Checking PHP..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1)
    success "PHP found: $PHP_VERSION"
else
    echo -e "${YELLOW}!${NC} PHP not found locally (will use Docker)"
fi
echo ""

# Create .env file if it doesn't exist
step "Setting up environment file..."
cd "$PROJECT_ROOT"

if [ ! -f .env ]; then
    cat > .env << EOF
# Database Configuration
DB_HOST=mysql
DB_PORT=3306
DB_NAME=pdgp_db
DB_USER=pdgp_user
DB_PASSWORD=dev_password_123
DB_ROOT_PASSWORD=root_password_123

# JWT Configuration
JWT_SECRET=$(openssl rand -base64 32 2>/dev/null || echo "dev-secret-key-please-change-in-production-min-32-chars")

# Application Configuration
APP_ENV=development
APP_DEBUG=true
EOF
    success "Created .env file with default development values"
    echo -e "${YELLOW}!${NC} Please review and update .env if needed"
else
    success ".env file already exists"
fi
echo ""

# Create storage directories
step "Creating storage directories..."
mkdir -p server/storage/assets
mkdir -p server/storage/packages
mkdir -p server/storage/temp
chmod -R 775 server/storage 2>/dev/null || true
success "Storage directories created"
echo ""

# Stop any existing containers
step "Stopping existing containers..."
docker-compose down -v &> /dev/null || true
success "Stopped any existing containers"
echo ""

# Build and start containers
step "Building Docker containers..."
docker-compose build
success "Docker containers built"
echo ""

step "Starting Docker containers..."
docker-compose up -d
success "Docker containers started"
echo ""

# Wait for MySQL to be ready
step "Waiting for MySQL to be ready..."
MAX_TRIES=30
COUNT=0
until docker-compose exec -T mysql mysqladmin ping -h localhost -u root -p"$DB_ROOT_PASSWORD" --silent &> /dev/null; do
    COUNT=$((COUNT + 1))
    if [ $COUNT -gt $MAX_TRIES ]; then
        error "MySQL failed to start after ${MAX_TRIES} attempts"
        docker-compose logs mysql
        exit 1
    fi
    echo "  Waiting... ($COUNT/$MAX_TRIES)"
    sleep 2
done
success "MySQL is ready"
echo ""

# Install Composer dependencies
step "Installing Composer dependencies..."
docker-compose exec -T php composer install
success "Composer dependencies installed"
echo ""

# Verify database setup
step "Verifying database setup..."
if docker-compose exec -T mysql mysql -u root -p"root_password_123" pdgp_db -e "SHOW TABLES;" &> /dev/null; then
    TABLE_COUNT=$(docker-compose exec -T mysql mysql -u root -p"root_password_123" pdgp_db -e "SHOW TABLES;" | wc -l)
    success "Database initialized with $TABLE_COUNT tables"
else
    error "Database initialization failed"
    exit 1
fi
echo ""

# Show container status
step "Checking container status..."
docker-compose ps
echo ""

# Test endpoints
step "Testing application..."
sleep 2

if curl -s -f http://localhost:8080 > /dev/null; then
    success "Application is responding"
else
    echo -e "${YELLOW}!${NC} Application may not be responding yet (this is sometimes normal)"
fi
echo ""

# Summary
echo "========================================"
echo "  Setup Complete!"
echo "========================================"
echo ""
echo "Your development environment is ready!"
echo ""
echo "Access points:"
echo "  • Application:  http://localhost:8080"
echo "  • phpMyAdmin:   http://localhost:8081"
echo ""
echo "Default credentials:"
echo "  • Username: admin"
echo "  • Password: changeme"
echo ""
echo "Useful commands:"
echo "  • View logs:         docker-compose logs -f"
echo "  • Stop containers:   docker-compose down"
echo "  • Restart:           docker-compose restart"
echo "  • Run tests:         ./scripts/test-api.sh"
echo "  • Pre-commit check:  ./scripts/pre-commit-check.sh"
echo ""
echo -e "${GREEN}Happy coding!${NC}"

