# Product Display Generator Project (PDGP)

![CI Pipeline](https://github.com/AntArst/AntsDocShare/actions/workflows/ci.yml/badge.svg)
![Code Quality](https://github.com/AntArst/AntsDocShare/actions/workflows/code-quality.yml/badge.svg)
![Docker Publish](https://github.com/AntArst/AntsDocShare/actions/workflows/docker-publish.yml/badge.svg)

A streamlined system for generating and deploying product display interfaces to embedded devices. Users provide product data through a simple application, which is processed on the server side to automatically generate frontend code using an AI model.

## Project Status

✅ **Server Infrastructure** - Complete  
🚧 **Rust Client Application** - Planned  
🚧 **AI Code Generation** - Planned  
🚧 **Yocto Device Integration** - Planned  

## Components

### Server (PHP + Docker)
Located in `server/` directory.

**Features:**
- JWT-based authentication
- Multi-tenant site management
- RESTful API for product upload/download
- Web management console
- Asset management with image optimization
- MySQL database
- Docker containerized

**Quick Start:**
```bash
cd server
docker-compose up -d
docker-compose exec php composer install
```

Access at http://localhost:8080 (default login: admin/changeme)

See [server/README.md](server/README.md) for detailed documentation.

### Client (Rust) - Coming Soon
Lightweight desktop application for uploading product data and images to the server.

### Device Integration (Yocto) - Coming Soon
Embedded Linux distribution for displaying generated product interfaces on devices.

## Architecture

```
┌─────────────────┐
│  Rust Client    │  Upload CSV + Images
│  Application    │────────────┐
└─────────────────┘            │
                               ▼
                    ┌──────────────────┐
                    │   PHP Server     │
                    │   - Auth (JWT)   │
                    │   - Site Mgmt    │
                    │   - API          │
                    │   - Assets       │
                    └──────────────────┘
                               │
                               ▼
                    ┌──────────────────┐
                    │  AI Model (1-bit)│
                    │  Code Generation │
                    └──────────────────┘
                               │
                               ▼
                    ┌──────────────────┐
                    │  Generated Pkg   │
                    │  (Code + Assets) │
                    └──────────────────┘
                               │
                               ▼
                    ┌──────────────────┐
                    │  Yocto Device    │
                    │  Display UI      │
                    └──────────────────┘
```

## Workflow

1. **User Input**: Open Rust client, paste spreadsheet data, upload images
2. **Server Processing**: Authenticate, parse data, store assets, generate code (via AI)
3. **Package Generation**: Bundle code and assets for deployment
4. **Device Deployment**: Yocto device downloads and displays the generated interface

## Technology Stack

- **Server**: PHP 8.2, MySQL 8.0, Nginx, Docker
- **Client**: Rust (planned)
- **AI**: 1-bit model for code generation (planned)
- **Devices**: Yocto Linux (planned)

## Getting Started

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd AntsDocShare
   ```

2. **Start the server** (see server/README.md):
   ```bash
   cd server
   docker-compose up -d
   ```

3. **Access the web console**:
   - Open http://localhost:8080
   - Login with admin/changeme
   - Create a site
   - Download CSV template
   - Upload products with images

## API Documentation

See [server/README.md](server/README.md) for complete API documentation.

**Key Endpoints:**
- `POST /api/auth/login` - Authentication
- `GET /api/sites` - List sites
- `POST /api/upload` - Upload products
- `GET /api/template/csv` - Download CSV template

## CI/CD and Testing

This project includes comprehensive GitHub Actions workflows for continuous integration and deployment:

### Automated Testing
- **PHP Syntax Checks**: Validates all PHP code for syntax errors
- **Composer Validation**: Ensures dependencies are properly configured
- **Database Testing**: Validates schema and seed data
- **Docker Build Tests**: Ensures containers build and run correctly
- **Integration Tests**: Tests the complete application stack
- **Security Audits**: Checks for vulnerable dependencies

### Code Quality
- **PSR-12 Compliance**: PHP code style checking
- **File Structure Validation**: Ensures required files exist
- **SQL Syntax Validation**: Checks database scripts
- **Documentation Linting**: Validates markdown files
- **Dependency Review**: Monitors for security vulnerabilities

### Continuous Deployment
- **Docker Image Publishing**: Automatically publishes tagged releases to GitHub Container Registry
- **Dependabot**: Automated dependency updates

See [.github/WORKFLOWS.md](.github/WORKFLOWS.md) for detailed documentation on CI/CD workflows.

### Running Tests Locally

```bash
# PHP Syntax Check
cd server && find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

# Validate Composer
cd server && composer validate --strict

# Test Docker Build
docker-compose config
docker-compose up -d
docker-compose ps
docker-compose down -v

# Database Validation
mysql -u root -p pdgp_test < server/database/schema.sql
mysql -u root -p pdgp_test < server/database/seed.sql
```

## Development Roadmap

- [x] Server infrastructure with Docker
- [x] Database schema and models
- [x] JWT authentication
- [x] RESTful API
- [x] Web management console
- [x] Asset management
- [x] CSV template generator
- [x] CI/CD workflows with GitHub Actions
- [ ] Rust client application
- [ ] AI model integration
- [ ] Package generation
- [ ] Yocto device integration
- [ ] End-to-end testing

## Contributing

This is an active development project. See [PDGP.md](PDGP.md) for detailed project specifications and architecture decisions.

## License

**Non-Commercial Use Only**

This project is licensed under a custom Non-Commercial License. You are free to:
- ✅ Use for personal, educational, or research purposes
- ✅ Modify and create derivative works (non-commercially)
- ✅ Study and learn from the code

You may **NOT**:
- ❌ Use for commercial purposes without permission
- ❌ Sell or monetize the software or derivatives
- ❌ Offer as a commercial service (SaaS)

For commercial licensing inquiries, please open an issue in the repository.

See [LICENSE](LICENSE) for full terms and conditions.

## Contact

For questions, suggestions, or commercial licensing:
- Open an issue on GitHub
- Refer to project documentation

