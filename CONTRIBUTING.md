# Contributing to AntsDocShare

Thank you for your interest in contributing to the Product Display Generator Project (PDGP)! This document provides guidelines for contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing Requirements](#testing-requirements)
- [Pull Request Process](#pull-request-process)
- [CI/CD Pipeline](#cicd-pipeline)

## Code of Conduct

- Be respectful and inclusive
- Focus on constructive feedback
- Help maintain a welcoming environment
- Report unacceptable behavior by opening an issue

## Getting Started

### Prerequisites

- **PHP 8.0+** with extensions: PDO, MySQL, JSON, GD
- **Docker** and Docker Compose
- **Composer** for PHP dependencies
- **Git** for version control
- **MySQL 8.0** (via Docker or local installation)

### Development Setup

1. **Fork and Clone**
   ```bash
   git clone https://github.com/YOUR_USERNAME/AntsDocShare.git
   cd AntsDocShare
   ```

2. **Create Environment File**
   ```bash
   cp .env.example .env
   # Edit .env with your local configuration
   ```

3. **Start Development Environment**
   ```bash
   docker-compose up -d
   docker-compose exec php composer install
   ```

4. **Verify Installation**
   ```bash
   # Access the application
   open http://localhost:8080
   
   # Check logs
   docker-compose logs -f
   ```

## Development Workflow

### Branching Strategy

- `main` - Production-ready code
- `develop` - Integration branch for features
- `feature/feature-name` - New features
- `bugfix/bug-name` - Bug fixes
- `hotfix/issue-name` - Critical production fixes

### Working on a Feature

1. **Create a branch from develop**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**
   - Write code following our coding standards
   - Add tests if applicable
   - Update documentation

3. **Test locally**
   ```bash
   # Run PHP syntax check
   cd server
   find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;
   
   # Validate composer
   composer validate --strict
   
   # Test Docker build
   docker-compose down -v
   docker-compose up -d
   ```

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat: add your feature description"
   ```

5. **Push and create Pull Request**
   ```bash
   git push origin feature/your-feature-name
   ```

### Commit Message Guidelines

Follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks
- `ci`: CI/CD changes

**Examples:**
```bash
feat(api): add product search endpoint
fix(auth): resolve JWT token expiration issue
docs(readme): update installation instructions
ci(workflow): add code coverage reporting
```

## Coding Standards

### PHP Code Standards

We follow **PSR-12** coding standards for PHP.

#### Key Points:
- Use 4 spaces for indentation (no tabs)
- Opening braces on same line for classes and functions
- Type declarations for parameters and return types
- Proper DocBlocks for classes and methods

#### Example:
```php
<?php

namespace App\Controllers;

class ExampleController
{
    /**
     * Get a resource by ID
     * 
     * @param int $id Resource identifier
     * @return array Resource data
     */
    public function getById(int $id): array
    {
        // Implementation
        return ['id' => $id];
    }
}
```

### File Organization

```
server/
├── src/
│   ├── Api/          # API routing
│   ├── Auth/         # Authentication logic
│   ├── Config/       # Configuration classes
│   ├── Controllers/  # Request handlers
│   ├── Models/       # Data models
│   └── Services/     # Business logic
├── public/           # Web-accessible files
├── views/            # HTML templates
├── database/         # SQL scripts
└── storage/          # File storage
```

### Database Conventions

- Use `snake_case` for table and column names
- Include `created_at` and `updated_at` timestamps
- Use foreign key constraints
- Add appropriate indexes
- Write migrations in `database/schema.sql`

## Testing Requirements

### Before Submitting PR

Run these tests locally:

```bash
# 1. PHP Syntax Check
cd server
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

# 2. Composer Validation
composer validate --strict

# 3. Database Schema Test
mysql -u root -p -e "CREATE DATABASE test_pdgp;"
mysql -u root -p test_pdgp < database/schema.sql
mysql -u root -p test_pdgp < database/seed.sql

# 4. Docker Build Test
docker-compose config
docker-compose up -d
docker-compose ps
docker-compose down -v

# 5. Code Style (Optional but recommended)
composer require --dev squizlabs/php_codesniffer
./vendor/bin/phpcs --standard=PSR12 src/
```

### Writing Tests

While we don't have a full test suite yet, consider:

- Testing API endpoints manually with curl or Postman
- Verifying database operations
- Checking edge cases and error handling
- Testing file upload functionality

## Pull Request Process

### Before Submitting

- [ ] Code follows PSR-12 standards
- [ ] All PHP files pass syntax check
- [ ] Composer dependencies are valid
- [ ] Database migrations work correctly
- [ ] Docker containers build and run
- [ ] Documentation is updated
- [ ] Commit messages follow conventions

### PR Checklist

1. **Title**: Use conventional commit format
   ```
   feat(scope): add new feature
   ```

2. **Description**: Explain your changes
   - What problem does this solve?
   - How does it work?
   - Any breaking changes?
   - Screenshots (if UI changes)

3. **Link Issues**: Reference related issues
   ```
   Closes #123
   Related to #456
   ```

4. **Review**: Request review from maintainers

### PR Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
How has this been tested?

## Checklist
- [ ] My code follows PSR-12 standards
- [ ] I have updated the documentation
- [ ] I have tested the changes locally
- [ ] All CI checks pass

## Screenshots (if applicable)
Add screenshots here
```

## CI/CD Pipeline

All pull requests automatically run through our CI/CD pipeline:

### Automated Checks

1. **PHP Lint** - Syntax validation
2. **Composer Dependencies** - Dependency validation
3. **Database Validation** - Schema and seed testing
4. **Integration Tests** - Full stack testing
5. **Docker Build** - Container build testing
6. **Security Check** - Vulnerability scanning
7. **Code Quality** - PSR-12 compliance
8. **Documentation** - Markdown linting

### Workflow Status

Monitor your PR for workflow results:
- ✅ All checks pass - Ready for review
- ❌ Some checks fail - Address the issues
- 🟡 Checks pending - Wait for completion

See [.github/WORKFLOWS.md](.github/WORKFLOWS.md) for detailed CI/CD documentation.

### Common CI Failures

**PHP Syntax Error:**
```bash
# Run locally to find errors
find server/ -name "*.php" -not -path "*/vendor/*" -exec php -l {} \;
```

**Composer Validation Failed:**
```bash
# Fix composer.json issues
cd server
composer validate --strict
composer update
```

**Docker Build Failed:**
```bash
# Test Docker build locally
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

**Database Migration Failed:**
```bash
# Test SQL scripts
mysql -u root -p test_db < server/database/schema.sql
```

## Development Tips

### Debugging

```bash
# View container logs
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f mysql

# Access PHP container
docker-compose exec php bash

# Access MySQL
docker-compose exec mysql mysql -u root -p
```

### Database Access

```bash
# Via command line
docker-compose exec mysql mysql -u pdgp_user -p pdgp_db

# Via phpMyAdmin
open http://localhost:8081
```

### Code Quality Tools

```bash
# Install PHP_CodeSniffer
composer require --dev squizlabs/php_codesniffer

# Check code style
./vendor/bin/phpcs --standard=PSR12 src/

# Auto-fix issues
./vendor/bin/phpcbf --standard=PSR12 src/
```

## Getting Help

- **Documentation**: Check [README.md](README.md) and [.github/WORKFLOWS.md](.github/WORKFLOWS.md)
- **Issues**: Search existing issues or create a new one
- **Discussions**: Use GitHub Discussions for questions
- **Code Review**: Ask for clarification in PR comments

## Recognition

Contributors will be recognized in:
- GitHub contributors list
- Release notes (for significant contributions)
- Documentation (for major features)

Thank you for contributing to AntsDocShare! 🎉

