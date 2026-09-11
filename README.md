# Real Estate CRM - Full-Stack Application

A professional Real Estate CRM system built with **Core PHP & MySQL** for managing leads, properties, and bookings for sales teams.

## 🎯 Project Overview

This is a **complete full-stack Real Estate CRM** designed for sales teams to:
- Manage leads across multiple sales pipeline stages
- Track property projects, buildings, and available units
- Create property bookings with **duplicate prevention**
- Assign leads to sales employees with follow-up tracking
- View real-time sales dashboard with key metrics

**Tech Stack:** PHP 7.4+ | MySQL 5.7+ | Tailwind CSS 3 | Vanilla JavaScript

---

## 🏗️ Architecture & Key Design Decisions

### 1. **MVC Architecture with Service Layer**
- **Models:** Database layer with prepared statements for security
- **Views:** Separate HTML files for each page (dashboard, leads, properties, bookings)
- **Controllers:** Logic embedded in view files for simplicity (MVC-lite pattern)
- **Singleton Database:** Single connection instance prevents resource leaks

**Why?** Core PHP doesn't require heavy framework overhead. This keeps the codebase lightweight while maintaining clean separation of concerns.

### 2. **Transaction-Based Booking System (Critical)**
```php
// Prevents race conditions & duplicate bookings
BEGIN TRANSACTION
  → Check if unit already booked
  → Check if lead has confirmed booking elsewhere  
  → Create booking record
  → Update unit status to 'Booked'
  → Update lead stage to 'Booked'
COMMIT or ROLLBACK
```

**Why?** In real estate, two customers must NEVER book the same unit. Transactions ensure atomic operations - all succeed or all fail, preventing data inconsistency even under concurrent access.

### 3. **Role-Based Access Control (Admin & Sales Employee)**
- Admin: Full access to users, properties, and system settings
- Sales Employee: Can only access assigned leads and create bookings
- Session-based auth with password hashing (bcrypt)

**Why?** Different roles have different responsibilities. This prevents sales staff from deleting properties or accessing admin functions while allowing them full lead management.

### 4. **Lead Notes with Follow-up Dates**
- Separate `lead_notes` table linked to leads
- Dashboard shows "Pending Follow-ups" (due within 7 days)
- Useful for reminding sales team about next actions

**Why?** Sales teams need history and reminders. This prevents leads from falling through cracks and provides accountability.

### 5. **Prepared Statements & Input Validation**
- All DB queries use parameterized statements
- Input sanitization for XSS prevention
- Email and phone validation

**Why?** SQL injection is the #1 web vulnerability. Prepared statements make it impossible.

---

## 📂 Project Structure

```
real-estate-crm/
├── config/
│   ├── constants.php          # App constants & definitions
│   └── database.php           # DB connection (Singleton)
├── models/
│   ├── User.php              # User authentication & management
│   ├── Lead.php              # Lead CRUD & pipeline management
│   ├── Property.php          # Projects, buildings, units
│   └── Booking.php           # Booking logic with duplicate prevention
├── views/
│   ├── layout/
│   │   └── main.php          # Main layout template
│   ├── auth/
│   │   └── auth.php          # Login page
│   ├── dashboard/
│   │   └── dashboard.php     # Dashboard with KPIs
│   ├── leads/
│   │   ├── leads.php         # Lead controller
│   │   ├── list.php          # Lead list view
│   │   ├── create.php        # Create lead form
│   │   ├── view.php          # Lead detail + notes
│   │   └── edit.php          # Edit lead form
│   ├── properties/
│   │   └── properties.php    # Property management
│   └── bookings/
│       └── bookings.php      # Booking management
├── includes/
│   └── helpers.php           # Utility functions
├── public/
│   └── index.php             # Main router/entry point
├── sql/
│   └── schema.sql            # Database schema
└── README.md
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer (optional, for vendor autoload)

### 1. Clone & Setup

```bash
git clone <repository-url>
cd real-estate-crm
```

### 2. Create Database

```bash
mysql -u root -p < sql/schema.sql
```

This creates:
- Database: `real_estate_crm`
- Sample users, projects, buildings, and units

### 3. Configure Database (if needed)

Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'real_estate_crm');
```

### 4. Start Local Server

```bash
cd public
php -S localhost:8000
```

Open: `http://localhost:8000`

### 5. Login

**Admin Account:**
- Email: `admin@crm.com`
- Password: `admin123`

**Sales Account:**
- Email: `john@crm.com`
- Password: `admin123`

---

## 📋 Features Implemented

### ✅ Lead Management
- Create, edit, view, and delete leads
- 7 lead stages: New → Contacted → Site Visit → Interested → Negotiation → Booked → Lost
- Assign leads to sales employees
- Add notes and follow-up dates
- Search and filter by stage, assignee, or contact info
- Dashboard showing leads by stage

### ✅ Property Management
- Projects (e.g., "Sunset Heights")
- Buildings within projects (e.g., "Tower A", "Tower B")
- Units with type (1BHK, 2BHK, 3BHK, Commercial)
- Unit pricing and availability status
- Search and filter properties

