# Docker Deployment Guide - Colevora Restaurant ERP

## 🐳 Docker Setup Complete

Your Laravel 12 Colevora Restaurant Management System is now ready for Docker deployment on Render or any Docker-compatible platform.

---

## 📦 What's Included

### Docker Configuration Files

1. **Dockerfile** - Production-ready PHP 8.2 + Apache container
2. **docker-compose.yml** - Local testing with MySQL 8
3. **.dockerignore** - Optimized file exclusions
4. **docker-entrypoint.sh** - Container startup script
5. **.env.example** - Docker-ready environment template

---

## 🚀 Quick Start (Local Testing)

### Prerequisites

- Docker Desktop installed
- Docker Compose installed

### Step 1: Create Environment File

```bash
cp .env.example .env
```

Edit `.env` if needed (defaults are set for Docker):

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=colevora_rims
DB_USERNAME=colevora_user
DB_PASSWORD=CoLev0ra_S3cur3_P@ssw0rd!2024
```

### Step 2: Build Docker Image

```bash
docker compose build
```

This will:
- Install PHP 8.2 with Apache
- Install all required PHP extensions
- Install Composer dependencies
- Install Node.js 22
- Build Vite assets with `npm run build`
- Set proper permissions

### Step 3: Start Containers

```bash
docker compose up
```

Or run in background:

```bash
docker compose up -d
```

### Step 4: Access Application

Open browser: **http://localhost**

**Default Login:**
- Email: `edwardcole203@gmail.com`
- Password: `password`

### Step 5: Stop Containers

```bash
docker compose down
```

To remove volumes (database data):

```bash
docker compose down -v
```

---

## 🏗️ Container Architecture

### Application Container (app)
- **Base**: PHP 8.2 Apache
- **Port**: 80
- **Extensions**: pdo_mysql, mbstring, bcmath, gd, xml, zip, intl, curl, opcache
- **Tools**: Composer, Node.js 22, NPM
- **DocumentRoot**: `/var/www/html/public`

### Database Container (mysql)
- **Image**: MySQL 8.0
- **Port**: 3306
- **Database**: colevora_rims
- **User**: colevora_user
- **Auto-imports**: `database/colevora_rims.sql` on first run

---

## 🔧 Container Startup Process

The `docker-entrypoint.sh` script runs automatically:

1. Waits for MySQL to be ready
2. Runs database migrations: `php artisan migrate --force`
3. Creates storage link: `php artisan storage:link`
4. Clears caches
5. Optimizes application (caches config, routes, views)
6. Sets proper permissions
7. Starts Apache

---

## 📊 Docker Commands Reference

### Build and Run

```bash
# Build images
docker compose build

# Start containers
docker compose up

# Start in background
docker compose up -d

# View logs
docker compose logs -f

# View app logs only
docker compose logs -f app
```

### Container Management

```bash
# Stop containers
docker compose stop

# Start existing containers
docker compose start

# Restart containers
docker compose restart

# Remove containers
docker compose down

# Remove containers and volumes
docker compose down -v
```

### Execute Commands in Container

```bash
# Open bash shell
docker compose exec app bash

# Run artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear
docker compose exec app php artisan optimize

# Check Laravel version
docker compose exec app php artisan --version
```

### Database Access

```bash
# Connect to MySQL
docker compose exec mysql mysql -u colevora_user -p colevora_rims

# Export database
docker compose exec mysql mysqldump -u colevora_user -p colevora_rims > backup.sql

# Import database
docker compose exec -T mysql mysql -u colevora_user -p colevora_rims < backup.sql
```

---

## 🌐 Deploy to Render

### Prerequisites

1. Render account (free tier available)
2. GitHub repository with your code
3. Docker support enabled on Render

### Step 1: Push to GitHub

```bash
git add .
git commit -m "Add Docker configuration for Render deployment"
git push origin main
```

### Step 2: Create Web Service on Render

1. Go to [Render Dashboard](https://dashboard.render.com/)
2. Click "New +" → "Web Service"
3. Connect your GitHub repository
4. Configure:
   - **Name**: colevora-restaurant-erp
   - **Environment**: Docker
   - **Region**: Choose nearest region
   - **Branch**: main
   - **Dockerfile Path**: ./Dockerfile (default)

### Step 3: Add Environment Variables

In Render dashboard, add:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key-here
APP_URL=https://your-app.onrender.com

DB_CONNECTION=mysql
DB_HOST=your-render-mysql-host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password
```

### Step 4: Add MySQL Database

**Option A: Render PostgreSQL (Recommended for Render)**
- Render offers free PostgreSQL
- Update `DB_CONNECTION=pgsql` in .env
- Install `pdo_pgsql` extension in Dockerfile

