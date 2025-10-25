@echo off
REM PDGP Server Startup Script for Windows

echo ===================================
echo   PDGP Server Startup
echo ===================================
echo.

REM Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo Error: Docker is not running. Please start Docker Desktop first.
    exit /b 1
)

REM Change to server directory
cd server

REM Check if .env exists
if not exist .env (
    echo Warning: .env file not found. Creating from example...
    if exist .env.example (
        copy .env.example .env
        echo [OK] Created .env file. Please review and update the values.
        echo   Especially: JWT_SECRET and database passwords
        echo.
    ) else (
        echo Error: .env.example not found. Please create .env manually.
        exit /b 1
    )
)

REM Start Docker containers
echo Starting Docker containers...
docker-compose up -d

echo.
echo Waiting for services to initialize...
timeout /t 5 /nobreak >nul

REM Install PHP dependencies
echo.
echo Installing PHP dependencies...
docker-compose exec -T php composer install --no-interaction

echo.
echo ===================================
echo   Server Started Successfully!
echo ===================================
echo.
echo Access the application at:
echo   - Web Console:  http://localhost:8080
echo   - phpMyAdmin:   http://localhost:8081
echo   - API:          http://localhost:8080/api
echo.
echo Default credentials:
echo   Username: admin
echo   Password: changeme
echo.
echo View logs with: docker-compose logs -f
echo Stop with: docker-compose down
echo.

cd ..