### ✅ Booking System (with Duplicate Prevention)
- Link leads to property units
- Create pending bookings
- Confirm bookings (locks unit as "Booked")
- Cancel bookings (releases unit)
- View booking history per lead
- Prevent two users from booking same unit (DB constraint + transaction logic)

### ✅ Dashboard
- Total leads count
- Confirmed bookings & revenue
- Available units count
- Pending follow-ups (due in 7 days)
- Sales pipeline visualization (leads by stage)
- Recent bookings list

### ✅ Authentication
- Admin & Sales Employee roles
- Session-based login/logout
- Password hashing (bcrypt)
- Protected routes (redirect to login if not authenticated)

### ✅ UI/UX
- Clean, professional design with Tailwind CSS
- Responsive layout (sidebar + main content)
- Forms with validation
- Status badges with color coding
- Pagination for tables
- Flash messages for user feedback

---

## 🔐 Security Features

1. **SQL Injection Prevention:** Prepared statements on all queries
2. **XSS Prevention:** HTML escaping with `esc()` helper
3. **Password Security:** bcrypt hashing (10 rounds)
4. **Session Management:** Session timeout support
5. **Database Constraints:** UNIQUE keys for preventing duplicate bookings
6. **Role-Based Access:** Admin-only features protected

---

## 📊 Database Schema Highlights

### Key Tables
- **users:** Admin & sales employees with roles
- **leads:** Lead records with stages and assignment
- **lead_notes:** Follow-up notes tied to leads
- **projects:** Real estate projects/locations
- **buildings:** Buildings within projects
- **units:** Individual property units
- **bookings:** Lead-to-unit bookings with status
- **activity_log:** Audit trail (for future logging)

### Important Constraints
```sql
-- Prevent duplicate confirmed bookings per unit
UNIQUE KEY unique_confirmed_booking (unit_id, booking_status)

-- Cascade deletes for data consistency
FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE RESTRICT
```

---

## 🎨 UI/UX Decisions

1. **Sidebar Navigation:** Always visible, easy access to main sections
2. **Color-Coded Badges:** Lead stages & booking statuses at a glance
3. **Pagination:** Handles large lead lists efficiently
4. **Empty States:** Clear messaging when no data
5. **Breadcrumb-style links:** Easy navigation back
6. **Mobile-Responsive:** Tailwind responsive grid system

---

## 🚢 Deployment Guide

### Option 1: Deploy to Railway.app (Recommended)

```bash
# 1. Create Railway project
# 2. Connect GitHub repo
# 3. Set environment variables:
#    DB_HOST, DB_USER, DB_PASS, DB_NAME, APP_URL

# 4. Configure buildpack:
#    - Runtime: PHP 7.4+
#    - Web command: php -S 0.0.0.0:$PORT public/

# 5. Deploy!
```

### Option 2: Deploy to Heroku

```bash
# 1. Create Procfile
echo "web: php -S 0.0.0.0:\$PORT public/" > Procfile

# 2. Push to Heroku
git push heroku main

# 3. Run migrations
heroku run mysql < sql/schema.sql
```

### Option 3: Traditional Shared Hosting

1. Upload files via FTP
2. Create MySQL database
3. Import `sql/schema.sql`
4. Update `config/database.php` with hosting credentials
5. Set document root to `public/` folder

---

## 📈 Future Enhancements

1. **SMS/Email Notifications:** Auto-notify on follow-ups
2. **Analytics:** Revenue by project, conversion funnel
3. **Payment Integration:** Razorpay/Stripe for online bookings
4. **Document Upload:** Attach property brochures to units
5. **Bulk Import:** CSV upload for leads/properties
6. **API Layer:** RESTful API for mobile app
7. **Real-time Notifications:** WebSocket for live updates

---

## 🐛 Troubleshooting

### "Connection failed" error
- Check MySQL is running: `mysql -u root -p`
- Verify credentials in `config/database.php`

### "Session lost" on page refresh
- Ensure `session.save_path` is writable: `chmod 777 /tmp`

### "Cannot create booking" for available unit
- Check unit status in database: `SELECT * FROM units WHERE id = ?`
- Verify no "Confirmed" booking exists for that unit

### 404 errors on links
- Ensure `public/` is the document root
- Check `.htaccess` for URL rewriting (if needed)

---

## 📝 Testing Scenarios

### Scenario 1: Create & Manage Lead
1. Login as sales employee
2. Create new lead: "Rajesh Kumar"
3. Assign to yourself
4. Add note: "Interested in 2BHK"
5. Move stage: New → Contacted → Interested

### Scenario 2: Book a Unit
1. View lead detail
2. Click "Book Unit"
3. Select available unit: "A-101"
4. Confirm booking
5. Verify unit status changes to "Booked"
6. Try booking same unit again (should fail)

### Scenario 3: Admin Functions
1. Login as admin
2. View all users & leads
3. Create new sales employee
4. Reassign leads to different employees

---

## 📞 Support

For issues or questions, check:
- Database errors in browser console
- MySQL error log: `mysql.log`
- PHP error log: `error_log`

---

**Built with ❤️ for real estate teams**  
Version 1.0 | Last Updated: 2024
