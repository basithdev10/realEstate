# 🚀 REAL ESTATE CRM - COMPLETE SETUP GUIDE

## ⚡ Quick Start (5 Minutes)

### **Step 1: Extract ZIP**
```bash
# Extract the real-estate-crm.zip file
# Folder structure should be:
real-estate-crm/
├── public/
├── config/
├── models/
├── views/
├── schema.sql
└── ...
```

---

### **Step 2: Create Database**

**Option A: MySQL Command Line**
```bash
# Open terminal/command prompt
mysql -u root -p

# Then paste this (press Enter after each line):
CREATE DATABASE real_estate_crm;
USE real_estate_crm;
# Then paste contents of schema.sql
```

**Option B: MySQL Workbench (GUI)**
1. Open MySQL Workbench
2. New connection → localhost
3. File → Run SQL Script
4. Select `schema.sql` from project folder
5. Click Execute

**Option C: phpMyAdmin (Web)**
1. Go to http://localhost/phpmyadmin
2. New Database: `real_estate_crm`
3. Import `schema.sql` file

---

### **Step 3: Configure Database (If Needed)**

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');    // Your MySQL host
define('DB_USER', 'root');         // Your MySQL username
define('DB_PASS', '');             // Your MySQL password
define('DB_NAME', 'real_estate_crm');  // Database name
```

---

### **Step 4: Start PHP Server**

**Option A: Using Command Line**
```bash
cd real-estate-crm/public
php -S localhost:8000
```

**Option B: Using VS Code Extension**
1. Install "PHP Server" extension
2. Right-click `public/index.php`
3. Click "PHP Server: Serve project"

**Option C: Using Built-in PHP Server (if installed)**
```bash
php -S 127.0.0.1:8000
```

---

### **Step 5: Access Application**

Open browser:
```
http://localhost:8000
```

You should see **Login Page** ✅

---

## 🔐 Login Credentials

### **Admin Account (Full Access)**
- **Email:** `admin@crm.com`
- **Password:** `admin123`

### **Sales Employee Account**
- **Email:** `john@crm.com`
- **Password:** `admin123`

### **Another Sales Employee**
- **Email:** `sarah@crm.com`
- **Password:** `admin123`

---

## 🐛 Troubleshooting Login Issues

### **Problem: "Connection failed: Connection refused"**

**Solution:**
1. Check MySQL is running
   ```bash
   mysql -u root -p
   # Should connect successfully
   ```
2. Verify database exists
   ```bash
   mysql -u root -p -e "SHOW DATABASES;"
   # Should show: real_estate_crm
   ```
3. Verify schema imported
   ```bash
   mysql -u root -p real_estate_crm -e "SHOW TABLES;"
   # Should show: users, leads, bookings, etc.
   ```

### **Problem: "Invalid email or password"**

**Solution:**
1. Verify database has users:
   ```bash
   mysql -u root -p real_estate_crm -e "SELECT * FROM users;"
   ```
2. Should show 3 users: admin, john, sarah

3. If empty, re-import schema.sql

### **Problem: Blank page / 500 error**

**Solution:**
1. Check PHP error log
   ```bash
   # Windows: Check Event Viewer
   # Mac/Linux: tail -f /var/log/apache2/error.log
   ```
2. Verify file permissions:
   ```bash
   chmod 755 public/
   chmod 755 config/
   ```

### **Problem: Database not connecting**

**Double-check credentials:**
```php
// config/database.php should have:
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Your username
define('DB_PASS', '');            // Your password (if any)
define('DB_NAME', 'real_estate_crm');
```

Test connection manually:
```bash
mysql -h localhost -u root -p -D real_estate_crm
```

---

## 📁 Project Structure

```
real-estate-crm/
├── public/
│   └── index.php                 ← Main entry point
├── config/
│   ├── database.php              ← Database connection
│   └── constants.php             ← App constants
├── models/
│   ├── User.php                  ← User authentication
│   ├── Lead.php                  ← Lead management
│   ├── Property.php              ← Property management
│   └── Booking.php               ← Booking system
├── includes/
│   └── helpers.php               ← Helper functions
├── views/
│   ├── auth/
│   │   └── auth.php              ← Login page
│   ├── dashboard/
│   │   └── dashboard.php         ← Dashboard
│   ├── leads/
│   │   ├── leads.php
│   │   ├── list.php
│   │   ├── create.php
│   │   ├── view.php
│   │   └── edit.php
│   ├── properties/
│   │   └── properties.php
│   ├── bookings/
│   │   └── bookings.php
│   └── layout/
│       └── main.php
├── schema.sql                    ← Database schema
├── README.md                     ← Full documentation
└── SETUP.md                      ← This file
```

---

## ✨ Features Available After Login

✅ **Dashboard** - View KPIs and sales pipeline
✅ **Leads** - Create, edit, search leads
✅ **Properties** - View projects and units
✅ **Bookings** - Manage property bookings
✅ **Notes** - Add follow-up notes to leads
✅ **Filtering** - Search by stage, status, etc.

---

## 🔧 Testing Checklist

After logging in, test these:

### **1. Create a Lead**
- Go to Leads → Add Lead
- Fill: Name, Email, Phone, Budget
- Click Create Lead
- Should see new lead in list

### **2. View Lead Details**
- Click on any lead name
- Should see all details + notes section
- Should see booking history

### **3. Manage Lead**
- Add a note with follow-up date
- Change lead stage
- Verify changes saved

### **4. View Properties**
- Go to Properties
- Should see all projects & units
- Filter by status, type, project

### **5. View Bookings**
- Go to Bookings
- Should see all bookings
- Filter by status (Confirmed, Pending, Cancelled)

---

## 🌐 Accessing from Other Computers

Instead of `localhost:8000`, use your computer's IP:

```bash
# Find your IP
ipconfig                    # Windows
ifconfig                    # Mac/Linux

# Then access from other computer:
http://YOUR_IP:8000
```

---

## 📚 Database Schema

### Main Tables:
- **users** - Admin and Sales employees
- **leads** - Customer leads with stages
- **lead_notes** - Notes and follow-ups
- **projects** - Real estate projects
- **buildings** - Buildings in projects
- **units** - Individual property units
- **bookings** - Lead to unit bookings
- **activity_log** - Audit trail

---

## 🔄 Sample Data

Database includes sample:
- 3 users (admin, 2 sales employees)
- 2 projects (Sunset Heights, Tech Park)
- 4 buildings
- 6 units (various types)

Start by creating your own leads and bookings!

---

## 🚀 Production Deployment

See `DEPLOYMENT.md` for:
- Railway.app setup
- Heroku deployment
- Traditional hosting
- Security checklist

---

## 📞 Need Help?

1. **Check the logs:**
   - Browser console (F12 → Console)
   - PHP error log
   - MySQL error log

2. **Verify database:**
   ```bash
   mysql -u root -p real_estate_crm -e "SELECT * FROM users;"
   ```

3. **Test connection:**
   ```bash
   php -r "new mysqli('localhost', 'root', '', 'real_estate_crm') or die('Connection failed');"
   ```

---

## 🎉 You're All Set!

Your Real Estate CRM is ready to use! 

**Happy selling! 🏠💼**

---

### Quick Reference

| Task | Command |
|------|---------|
| Create Database | `mysql -u root -p < schema.sql` |
| Start Server | `php -S localhost:8000` |
| Login | `admin@crm.com / admin123` |
| View Logs | `tail -f /var/log/apache2/error.log` |
| Test Connection | `mysql -u root -p -D real_estate_crm` |
