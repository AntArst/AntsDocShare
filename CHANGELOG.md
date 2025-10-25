# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- GitHub Actions CI/CD workflows for automated testing
- Comprehensive code quality checks (PSR-12, PHP syntax, SQL validation)
- Docker build and integration testing
- Security vulnerability scanning
- Database schema validation in CI
- Dependabot for automated dependency updates
- PR and issue templates for better project management
- Contributing guidelines
- Detailed workflow documentation
- .gitkeep files for storage directories

### Changed
- Enhanced .gitignore with testing and CI/CD patterns
- Updated README with CI/CD badges and testing documentation
- Improved project structure documentation

## [0.1.0] - 2025-10-25

### Added
- Initial server infrastructure with PHP 8.2
- JWT-based authentication system
- Multi-tenant site management
- RESTful API for product operations
- Web management console
- MySQL 8.0 database with complete schema
- Docker containerization (nginx, php-fpm, mysql, phpmyadmin)
- Asset management service with image handling
- CSV template generation
- File upload and processing
- Product display generation framework

### Database
- Users table with role-based access
- Sites table for multi-tenant support
- Products table with JSON asset storage
- Uploads tracking table
- Generated packages table

### API Endpoints
- `POST /api/auth/login` - User authentication
- `GET /api/sites` - List user sites
- `POST /api/sites` - Create new site
- `GET /api/sites/{id}` - Get site details
- `POST /api/upload` - Upload product data
- `GET /api/template/csv` - Download CSV template
- `GET /api/packages/{id}` - Download generated package

### Documentation
- Project README with architecture overview
- Server-specific documentation
- Quick start guide
- Environment setup instructions
- API documentation
- Project structure documentation
- License and copyright information

## Release Types

### Major (x.0.0)
- Breaking API changes
- Major feature additions
- Significant architecture changes

### Minor (0.x.0)
- New features (backwards compatible)
- Feature enhancements
- New API endpoints

### Patch (0.0.x)
- Bug fixes
- Security patches
- Documentation updates
- Performance improvements

---

## How to Update This File

When making changes:

1. **Add to [Unreleased]** section first
2. **Categorize** under:
   - `Added` - New features
   - `Changed` - Changes to existing functionality
   - `Deprecated` - Soon-to-be removed features
   - `Removed` - Removed features
   - `Fixed` - Bug fixes
   - `Security` - Security improvements

3. **On Release**, move Unreleased items to new version section

### Example Entry Format

```markdown
### Added
- Feature description ([#123](link-to-pr))
- Another feature with reference to issue #456

### Fixed
- Bug description ([#789](link-to-pr))
```

## Links

- [Repository](https://github.com/YOUR_USERNAME/AntsDocShare)
- [Issues](https://github.com/YOUR_USERNAME/AntsDocShare/issues)
- [Pull Requests](https://github.com/YOUR_USERNAME/AntsDocShare/pulls)

