# GitHub to InfinityFree Deployment Guide

## 🎯 Deploy Colevora from GitHub Repository

Your repository is live at: **https://github.com/Edward2033/colevora-rims**

---

## 📋 Quick Deployment Steps

### Step 1: Push Latest Changes to GitHub

```bash
# Add all changes
git add .

# Commit
git commit -m "Ready for production deployment"

# Push to GitHub
git push origin main
```

### Step 2: Clone to InfinityFree

**Option A: Using Git (if available)**
```bash
cd /htdocs
git clone https://github.com/Edward2033/colevora-rims.git temp
mv temp/* ./
mv temp/.* ./
rm -rf temp
```

**Option B: Download and Upload**
1. Go to: https://github.com/Edward2033/colevora-rims
2. Click "Code" → "Download ZIP"
3. Extract on your computer
4. Upload via FTP to `/htdocs/`

### Step 3: Install Dependencies on Server

**If Composer is available:**
```bash
cd /htdocs
composer install --no-dev --optimize-autoloader
```

**If Composer is NOT available:**
- Upload the `vendor/` folder from your local machine
- Your local vendor folder is already optimized

### Step 4: Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Edit with your InfinityFree credentials
nano .env
```

**Update these values:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=sql202.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_42455956_colevora
DB_USERNAME=if0_42455956
DB_PASSWORD=Edward203Edw
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Import Database

1. Open PHPMyAdmin
2. Login: `if0_42455956` / `Edward203Edw`
3. Select database: `if0_42455956_colevora`
4. Import: `database/colevora_rims.sql`
5. Verify 41 tables created

### Step 7: Set Permissions

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Step 8: Create Storage Link

```bash
php artisan storage:link
```

### Step 9: Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 10: Test Website

Visit: `https://yourdomain.infinityfreeapp.com`

Login with:
- Email: `edwardcole203@gmail.com`
- Password: `password`

---

## 🔄 Update Deployment (After Changes)

1. **Push changes to GitHub:**
```bash
git add .
git commit -m "Update description"
git push origin main
```

2. **Pull on server:**
```bash
cd /htdocs
git pull origin main
```

3. **Clear caches:**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

4. **Re-optimize:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📦 What's Included in Repository

✅ Complete Laravel 12 application  
✅ All source code and configurations  
✅ Database SQL file with data  
✅ Migrations and seeders  
✅ Compiled assets (build/)  
✅ Uploaded images (storage/app/public/)  
✅ Documentation and README  

❌ NOT included (in .gitignore):  
- node_modules/
- vendor/ (install with composer)
- .env file (create from .env.example)

---

## 🚀 Repository Link

**Main Repository**: https://github.com/Edward2033/colevora-rims

**Clone Command**:
```bash
git clone https://github.com/Edward2033/colevora-rims.git
```

---

## ✅ Verification Checklist

After deployment:

- [ ] Repository cloned/uploaded successfully
- [ ] Dependencies installed (composer install)
- [ ] .env file created and configured
- [ ] Application key generated
- [ ] Database imported (41 tables)
- [ ] Storage permissions set (755)
- [ ] Storage link created
- [ ] Caches optimized
- [ ] Homepage loads
- [ ] Can login to admin
- [ ] Images display correctly
- [ ] All features working

---

## 📞 Support

- **Repository Issues**: https://github.com/Edward2033/colevora-rims/issues
- **Email**: edwardcole203@gmail.com

---

**Your GitHub repository is ready for deployment! 🎉**
