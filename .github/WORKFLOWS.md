# GitHub Actions Workflows

This document describes the CI/CD workflows configured for the AntsDocShare project.

## Workflows Overview

### 1. CI Pipeline (`ci.yml`)

**Triggers:**
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop` branches

**Jobs:**

#### PHP Lint
- Validates `composer.json` syntax and structure
- Checks all PHP files for syntax errors
- Runs: PHP 8.2 with required extensions (PDO, MySQL, JSON, GD)

#### Composer Dependencies
- Installs and validates Composer dependencies
- Uses caching to speed up subsequent runs
- Verifies autoload configuration

#### Database Validation
- Spins up MySQL 8.0 service
- Runs schema migration (`schema.sql`)
- Runs seed data (`seed.sql`)
- Validates all table structures (users, sites, products, uploads, generated_packages)

#### Integration Tests
- Full environment setup with PHP and MySQL
- Installs dependencies and sets up database
- Creates required storage directories
- Tests application bootstrap

#### Docker Build
- Builds PHP-FPM Docker image
- Tests Docker Compose configuration
- Starts all services (nginx, php, mysql, phpmyadmin)
- Verifies service health and connectivity
- Checks database initialization

#### Security Check
- Audits Composer dependencies for vulnerabilities
- Checks file permissions
- Scans for sensitive data patterns

### 2. Docker Image Publish (`docker-publish.yml`)

**Triggers:**
- Git tags matching `v*.*.*` (e.g., v1.0.0)
- Manual workflow dispatch

**Actions:**
- Builds Docker image for production
- Publishes to GitHub Container Registry (ghcr.io)
- Tags with version numbers and `latest`
- Uses build cache for efficiency

### 3. Code Quality (`code-quality.yml`)

**Triggers:**
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop` branches

**Jobs:**

#### PHP Code Style Check
- Runs PHP_CodeSniffer with PSR-12 standard
- Non-blocking (continues on errors)

#### File Structure Validation
- Verifies required files exist:
  - `docker-compose.yml`
  - `server/composer.json`
  - `server/Dockerfile`
  - `server/database/schema.sql`
  - `server/src/` directory
  - `server/public/` directory

#### Documentation Check
- Verifies README files exist
- Runs Markdown linting (non-blocking)
- Lists all documentation files

#### SQL Syntax Check
- Validates SQL files for basic syntax issues

#### Dependency Review
- Reviews dependencies in pull requests
- Fails on moderate or higher severity vulnerabilities

#### Repository Size Check
- Identifies files larger than 5MB
- Reports storage directory size

## Required Secrets

No secrets are required for the CI workflows. For Docker publishing, the following is automatically available:
- `GITHUB_TOKEN` - Automatically provided by GitHub Actions

## Environment Variables

The workflows use the following test environment variables:

```env
DB_HOST=127.0.0.1 (or mysql for Docker)
DB_PORT=3306
DB_NAME=pdgp_test
DB_USER=pdgp_user (or root for some tests)
DB_PASSWORD=pdgp_password (or rootpassword for root)
DB_ROOT_PASSWORD=rootpassword
JWT_SECRET=test-secret-key-for-ci-testing
```

## Local Testing

To run similar tests locally:

### PHP Syntax Check
```bash
cd server
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;
```

### Composer Validation
```bash
cd server
composer validate --strict
composer install
```

### Database Setup
```bash
# Using local MySQL
mysql -u root -p -e "CREATE DATABASE pdgp_test;"
mysql -u root -p pdgp_test < server/database/schema.sql
mysql -u root -p pdgp_test < server/database/seed.sql
```

### Docker Build Test
```bash
# Create .env file first
docker-compose config
docker-compose up -d
docker-compose ps
docker-compose down -v
```

### Code Style Check
```bash
cd server
# Install PHP_CodeSniffer
composer require --dev "squizlabs/php_codesniffer=*"
./vendor/bin/phpcs --standard=PSR12 src/
```

## Workflow Status Badges

Add these badges to your README.md:

```markdown
![CI Pipeline](https://github.com/YOUR_USERNAME/AntsDocShare/actions/workflows/ci.yml/badge.svg)
![Code Quality](https://github.com/YOUR_USERNAME/AntsDocShare/actions/workflows/code-quality.yml/badge.svg)
```

## Best Practices

1. **Before Committing:**
   - Run PHP syntax checks locally
   - Validate composer.json
   - Test Docker build locally

2. **Pull Requests:**
   - Ensure all CI checks pass
   - Review code quality warnings
   - Update documentation if needed

3. **Releases:**
   - Tag releases with semantic versioning (v1.0.0)
   - Let Docker publish workflow build production images
   - Update CHANGELOG

## Troubleshooting

### CI Failures

**PHP Syntax Errors:**
- Check the file mentioned in the error
- Verify PHP 8.0+ compatibility

**Database Connection:**
- Ensure schema.sql has no syntax errors
- Check for missing tables or foreign key issues

**Docker Build Failures:**
- Verify Dockerfile syntax
- Check for missing dependencies in composer.json
- Ensure all required extensions are installed

**Composer Issues:**
- Run `composer validate` locally
- Check for version conflicts
- Clear composer cache: `composer clear-cache`

### Performance Optimization

The workflows use caching for:
- Composer packages (speeds up dependency installation)
- Docker build layers (reduces build time)
- GitHub Actions cache (general performance)

## Future Enhancements

Potential improvements to consider:

1. **Unit Tests:**
   - Add PHPUnit for automated testing
   - Include test coverage reporting

2. **End-to-End Tests:**
   - Add Selenium or similar for UI testing
   - Test file upload and download workflows

3. **Performance Testing:**
   - Add load testing with Apache Bench or similar
   - Monitor response times

4. **Static Analysis:**
   - Add PHPStan or Psalm for deeper code analysis
   - Include complexity metrics

5. **Deployment:**
   - Add deployment workflows for staging/production
   - Include rollback mechanisms

## Contact

For questions or issues with the workflows, please open an issue in the repository.

