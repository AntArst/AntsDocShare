# CI/CD Setup Summary

This document provides an overview of the continuous integration and continuous deployment setup for the AntsDocShare project.

## 📋 Table of Contents

- [Overview](#overview)
- [Files Added](#files-added)
- [Workflows](#workflows)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [Usage Examples](#usage-examples)
- [Troubleshooting](#troubleshooting)

## 🎯 Overview

The project now includes comprehensive CI/CD automation using GitHub Actions. Every push and pull request is automatically tested to ensure code quality and functionality.

### Key Features

✅ **Automated Testing**
- PHP syntax validation
- Database schema validation
- Docker build testing
- Integration testing

✅ **Code Quality Checks**
- PSR-12 compliance
- SQL syntax validation
- Documentation linting
- Dependency review

✅ **Security**
- Vulnerability scanning
- Automated dependency updates via Dependabot
- Security audit checks

✅ **Developer Tools**
- Pre-commit check scripts
- API testing scripts
- Development setup automation
- Comprehensive documentation

## 📁 Files Added

### GitHub Workflows (.github/workflows/)

| File | Purpose | Trigger |
|------|---------|---------|
| `ci.yml` | Main CI pipeline with 6 test jobs | Push/PR to main/develop |
| `code-quality.yml` | Code quality and linting checks | Push/PR to main/develop |
| `docker-publish.yml` | Build and publish Docker images | Git tags (v*.*.*) |

### GitHub Configuration (.github/)

| File | Purpose |
|------|---------|
| `dependabot.yml` | Automated dependency updates |
| `PULL_REQUEST_TEMPLATE.md` | Standardized PR format |
| `ISSUE_TEMPLATE/bug_report.md` | Bug report template |
| `ISSUE_TEMPLATE/feature_request.md` | Feature request template |
| `ISSUE_TEMPLATE/config.yml` | Issue template configuration |
| `WORKFLOWS.md` | Detailed workflow documentation |
| `DEVELOPMENT_GUIDE.md` | Quick reference for developers |
| `CI_CD_SETUP.md` | This file |

### Developer Scripts (scripts/)

| File | Purpose |
|------|---------|
| `pre-commit-check.sh` | Run validation checks before committing |
| `test-api.sh` | Test API endpoints |
| `setup-dev.sh` | Automated development environment setup |

### Storage Directories (server/storage/)

| Directory | Purpose |
|-----------|---------|
| `assets/.gitkeep` | Track assets directory |
| `packages/.gitkeep` | Track packages directory |
| `temp/.gitkeep` | Track temp directory |

### Documentation

| File | Purpose |
|------|---------|
| `CONTRIBUTING.md` | Contribution guidelines |
| `CHANGELOG.md` | Version history |
| `.gitignore` | Enhanced with CI/CD patterns |
| `README.md` | Updated with CI/CD badges |

## 🔄 Workflows

### 1. CI Pipeline (ci.yml)

Comprehensive testing pipeline with 6 parallel jobs:

#### Jobs Overview

```
┌─────────────────────────────────────────────────────────┐
│                   CI Pipeline                           │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌─────────────────┐  ┌─────────────────┐             │
│  │   PHP Lint      │  │   Composer Deps │             │
│  │                 │  │                 │             │
│  │ • Validate JSON │  │ • Cache deps    │             │
│  │ • Check syntax  │  │ • Install deps  │             │
│  └─────────────────┘  └─────────────────┘             │
│                                                         │
│  ┌─────────────────┐  ┌─────────────────┐             │
│  │  Database Test  │  │ Integration Test│             │
│  │                 │  │                 │             │
│  │ • MySQL setup   │  │ • Full stack    │             │
│  │ • Run schema    │  │ • Bootstrap app │             │
│  │ • Verify tables │  │ • Test storage  │             │
│  └─────────────────┘  └─────────────────┘             │
│                                                         │
│  ┌─────────────────┐  ┌─────────────────┐             │
│  │  Docker Build   │  │ Security Check  │             │
│  │                 │  │                 │             │
│  │ • Build images  │  │ • Audit deps    │             │
│  │ • Test compose  │  │ • Check perms   │             │
│  │ • Verify health │  │ • Scan secrets  │             │
│  └─────────────────┘  └─────────────────┘             │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### 2. Code Quality (code-quality.yml)

Checks code standards and project structure:

- **PHP Code Style**: PSR-12 compliance check
- **File Structure**: Validates required files exist
- **Documentation**: Markdown linting
- **SQL Validation**: Basic syntax checks
- **Dependency Review**: Security vulnerability checks
- **Repository Size**: Checks for large files

### 3. Docker Publish (docker-publish.yml)

Publishes Docker images to GitHub Container Registry:

- Triggered on version tags (e.g., `v1.0.0`)
- Builds production-ready images
- Publishes to `ghcr.io`
- Tags with semantic versioning

### 4. Dependabot

Automated dependency updates for:
- PHP Composer packages (weekly, Mondays)
- Docker base images (weekly, Mondays)
- GitHub Actions versions (weekly, Mondays)

## 🚀 Getting Started

### For New Contributors

1. **Fork and clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/AntsDocShare.git
   cd AntsDocShare
   ```

2. **Run the development setup script**
   ```bash
   chmod +x scripts/setup-dev.sh
   ./scripts/setup-dev.sh
   ```

3. **Verify the setup**
   ```bash
   ./scripts/test-api.sh
   ```

### Before Committing Code

Always run the pre-commit checks:

```bash
chmod +x scripts/pre-commit-check.sh
./scripts/pre-commit-check.sh
```

### Creating a Pull Request

1. Create a feature branch from `develop`:
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/your-feature-name
   ```

2. Make your changes and test locally

3. Run pre-commit checks:
   ```bash
   ./scripts/pre-commit-check.sh
   ```

4. Commit with conventional commit format:
   ```bash
   git add .
   git commit -m "feat: your feature description"
   ```

5. Push and create PR:
   ```bash
   git push origin feature/your-feature-name
   ```

6. Fill out the PR template and wait for CI checks

## ⚙️ Configuration

### Workflow Badge URLs

Update the following in `README.md` with your GitHub username:

```markdown
![CI Pipeline](https://github.com/YOUR_USERNAME/AntsDocShare/actions/workflows/ci.yml/badge.svg)
![Code Quality](https://github.com/YOUR_USERNAME/AntsDocShare/actions/workflows/code-quality.yml/badge.svg)
![Docker Publish](https://github.com/YOUR_USERNAME/AntsDocShare/actions/workflows/docker-publish.yml/badge.svg)
```

### Dependabot Configuration

Update `.github/dependabot.yml` to set the correct reviewer:

```yaml
reviewers:
  - "YOUR_USERNAME"
```

### Issue Template Configuration

Update `.github/ISSUE_TEMPLATE/config.yml` with your repository URLs.

## 📚 Usage Examples

### Running Workflows Manually

You can manually trigger workflows from the GitHub Actions tab:

1. Go to `Actions` tab in your repository
2. Select the workflow you want to run
3. Click `Run workflow`
4. Choose the branch and click `Run workflow`

### Viewing Workflow Results

```
Repository → Actions → Select Workflow → View Run
```

Each job shows:
- ✅ Success (green checkmark)
- ❌ Failure (red X)
- 🟡 In Progress (yellow circle)
- ⚪ Skipped (grey circle)

### Local Testing Commands

#### PHP Syntax Check
```bash
cd server
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;
```

#### Composer Validation
```bash
cd server
composer validate --strict
```

#### Docker Build Test
```bash
docker-compose config
docker-compose up -d
docker-compose ps
docker-compose logs
docker-compose down -v
```

#### Database Schema Test
```bash
docker-compose exec mysql mysql -u root -prootpassword pdgp_db -e "SHOW TABLES;"
```

#### API Testing
```bash
./scripts/test-api.sh http://localhost:8080
```

## 🔧 Troubleshooting

### Common CI Failures

#### 1. PHP Syntax Error

**Error**: `PHP Parse error: syntax error...`

**Fix**:
```bash
cd server
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;
```

#### 2. Composer Validation Failed

**Error**: `composer.json is invalid`

**Fix**:
```bash
cd server
composer validate --strict
composer update
```

#### 3. Docker Build Failed

**Error**: `Error building image`

**Fix**:
```bash
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

#### 4. Database Migration Failed

**Error**: `Error executing SQL`

**Fix**:
- Check `server/database/schema.sql` for syntax errors
- Verify all foreign key references exist
- Test locally:
  ```bash
  mysql -u root -p test_db < server/database/schema.sql
  ```

#### 5. Integration Test Failed

**Error**: `Application bootstrap failed`

**Fix**:
- Verify `.env` variables are correct
- Check database connection
- Review PHP error logs:
  ```bash
  docker-compose logs php
  ```

### Workflow Not Running

If workflows don't trigger:

1. **Check branch name**: Workflows trigger on `main` and `develop`
2. **Check file location**: Must be in `.github/workflows/`
3. **Check YAML syntax**: Use YAML validator
4. **Check permissions**: Repository settings → Actions → Allow workflows

### Permission Errors (Linux/Mac)

If you get permission errors with scripts:

```bash
chmod +x scripts/*.sh
```

If storage directories have permission issues:

```bash
chmod -R 775 server/storage
```

Or in Docker:

```bash
docker-compose exec php chown -R www-data:www-data storage
docker-compose exec php chmod -R 775 storage
```

## 📊 Monitoring

### Workflow Status

View all workflow runs:
```
https://github.com/YOUR_USERNAME/AntsDocShare/actions
```

### Dependabot PRs

View automated dependency updates:
```
https://github.com/YOUR_USERNAME/AntsDocShare/pulls
```

Filter by label: `dependencies`

### Security Alerts

View security vulnerabilities:
```
https://github.com/YOUR_USERNAME/AntsDocShare/security/dependabot
```

## 🎓 Best Practices

### Before Pushing Code

1. ✅ Run `./scripts/pre-commit-check.sh`
2. ✅ Test locally with Docker
3. ✅ Update documentation if needed
4. ✅ Use conventional commit messages
5. ✅ Create from `develop` branch

### Pull Request Best Practices

1. ✅ Fill out the PR template completely
2. ✅ Link related issues
3. ✅ Wait for all CI checks to pass
4. ✅ Respond to code review comments
5. ✅ Keep PRs focused and small

### Branch Strategy

```
main (production)
  ↑
develop (integration)
  ↑
feature/xxx (features)
bugfix/xxx (bug fixes)
hotfix/xxx (critical fixes)
```

## 📖 Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)

## 🤝 Getting Help

- **Documentation**: Check [WORKFLOWS.md](WORKFLOWS.md) and [DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md)
- **Issues**: Create an issue using the templates
- **Discussions**: Use GitHub Discussions for questions
- **CI Logs**: Check workflow logs for detailed error messages

## ✨ Summary

The AntsDocShare project now has:

- ✅ 3 comprehensive GitHub Actions workflows
- ✅ Automated testing on every push/PR
- ✅ Code quality enforcement
- ✅ Security vulnerability scanning
- ✅ Automated dependency updates
- ✅ Developer-friendly scripts
- ✅ Complete documentation
- ✅ Issue and PR templates

**Happy coding and continuous integrating!** 🚀

