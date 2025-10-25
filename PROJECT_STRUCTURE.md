# PDGP Project Structure

Complete file structure of the Product Display Generator Project.

## Root Directory

```
AntsDocShare/
├── .gitignore                      # Git ignore rules
├── docker-compose.yml              # Docker orchestration
├── PDGP.md                         # Project specification
├── README.md                       # Main project README
├── QUICKSTART.md                   # Quick start guide
├── ENV_SETUP.md                    # Environment setup guide
├── PROJECT_STRUCTURE.md            # This file
├── start-server.sh                 # Linux/Mac startup script
├── start-server.bat                # Windows startup script
├── .env                            # Environment variables (create this!)
└── server/                         # Server application
```

## Server Directory Structure

```
server/
├── Dockerfile                      # PHP container configuration
├── nginx.conf                      # Nginx web server config
├── composer.json                   # PHP dependencies
├── composer.lock                   # Dependency lock file
├── .htaccess                       # URL rewriting rules
├── .gitignore                      # Server-specific git ignore
├── README.md                       # Server documentation
│
├── database/                       # Database files
│   ├── schema.sql                 # Database schema
│   └── seed.sql                   # Initial data
│
├── public/                         # Public web root
│   ├── index.php                  # Application entry point
│   └── css/
│       └── style.css              # Custom styles
│
├── src/                            # Application source code
│   ├── Api/
│   │   └── Router.php             # API routing logic
│   │
│   ├── Auth/
│   │   ├── JWTHandler.php         # JWT token management
│   │   └── AuthMiddleware.php    # Authentication middleware
│   │
│   ├── Config/
│   │   ├── Database.php           # Database connection
│   │   └── App.php                # Application configuration
│   │
│   ├── Controllers/
│   │   ├── AuthController.php     # Authentication endpoints
│   │   ├── SiteController.php     # Site management endpoints
│   │   └── UploadController.php   # Product upload endpoints
│   │
│   ├── Models/
│   │   ├── User.php               # User model
│   │   ├── Site.php               # Site model
│   │   └── Product.php            # Product model
│   │
│   └── Services/
│       ├── AssetManager.php       # Image/asset management
│       └── TemplateGenerator.php  # CSV template generator
│
├── views/                          # Web interface templates
│   ├── layout.php                 # Main layout template
│   ├── login.php                  # Login page
│   ├── dashboard.php              # Dashboard/home page
│   ├── site-add.php               # Add site form
│   └── site-detail.php            # Site details page
│
├── storage/                        # File storage
│   ├── assets/                    # Uploaded images
│   │   └── .gitkeep
│   ├── packages/                  # Generated packages
│   │   └── .gitkeep
│   └── temp/                      # Temporary files
│       └── .gitkeep
│
└── vendor/                         # Composer dependencies (auto-generated)
```

## Docker Services

The application runs in multiple Docker containers:

```
┌─────────────────────────────────────────────────────────┐
│                     Docker Network                       │
│                                                          │
│  ┌─────────────┐    ┌─────────────┐    ┌────────────┐ │
│  │   Nginx     │───►│   PHP-FPM   │───►│   MySQL    │ │
│  │  Port 8080  │    │   PHP 8.2   │    │  Port 3306 │ │
│  └─────────────┘    └─────────────┘    └────────────┘ │
│                                                          │
│  ┌─────────────┐                                        │
│  │ phpMyAdmin  │                                        │
│  │  Port 8081  │                                        │
│  └─────────────┘                                        │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

## Database Schema

```
┌──────────┐         ┌──────────┐         ┌──────────┐
│  users   │         │  sites   │         │ products │
├──────────┤         ├──────────┤         ├──────────┤
│ id       │◄───┐    │ id       │◄────┐   │ id       │
│ username │    │    │ name     │     │   │ site_id  │──┐
│ email    │    │    │ slug     │     │   │ item_name│  │
│ password │    └────│ owner_id │     └───│ image    │  │
│ role     │         │ active   │         │ price    │  │
└──────────┘         └──────────┘         │ assets   │  │
                                           └──────────┘  │
                                                 ▲        │
                                                 │        │
