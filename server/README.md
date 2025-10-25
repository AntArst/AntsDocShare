# PDGP Server

PHP-based server for the Product Display Generator Project with JWT authentication, multi-tenant site management, and API endpoints for product data upload/download.

## Features

- **JWT Authentication**: Secure token-based authentication
- **Multi-tenant Sites**: Multiple client organizations/locations
- **RESTful API**: Complete API for product management
- **Web Console**: Bootstrap-based management interface
- **Asset Management**: Image upload, optimization, and storage
- **CSV Template**: Sample CSV generator for product data

## Prerequisites

- Docker
- Docker Compose

## Quick Start

1. **Copy environment file**:
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` and update the values, especially:
   - `JWT_SECRET` - Use a strong random string
   - Database passwords

2. **Start the services**:
   ```bash
   docker-compose up -d
   ```

3. **Install PHP dependencies**:
   ```bash
   docker-compose exec php composer install
   ```

4. **Access the application**:
   - Web Console: http://localhost:8080
   - phpMyAdmin: http://localhost:8081
   - API Base: http://localhost:8080/api

5. **Default credentials**:
   - Username: `admin`
   - Password: `changeme`

## Project Structure

```
server/
├── docker-compose.yml      # Docker services configuration
├── Dockerfile              # PHP container setup
├── nginx.conf             # Nginx web server config
├── composer.json          # PHP dependencies
├── database/              # Database schema and seeds
│   ├── schema.sql
│   └── seed.sql
├── public/                # Public web root
│   └── index.php         # Application entry point
├── src/                   # Application source code
│   ├── Api/              # API routing
│   ├── Auth/             # JWT authentication
│   ├── Config/           # Configuration classes
│   ├── Controllers/      # Request handlers
│   ├── Models/           # Data models
│   └── Services/         # Business logic
├── views/                 # Web interface templates
│   ├── layout.php
│   ├── login.php
│   ├── dashboard.php
│   ├── site-add.php
│   └── site-detail.php
└── storage/               # File storage
    ├── assets/           # Uploaded images
    ├── packages/         # Generated packages
    └── temp/             # Temporary files
```

## API Endpoints

### Authentication
- `POST /api/auth/login` - Login and receive JWT token
- `POST /api/auth/register` - Register new user
- `POST /api/auth/refresh` - Refresh JWT token

### Sites
- `GET /api/sites` - List all sites (filtered by user)
- `POST /api/sites` - Create new site
- `GET /api/sites/{id}` - Get site details with products
- `PUT /api/sites/{id}` - Update site
- `DELETE /api/sites/{id}` - Deactivate site

### Products
- `POST /api/upload` - Upload CSV + images (requires `site_id` parameter)
  - Form fields: `csv` (file), `images[]` (files), `site_id` (int)

### Packages
- `GET /api/packages/{site_id}/latest` - Get latest package for site
- `GET /api/packages/{upload_id}` - Get specific package

### Template
- `GET /api/template/csv` - Download CSV template

## API Usage Examples

### Login
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"changeme"}'
```

### List Sites
```bash
curl http://localhost:8080/api/sites \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Upload Products
```bash
curl -X POST http://localhost:8080/api/upload \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "site_id=1" \
  -F "csv=@products.csv" \
  -F "images[]=@product1.jpg" \
  -F "images[]=@product2.jpg"
```

## Database Schema

- **users** - User accounts with roles
- **sites** - Client organizations/locations
- **products** - Product data per site
- **uploads** - Upload tracking
- **generated_packages** - Generated package metadata

## Development

### View Logs
```bash
docker-compose logs -f
```

### Access PHP Container
```bash
docker-compose exec php sh
```

### Access MySQL
```bash
docker-compose exec mysql mysql -u pdgp_user -p pdgp_db
```

### Rebuild Containers
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## CSV Format

The CSV file should have the following columns:

```csv
item_name,image_name,price,description,assets
Product Name,product.jpg,19.99,Product description,"{\"color\":\"blue\",\"size\":\"medium\"}"
```

- `item_name` (required): Product name
- `image_name` (optional): Filename of uploaded image
- `price` (optional): Product price
- `description` (optional): Product description
- `assets` (optional): JSON string with additional attributes

Download the template from the web console or API endpoint.

## Security Notes

- Change default admin password immediately
- Use a strong `JWT_SECRET` in production
- Keep `.env` file secure and never commit it
- Use HTTPS in production
- Configure proper CORS settings for production

## Troubleshooting

### Database connection failed
- Ensure MySQL container is running: `docker-compose ps`
- Check database credentials in `.env`
- Wait for MySQL to fully initialize (may take 30 seconds on first start)

### Permission denied on storage
```bash
docker-compose exec php chmod -R 775 storage
docker-compose exec php chown -R www-data:www-data storage
```

### Composer dependencies missing
```bash
docker-compose exec php composer install
```

## Future Enhancements

- AI model integration for code generation
- Package generation and bundling
- Device deployment mechanisms
- Enhanced asset management
- Batch operations
- User management UI

## License

This software is licensed under a **Non-Commercial License**. 

✅ Free for personal, educational, and research use  
❌ Commercial use requires separate licensing

See [../LICENSE](../LICENSE) for complete terms.

