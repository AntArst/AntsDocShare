# Environment Configuration

The server requires environment variables to be configured. Create a `.env` file in the root directory with the following content:

## Required .env File

Create a file named `.env` in the root directory (same level as `docker-compose.yml`) with the following content:

```env
# Database Configuration
DB_HOST=mysql
DB_PORT=3306
DB_NAME=pdgp_db
DB_USER=pdgp_user
DB_PASSWORD=pdgp_secure_password
DB_ROOT_PASSWORD=root_secure_password

# JWT Configuration
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
JWT_EXPIRATION=3600

# Application Configuration
APP_ENV=development
APP_URL=http://localhost:8080
```

## Important Notes

### Security Recommendations

1. **JWT_SECRET**: 
   - MUST be changed in production
   - Use a long, random string (at least 32 characters)
   - Generate with: `openssl rand -base64 32` or similar
   - Never commit this to version control

2. **Database Passwords**:
   - Change `DB_PASSWORD` and `DB_ROOT_PASSWORD` for production
   - Use strong passwords (mix of letters, numbers, symbols)
   - Keep these secure and never share publicly

3. **APP_ENV**:
   - Set to `production` when deploying to production servers
   - This affects error reporting and logging

### Variable Descriptions

- `DB_HOST`: MySQL container hostname (use `mysql` for Docker network)
- `DB_PORT`: MySQL port (default 3306)
- `DB_NAME`: Database name for the application
- `DB_USER`: MySQL user for the application
- `DB_PASSWORD`: Password for the application database user
- `DB_ROOT_PASSWORD`: MySQL root password
- `JWT_SECRET`: Secret key for JWT token signing
- `JWT_EXPIRATION`: Token expiration time in seconds (3600 = 1 hour)
- `APP_ENV`: Environment mode (`development` or `production`)
- `APP_URL`: Base URL where the application is accessible

### Creating the File

**On Windows:**
```powershell
# Using PowerShell
New-Item -Path ".env" -ItemType File
notepad .env
# Then paste the content above and save
```

**On Linux/Mac:**
```bash
# Using terminal
touch .env
nano .env
# Or
vim .env
# Then paste the content above and save
```

### Verifying the Setup

After creating the `.env` file:

1. Make sure it's in the same directory as `docker-compose.yml`
2. Check that it's not committed to Git (it should be in `.gitignore`)
3. Verify Docker can read it: `docker-compose config` (should show the variables)

### Troubleshooting

**Problem**: Environment variables not loading

**Solutions**:
- Ensure `.env` is in the correct directory (root, not server/)
- Check file encoding (should be UTF-8, no BOM)
- Restart Docker containers: `docker-compose down && docker-compose up -d`
- Check Docker logs: `docker-compose logs`

**Problem**: Database connection failed

**Solutions**:
- Verify DB credentials match in `.env`
- Wait 30 seconds for MySQL to initialize on first start
- Check MySQL logs: `docker-compose logs mysql`
- Access MySQL directly: `docker-compose exec mysql mysql -u root -p`

### Production Deployment

When deploying to production:

1. Create a production `.env` file with secure values
2. Set `APP_ENV=production`
3. Generate a new strong `JWT_SECRET`
4. Use strong database passwords
5. Update `APP_URL` to your production domain
6. Configure HTTPS/SSL certificates
7. Review and tighten security settings
8. Never expose `.env` file publicly

### Backup

Always keep a secure backup of your production `.env` file in a safe location (password manager, encrypted storage, etc.). Losing these credentials can make it impossible to access your data.