┌──────────┐         ┌────────────────┐         │        │
│ uploads  │         │ generated_pkgs │         │        │
├──────────┤         ├────────────────┤         │        │
│ id       │◄────┐   │ id             │         │        │
│ site_id  │─────┼──►│ upload_id      │         │        │
│ user_id  │     │   │ site_id        │─────────┘        │
│ status   │     └───│ package_path   │                  │
│ path     │         │ version        │                  │
└──────────┘         └────────────────┘
```

## API Endpoints

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `POST /api/auth/refresh` - Token refresh

### Sites
- `GET /api/sites` - List all sites
- `POST /api/sites` - Create new site
- `GET /api/sites/{id}` - Get site details
- `PUT /api/sites/{id}` - Update site
- `DELETE /api/sites/{id}` - Deactivate site

### Products
- `POST /api/upload` - Upload products (CSV + images)

### Packages
- `GET /api/packages/{site_id}/latest` - Get latest package
- `GET /api/packages/{upload_id}` - Get specific package

### Utilities
- `GET /api/template/csv` - Download CSV template

## Web Routes

- `GET /` - Dashboard (requires login)
- `GET /login` - Login page
- `GET /sites/add` - Add site form
- `GET /sites/{id}` - Site details

## Data Flow

```
1. User Login
   Browser ──► POST /api/auth/login ──► AuthController
                                         │
                                         ▼
                                    User Model ──► MySQL
                                         │
                                         ▼
                                    JWT Token ──► Browser

2. Create Site
   Browser ──► POST /api/sites ──► SiteController
                                     │
                                     ▼
                                 Site Model ──► MySQL

3. Upload Products
   Browser ──► POST /api/upload ──► UploadController
                                      │
                                      ├──► CSV Parser
                                      │
                                      ├──► AssetManager ──► Storage
                                      │
                                      └──► Product Model ──► MySQL

4. View Dashboard
   Browser ──► GET / ──► Router ──► views/dashboard.php
                                     │
                                     ▼
                              GET /api/sites ──► SiteController
                                                   │
                                                   ▼
                                              Site Model ──► MySQL
```

## File Size Notes

**Important Files:**
- Most PHP files: 1-5 KB
- Views: 3-8 KB each
- Database schema: ~2 KB
- Docker configs: 1-2 KB each

**Storage Growth:**
- MySQL data: Grows with usage
- Uploaded images: Depends on uploads (optimized to max 1920x1920)
- Generated packages: Future implementation

## Security Layers

```
┌─────────────────────────────────────────┐
│  1. CORS Headers (API Level)            │
│  2. JWT Authentication (Middleware)     │
│  3. User Authorization (Controller)     │
│  4. Input Validation (Controller)       │
│  5. SQL Prepared Statements (Model)     │
│  6. File Type Validation (AssetManager) │
│  7. Image Optimization (AssetManager)   │
└─────────────────────────────────────────┘
```

## Environment Variables

Required in `.env` file:

```env
# Database
DB_HOST=mysql
DB_PORT=3306
DB_NAME=pdgp_db
DB_USER=pdgp_user
DB_PASSWORD=<secure-password>
DB_ROOT_PASSWORD=<secure-password>

# JWT
JWT_SECRET=<random-secret-key>
JWT_EXPIRATION=3600

# Application
APP_ENV=development
APP_URL=http://localhost:8080
```

## Development Workflow

```
1. Make changes to PHP files
   ↓
2. Changes auto-reload (no restart needed for PHP)
   ↓
3. Test in browser or via API
   ↓
4. Check logs: docker-compose logs -f
   ↓
5. Commit changes to Git
```

## Production Deployment

When deploying to production:

1. Set `APP_ENV=production` in `.env`
2. Generate new `JWT_SECRET`
3. Use strong database passwords
4. Configure HTTPS/SSL
5. Set up proper domain in `APP_URL`
6. Configure firewall rules
7. Set up regular database backups
8. Monitor logs and errors

## Technology Versions

- **PHP**: 8.2 (Alpine Linux)
- **MySQL**: 8.0
- **Nginx**: Alpine latest
- **phpMyAdmin**: Latest
- **Composer**: Latest (v2.x)
- **Firebase JWT**: 6.10+
- **Bootstrap**: 5.3.0 (CDN)
- **Bootstrap Icons**: 1.10.0 (CDN)

## Key Design Patterns

- **MVC Pattern**: Models, Views, Controllers separation
- **Repository Pattern**: Models handle data access
- **Middleware Pattern**: JWT authentication
- **Service Pattern**: AssetManager, TemplateGenerator
- **Router Pattern**: Centralized routing logic
- **PSR-4 Autoloading**: Composer autoload

## Future Additions (Planned)

```
AntsDocShare/
├── client/                  # Rust client application
│   ├── src/
│   └── Cargo.toml
│
├── ai-model/               # AI code generation
│   └── model/
│
└── yocto/                  # Device integration
    └── recipes/
```

---

This structure provides a clean, organized, and scalable foundation for the PDGP project.