**Option B: External MySQL**
- Use services like PlanetScale, Railway, or AWS RDS
- Add connection details to environment variables

### Step 5: Deploy

1. Click "Create Web Service"
2. Render will:
   - Clone your repository
   - Build Docker image
   - Deploy container
   - Assign URL

### Step 6: Run Migrations

After first deployment:

```bash
# SSH into Render container (from Render Shell)
php artisan migrate --force
php artisan storage:link
```

---

## 🔐 Environment Variables for Render

### Required Variables

```env
APP_NAME=Colevora
APP_ENV=production
APP_KEY=base64:generated-by-artisan-key-generate
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

DB_CONNECTION=mysql
DB_HOST=mysql-host
DB_PORT=3306
DB_DATABASE=colevora_rims
DB_USERNAME=db_user
DB_PASSWORD=secure_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Generate APP_KEY

Run locally:
```bash
php artisan key:generate --show
```

Copy the output to Render environment variables.

---

## 🐛 Troubleshooting

### Issue: Container Fails to Start

**Solution:**
```bash
# Check logs
docker compose logs app

# Common fixes
docker compose down
docker compose build --no-cache
docker compose up
```

### Issue: Database Connection Failed

**Solution:**
```bash
# Verify MySQL is running
docker compose ps

# Check MySQL logs
docker compose logs mysql

# Test connection
docker compose exec app php artisan db:show
```

### Issue: Permission Errors

**Solution:**
```bash
# Fix permissions inside container
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Issue: Assets Not Loading

**Solution:**
```bash
# Rebuild assets
docker compose exec app npm run build

# Or rebuild container
docker compose build --no-cache
docker compose up -d
```

### Issue: Port 80 Already in Use

**Solution:**
```bash
# Change port in docker-compose.yml
ports:
  - "8080:80"  # Use 8080 instead

# Then access: http://localhost:8080
```

---

## 📦 What Happens During Build

### Dockerfile Build Steps

1. **Base Image**: PHP 8.2 with Apache
2. **System Dependencies**: Install curl, git, libraries
3. **PHP Extensions**: Install pdo_mysql, mbstring, bcmath, gd, xml, zip, intl, curl, opcache
4. **Composer**: Install from official image
5. **Node.js 22**: Install from NodeSource
6. **Apache Config**: Enable mod_rewrite, set DocumentRoot to `/var/www/html/public`
7. **Application Code**: Copy project files
8. **Composer Install**: `composer install --no-dev --optimize-autoloader`
9. **NPM Build**: `npm install && npm run build`
10. **Permissions**: Set storage and bootstrap/cache permissions
11. **Entrypoint**: Copy and enable startup script

### Container Startup (entrypoint.sh)

1. Wait for MySQL connection
2. Run migrations
3. Create storage link
4. Clear caches
5. Optimize (cache config, routes, views)
6. Set permissions
7. Start Apache

---

## 📊 Resource Requirements

### Minimum (Development)
- **CPU**: 1 core
- **RAM**: 1 GB
- **Storage**: 2 GB

### Recommended (Production)
- **CPU**: 2 cores
- **RAM**: 2 GB
- **Storage**: 5 GB

### Render Free Tier
- ✅ Sufficient for testing
- ⚠️ Spins down after 15 min inactivity
- ⚠️ Limited to 512 MB RAM

---

## 🔍 Verification Checklist

After deployment:

- [ ] Container builds successfully
- [ ] Application starts without errors
- [ ] Homepage loads at http://localhost
- [ ] Database connection works
- [ ] Can login with admin credentials
- [ ] Images display correctly
- [ ] Admin dashboard loads
- [ ] Mobile responsive design works
- [ ] All features functional

---

## 📝 Additional Configuration

### Custom Domain on Render

1. Go to Render dashboard
2. Select your service
3. Settings → Custom Domains
4. Add your domain
5. Update DNS records as instructed
6. Update `APP_URL` in environment variables

### SSL Certificate

Render automatically provides SSL certificates for:
- `.onrender.com` subdomains
- Custom domains

No additional configuration needed!

### Scaling

Render allows:
- Vertical scaling (increase RAM/CPU)
- Horizontal scaling (multiple instances) on paid plans

---

## 🎯 Next Steps

1. ✅ Test locally with `docker compose up`
2. ✅ Verify all features work
3. ✅ Push to GitHub
4. ✅ Deploy to Render
5. ✅ Configure environment variables
6. ✅ Run migrations
7. ✅ Test production site
8. ✅ Add custom domain (optional)

---

## 📞 Support

- **GitHub Issues**: https://github.com/Edward2033/colevora-rims/issues
- **Render Docs**: https://render.com/docs
- **Laravel Docs**: https://laravel.com/docs/12.x/deployment

---

**Your Docker setup is complete and ready for deployment! 🎉**
