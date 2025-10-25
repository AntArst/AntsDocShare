#!/bin/bash

# PDGP Server Startup Script

set -e

echo "==================================="
echo "  PDGP Server Startup"
echo "==================================="
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Error: Docker is not running. Please start Docker first."
    exit 1
fi

# Change to server directory
cd server

# Check if .env exists
if [ ! -f .env ]; then
    echo "Warning: .env file not found. Creating from example..."
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✓ Created .env file. Please review and update the values."
        echo "  Especially: JWT_SECRET and database passwords"
        echo ""
    else
        echo "Error: .env.example not found. Please create .env manually."
        exit 1
    fi
fi

# Start Docker containers
echo "Starting Docker containers..."
docker-compose up -d

echo ""
echo "Waiting for services to initialize..."
sleep 5

# Install PHP dependencies
echo ""
echo "Installing PHP dependencies..."
docker-compose exec -T php composer install --no-interaction

echo ""
echo "==================================="
echo "  Server Started Successfully!"
echo "==================================="
echo ""
echo "Access the application at:"
echo "  - Web Console:  http://localhost:8080"
echo "  - phpMyAdmin:   http://localhost:8081"
echo "  - API:          http://localhost:8080/api"
echo ""
echo "Default credentials:"
echo "  Username: admin"
echo "  Password: changeme"
echo ""
echo "View logs with: docker-compose logs -f"
echo "Stop with: docker-compose down"
echo ""

