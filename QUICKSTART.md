# Quick Start Guide - PDGP Server

Get the PDGP server running in 5 minutes!

## Prerequisites

- **Docker Desktop** installed and running
  - Windows: [Download Docker Desktop](https://www.docker.com/products/docker-desktop)
  - Mac: [Download Docker Desktop](https://www.docker.com/products/docker-desktop)
  - Linux: Install Docker and Docker Compose

## Step 1: Create Environment File

Create a `.env` file in the root directory (same folder as this file):

```bash
# Copy this content into a new file named .env
DB_HOST=mysql
DB_PORT=3306
DB_NAME=pdgp_db
DB_USER=pdgp_user
DB_PASSWORD=pdgp_secure_password
DB_ROOT_PASSWORD=root_secure_password
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
JWT_EXPIRATION=3600
APP_ENV=development
APP_URL=http://localhost:8080
```

**Important**: For production, change `JWT_SECRET` and database passwords!

See [ENV_SETUP.md](ENV_SETUP.md) for detailed instructions.

## Step 2: Start the Server

### Option A: Use the Startup Script

**On Windows:**
```powershell
.\start-server.bat
```

**On Linux/Mac:**
```bash
chmod +x start-server.sh
./start-server.sh
```

### Option B: Manual Start

```bash
cd server
docker-compose up -d
docker-compose exec php composer install
```

## Step 3: Access the Application

Once started, open your browser:

- **Web Console**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081 (for database management)

**Default Login:**
- Username: `admin`
- Password: `changeme`

## Step 4: Your First Site

1. **Login** to the web console
2. **Click "Add New Site"** button
3. **Enter a site name** (e.g., "My Store")
4. **Click "Create Site"**

## Step 5: Upload Products

1. **Download the CSV template**:
   - From the sidebar: "Download CSV Template"
   - Or directly: http://localhost:8080/api/template/csv

2. **Edit the CSV** with your product data:
   ```csv
   item_name,image_name,price,description,assets
   Widget A,widget-a.jpg,19.99,Amazing widget,"{\"color\":\"blue\"}"
   Widget B,widget-b.jpg,29.99,Better widget,"{\"color\":\"red\"}"
   ```

3. **Prepare product images** (JPEG, PNG, GIF, or WebP)

4. **Upload to your site**:
   - Click on your site in the dashboard
   - Click "Upload Products"
   - Select your CSV file
   - Select your image files
   - Click "Upload"

## Verification

Your products should now appear in the site details page!

## Using the API

All API endpoints require a JWT token (except login/register).

### Get Token
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"changeme"}'
```

### List Sites
```bash
curl http://localhost:8080/api/sites \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Upload Products
```bash
curl -X POST http://localhost:8080/api/upload \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -F "site_id=1" \
  -F "csv=@products.csv" \
  -F "images[]=@image1.jpg" \
  -F "images[]=@image2.jpg"
```

## Common Commands

### View Logs
```bash
cd server
docker-compose logs -f
```

### Stop Server
```bash
cd server
docker-compose down
```

### Restart Server
```bash
cd server
docker-compose restart
```

### Access Database
```bash
cd server
docker-compose exec mysql mysql -u pdgp_user -p pdgp_db
# Password: pdgp_secure_password
```

### Rebuild Containers
```bash
cd server
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Troubleshooting

### Issue: "Cannot connect to Docker daemon"
**Solution**: Start Docker Desktop and wait for it to fully initialize.

### Issue: "Port 8080 already in use"
**Solution**: 
- Stop other applications using port 8080
- Or change the port in `docker-compose.yml`:
  ```yaml
  nginx:
    ports:
      - "8081:80"  # Change 8080 to 8081
  ```

### Issue: "Database connection failed"
**Solution**: 
- Wait 30 seconds for MySQL to initialize (first start takes longer)
- Verify `.env` credentials match
- Check logs: `docker-compose logs mysql`

### Issue: "Composer dependencies missing"
**Solution**:
```bash
cd server
docker-compose exec php composer install
```

### Issue: "Permission denied on storage"
**Solution**:
```bash
cd server
docker-compose exec php chmod -R 775 storage
docker-compose exec php chown -R www-data:www-data storage
```

## Next Steps

- Change the default admin password (in phpMyAdmin or via SQL)
- Explore the API endpoints (see [server/README.md](server/README.md))
- Configure for production (see [ENV_SETUP.md](ENV_SETUP.md))
- Integrate with the Rust client (coming soon)

## Need Help?

- Check [server/README.md](server/README.md) for detailed documentation
- Review [ENV_SETUP.md](ENV_SETUP.md) for environment configuration
- Check [PDGP.md](PDGP.md) for project architecture

## Architecture Overview

```
Your Browser ──────► Nginx (Port 8080) ──────► PHP-FPM
                                                  │
                                                  ▼
                                              MySQL DB
                                              (Port 3306)
```

All data is persisted in Docker volumes, so your data remains even if you stop/restart containers.

## Default Credentials Summary

**Web Console & API:**
- Username: `admin`
- Password: `changeme`

**phpMyAdmin:**
- Server: `mysql`
- Username: `root`
- Password: `root_secure_password` (from .env)

**MySQL Database:**
- Host: `localhost:3306`
- Database: `pdgp_db`
- Username: `pdgp_user`
- Password: `pdgp_secure_password` (from .env)

---

🎉 **Congratulations!** Your PDGP server is now running!

