# 🚀 Portofolio Kyy v2.0 - Setup Guide

**Complete setup guide untuk menjalankan website portfolio kompleks**

---

## 📋 Prerequisites

Pastikan Anda memiliki:
- **PHP 7.4 atau lebih tinggi**
- **MySQL 5.7 atau MariaDB 10.2+**
- **Web server** (Apache dengan mod_rewrite / Nginx)
- **Text editor** (VS Code, Sublime, etc)
- **Command line access** (Terminal/PowerShell)

---

## 🔧 Installation Steps

### Step 1: Download & Extract

```bash
# Download project
git clone https://github.com/yourusername/portofolio-kyy.git

# Atau extract dari ZIP file ke folder
cd BelajarCoding-Kyy
```

### Step 2: Create MySQL Database

**Opsi A: Menggunakan Command Line**

```bash
# Login ke MySQL
mysql -u root -p

# Copy-paste database.sql isi ke terminal, atau:
mysql -u root -p < database.sql

# Or import file di MySQL client
SOURCE /path/to/database.sql;
```

**Opsi B: Menggunakan PhpMyAdmin**

1. Buka `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Select `database.sql` file
4. Click "Go"

### Step 3: Configure Database Connection

Edit file `config.php`:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');      // Usually 'localhost'
define('DB_USER', 'root');            // Your MySQL username
define('DB_PASS', 'password');        // Your MySQL password
define('DB_NAME', 'portfolio_kyy');   // Database name

// Application Settings
define('APP_NAME', 'Portofolio Kyy');
define('APP_URL', 'http://localhost/BelajarCoding-Kyy');
define('APP_ENV', 'production');      // or 'development'
?>
```

### Step 4: Upload Files to Web Server

```bash
# Copy all files to web server root
cp -r BelajarCoding-Kyy /var/www/html/

# Or if using XAMPP
cp -r BelajarCoding-Kyy /xampp/htdocs/
```

**File Permissions** (untuk production):
```bash
chmod 755 BelajarCoding-Kyy/
chmod 644 BelajarCoding-Kyy/*.php
chmod 755 BelajarCoding-Kyy/admin/
mkdir -p BelajarCoding-Kyy/uploads
chmod 777 BelajarCoding-Kyy/uploads/
```

### Step 5: Create Admin Account

**Opsi A: Menggunakan SQL**

```sql
-- Hash password 'admin123' dengan bcrypt
-- Use: password_hash('admin123', PASSWORD_BCRYPT)
-- Result: $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/eJa

INSERT INTO users (username, email, password, full_name, role, is_active) 
VALUES ('admin', 'admin@example.com', '$2y$10$...hashedpassword...', 'Administrator', 'admin', 1);
```

**Opsi B: Menggunakan PHP Script**

Buat file `create-admin.php`:

```php
<?php
require_once 'config.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    
    $username = 'admin';
    $email = 'admin@example.com';
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $full_name = 'Administrator';
    
    $stmt = $db->conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'admin')");
    $stmt->execute([$username, $email, $password, $full_name]);
    
    echo "✅ Admin user created successfully!";
    unlink(__FILE__); // Delete this file after creating admin
}
?>
<form method="POST">
    <button type="submit">Create Admin Account</button>
</form>
```

Akses `http://localhost/BelajarCoding-Kyy/create-admin.php` di browser.

### Step 6: Load Sample Data (Optional)

```bash
# Via command line
php seed-data.php

# Or visit the script in browser
http://localhost/BelajarCoding-Kyy/seed-data.php
```

### Step 7: Test Installation

1. **Homepage**: `http://localhost/BelajarCoding-Kyy/`
2. **Login**: `http://localhost/BelajarCoding-Kyy/login.php`
3. **Admin Panel**: `http://localhost/BelajarCoding-Kyy/admin/`

**Default Credentials (from seed-data):**
- Email: `admin@example.com`
- Password: `admin123`

---

## 🎯 Configuration Files

### `config.php`
```php
// Database
DB_HOST, DB_USER, DB_PASS, DB_NAME

// Application
APP_NAME, APP_URL, APP_ENV, APP_DEBUG

// Security
CSRF_TOKEN_ENABLED, SESSION_TIMEOUT
```

### `functions.php`
Helper functions untuk:
- Email sending
- File upload handling
- String utilities
- Date formatting

### `.env` (Optional)

Untuk production, gunakan file `.env`:

```env
DB_HOST=localhost
DB_USER=portfolio_user
DB_PASS=strong_password_here
DB_NAME=portfolio_kyy

APP_ENV=production
APP_DEBUG=false
```

Load `.env`:
```php
$_ENV = parse_ini_file('.env');
```

---

## 🔐 Security Configuration

### 1. Secure Passwords

Edit `config.php`:
```php
define('SESSION_TIMEOUT', 3600);      // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('CSRF_TOKEN_ENABLED', true);
```

### 2. Database User

Buat dedicated database user:

```sql
CREATE USER 'portfolio_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON portfolio_kyy.* TO 'portfolio_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. File Permissions

```bash
# Production settings
chmod 700 BelajarCoding-Kyy/
chmod 644 config.php
chmod 600 config.php   # More restrictive

# No write access to core files
chmod 444 *.php
chmod 755 admin/
```

### 4. HTTPS (Important for Production)

Redirect HTTP to HTTPS di `.htaccess`:

```apache
<IfModule mod_ssl.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### 5. Disable Directory Listing

Tambah ke `.htaccess`:

```apache
Options -Indexes
```

---

## 🗄️ Database Verification

Verifikasi database setup:

```sql
-- Check tables created
SHOW TABLES;

-- Check users table
DESC users;

-- Verify admin user exists
SELECT * FROM users WHERE role='admin';

-- Check all indexes
SHOW INDEX FROM blog_posts;
SHOW INDEX FROM contacts;
```

---

## 🚨 Troubleshooting

### Issue 1: "Cannot connect to database"

**Solution:**
```php
// Check connection
try {
    $db = new Database();
    echo "✅ Connected successfully";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

### Issue 2: "Class Database not found"

**Solution:**
```php
// Pastikan db.php di-include di config.php
require_once 'db.php';
```

### Issue 3: "Admin login failed"

**Solution:**
```sql
-- Verify admin account exists
SELECT username, email, role FROM users WHERE role='admin';

-- Reset password
UPDATE users SET password=PASSWORD('newpassword') WHERE username='admin';
```

### Issue 4: "Search not working"

**Solution:**
```sql
-- Check FULLTEXT indexes
SHOW INDEX FROM blog_posts;
SHOW INDEX FROM contacts;

-- Recreate if missing
ALTER TABLE blog_posts ADD FULLTEXT INDEX ft_title_content (title, content);
ALTER TABLE contacts ADD FULLTEXT INDEX ft_message (message, subject);
```

### Issue 5: "Upload not working"

**Solution:**
```bash
# Create uploads folder
mkdir -p uploads/
chmod 777 uploads/

# Check PHP upload settings
php -r "phpinfo();" | grep -i upload
```

---

## 📊 Database Tables Overview

| Table | Purpose | Records |
|-------|---------|---------|
| users | User accounts & auth | 1+ |
| projects | Portfolio projects | 3+ (sample) |
| skills | Skills & expertise | 6+ (sample) |
| experiences | Work history | 3+ (sample) |
| blog_posts | Blog articles | 3+ (sample) |
| blog_categories | Blog categories | 4 (sample) |
| blog_comments | Post comments | 0 (empty) |
| testimonials | Client reviews | 3+ (sample) |
| newsletter_subscribers | Email list | 0 (empty) |
| contacts | Contact messages | 0 (empty) |

---

## 🧪 Testing Checklist

- [ ] Database connected
- [ ] Admin account created
- [ ] Homepage loads
- [ ] Login page works
- [ ] Admin panel accessible
- [ ] Projects display
- [ ] Blog posts show
- [ ] Search functionality
- [ ] Filters work
- [ ] Contact form submits
- [ ] Newsletter signup works
- [ ] Mobile responsive

---

## 🔄 Regular Maintenance

### Daily
- Monitor error logs
- Check database size

### Weekly
- Review contact messages
- Approve comments
- Check server resources

### Monthly
- Database backup
- Update blog content
- Review analytics

### Yearly
- Security audit
- PHP update check
- SSL certificate renewal

---

## 📦 Backup & Restore

### Backup Database

```bash
# Backup to file
mysqldump -u root -p portfolio_kyy > backup.sql

# Backup dengan timestamp
mysqldump -u root -p portfolio_kyy > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database

```bash
# Restore from backup
mysql -u root -p portfolio_kyy < backup.sql
```

### Full Project Backup

```bash
# Backup everything
tar -czf portfolio-backup-$(date +%Y%m%d).tar.gz BelajarCoding-Kyy/
```

---

## 🚀 Deployment to Production

### Checklist
- [ ] Update config.php with production values
- [ ] Set APP_ENV='production'
- [ ] Disable APP_DEBUG
- [ ] Configure proper file permissions
- [ ] Setup SSL/HTTPS
- [ ] Configure backups
- [ ] Setup monitoring
- [ ] Test all features

### Example Production Config
```php
define('DB_HOST', 'db.example.com');
define('DB_USER', 'prod_user');
define('DB_PASS', 'secure_password');
define('DB_NAME', 'portfolio_kyy');

define('APP_ENV', 'production');
define('APP_DEBUG', false);
define('HTTPS_ONLY', true);
define('CSRF_TOKEN_ENABLED', true);
```

---

## 📞 Getting Help

1. Check [FEATURES.md](FEATURES.md) for feature details
2. Review [BACKEND_DOCS.md](BACKEND_DOCS.md) for API docs
3. Check error logs: `/logs/error.log`
4. Contact support or open GitHub issue

---

**Setup Complete!** 🎉  
Now your portfolio is ready to use!

