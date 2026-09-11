# Deployment Guide - Real Estate CRM

## 🚀 Quick Deployment Steps

### Local Development (Windows/Mac/Linux)

```bash
# 1. Clone repository
git clone <repo-url>
cd real-estate-crm

# 2. Create database
mysql -u root -p < sql/schema.sql

# 3. Start server
cd public
php -S localhost:8000

# 4. Access
Open: http://localhost:8000
Login: admin@crm.com / admin123
```

---

## ☁️ Railway.app Deployment (Recommended - Free Tier)

### Step 1: Prepare Project
```bash
# Ensure all files are committed
git add .
git commit -m "Real Estate CRM v1.0"
git push origin main
```

### Step 2: Create Railway Project
1. Go to https://railway.app
2. Sign up (GitHub login recommended)
3. Click "New Project"
4. Select "Deploy from GitHub"
5. Connect your repository

### Step 3: Configure Database
1. In Railway dashboard, click "Add Service"
2. Select "MySQL"
3. Set MySQL version: 5.7 or 8.0
4. Create

### Step 4: Configure Environment Variables
In Railway project settings, add:
```
DB_HOST=mysql
DB_USER=root
DB_PASS=<generated-password>
DB_NAME=real_estate_crm
APP_URL=<your-railway-domain>.railway.app
```

### Step 5: Configure Web Service
1. Click on the main service (PHP app)
2. In "Settings", set:
   - **Build Command:** `composer install` (if using composer)
   - **Start Command:** `php -S 0.0.0.0:$PORT public/`
   - **Port:** 8000 (or use $PORT)

### Step 6: Deploy Database Schema
```bash
# SSH into Railway container
railway run bash

# Import schema
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS < sql/schema.sql
```

### Step 7: Go Live
- Railway automatically deploys on git push
- Your app is live at: `https://<project-name>.railway.app`

---

## 🎯 Heroku Deployment

### Prerequisites
- Heroku CLI installed
- Git repository initialized

### Steps
```bash
# 1. Create Heroku app
heroku create real-estate-crm

# 2. Add MySQL add-on
heroku addons:create cleardb:ignite

# 3. Get database URL
heroku config | grep CLEARDB_DATABASE_URL

# 4. Set config variables
heroku config:set DB_HOST=<host> DB_USER=<user> DB_PASS=<pass> DB_NAME=<name>

# 5. Deploy
git push heroku main

# 6. Import schema
heroku run "mysql -h \$DB_HOST -u \$DB_USER -p\$DB_PASS < sql/schema.sql"

# 7. View logs
heroku logs --tail
```

---

## 🏠 Traditional Shared Hosting (cPanel/Plesk)

### Prerequisites
- PHP 7.4+ with MySQLi support
- MySQL 5.7+
- FTP/SFTP access

### Steps

#### 1. Upload Files
```bash
# Using FTP
- Connect to your hosting FTP
- Upload all project files to public_html/
- Keep "public" folder as root
```

#### 2. Create Database
```bash
# Via cPanel
1. Go to cPanel → MySQL Databases
2. Create new database: real_estate_crm
3. Create user: crm_user
4. Assign user to database (all privileges)
5. Note down credentials
```

#### 3. Import Schema
```bash
# Via PhpMyAdmin
1. Go to PhpMyAdmin
2. Create new database
3. Import sql/schema.sql
```

#### 4. Configure Database
Edit `config/database.php`:
```php
define('DB_HOST', 'your-hosting-server.com');
define('DB_USER', 'crm_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'real_estate_crm');
```

#### 5. Set File Permissions
```bash
chmod 755 /
chmod 755 /public
chmod 755 /config
chmod 644 /config/database.php
```

#### 6. Set Document Root
In cPanel:
1. Go to Addon Domains
2. Set document root to: `public_html/real-estate-crm/public`

#### 7. Access Application
```
https://yourdomain.com
```

---

## 🔒 Production Security Checklist

- [ ] Change default admin password
- [ ] Set `DB_PASS` to strong password
- [ ] Enable HTTPS/SSL certificate
- [ ] Set file permissions correctly
- [ ] Create `.htaccess` for URL rewriting
- [ ] Enable logging and monitoring
- [ ] Regular database backups
- [ ] Test booking duplicate prevention
- [ ] Test role-based access control

### .htaccess for clean URLs (optional)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [L]
</IfModule>
```

---

## 🆘 Troubleshooting

### "Connection to database failed"
- Verify `DB_HOST`, `DB_USER`, `DB_PASS` in `config/database.php`
- Check if MySQL is running
- Verify user has database privileges

### "Sessions not persisting"
- Ensure `session.save_path` is writable
- Check temp directory permissions
- Look at PHP error logs

### "Blank page / 500 error"
- Check PHP error logs
- Verify all required extensions (MySQLi, PDO)
- Check file permissions

### "Can't book unit - unit already booked"
- This is normal! Duplicate prevention is working
- Database constraint prevents double booking
- Check unit status in admin panel

---

## 📊 Monitoring & Maintenance

### Backup Database
```bash
# Regular backups
mysqldump -u root -p real_estate_crm > backup_$(date +%Y%m%d).sql

# Automated (cron job)
0 2 * * * /usr/bin/mysqldump -u root -p real_estate_crm > /backups/crm_$(date +\%Y\%m\%d).sql
```

### Monitor Performance
- Check PHP error logs: `tail -f /var/log/apache2/error.log`
- Monitor database: Check slow query logs
- Check disk space: `df -h`

### Update Passwords Regularly
```php
// Via admin panel or directly:
UPDATE users SET password = PASSWORD('new_password') WHERE id = 1;
```

---

## 📞 Support

For deployment issues:
1. Check application logs
2. Review database credentials
3. Verify file permissions
4. Test connection manually: `mysql -h host -u user -p`

**Success! Your CRM is now live 🎉**
