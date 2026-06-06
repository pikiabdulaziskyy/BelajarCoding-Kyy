# 🗄️ MySQL Setup Guide

## 📋 Persyaratan
- MySQL Server 5.7 atau lebih tinggi
- phpMyAdmin atau MySQL CLI
- PHP dengan MySQLi extension

---

## 🚀 Instalasi Database

### Opsi 1: Menggunakan MySQL CLI

#### Step 1: Buka MySQL Command Line
```bash
# Windows
mysql -u root -p

# Linux/Mac
mysql -u root -p
```

#### Step 2: Import Database Schema
```bash
mysql -u root -p < database.sql
```

Atau:
```sql
SOURCE /path/to/database.sql;
```

#### Step 3: Verifikasi Database
```sql
-- Cek database
SHOW DATABASES;

-- Gunakan database
USE portfolio_kyy;

-- Cek tables
SHOW TABLES;

-- Lihat struktur tabel
DESCRIBE projects;
DESCRIBE contacts;
DESCRIBE messages;
```

---

### Opsi 2: Menggunakan phpMyAdmin

#### Step 1: Buka phpMyAdmin
```
http://localhost/phpmyadmin
```

#### Step 2: Buat Database Baru
- Klik **"New"** di sidebar
- Nama database: `portfolio_kyy`
- Collation: `utf8mb4_unicode_ci`
- Klik **"Create"**

#### Step 3: Import SQL File
- Pilih database `portfolio_kyy`
- Klik tab **"Import"**
- Pilih file `database.sql`
- Klik **"Go"**

#### Step 4: Verifikasi
- Database dan semua tabel sudah dibuat
- Sample data sudah ter-insert

---

### Opsi 3: Menggunakan XAMPP

#### Step 1: Buka XAMPP Control Panel
- Jalankan Apache dan MySQL

#### Step 2: Akses phpMyAdmin
```
http://localhost/phpmyadmin
```

#### Step 3: Ikuti Opsi 2

---

## ⚙️ Konfigurasi Database Connection

### File: `db.php`

Update credentials jika berbeda:

```php
define('DB_HOST', 'localhost');    // Server address
define('DB_USER', 'root');         // MySQL username
define('DB_PASS', '');             // MySQL password
define('DB_NAME', 'portfolio_kyy'); // Database name
define('DB_PORT', 3306);           // MySQL port
```

---

## 🔍 Verifikasi Database

### 1. Test Connection
```bash
php -r "include 'db.php'; echo 'Connected to MySQL!';"
```

### 2. Check Tables
```sql
USE portfolio_kyy;
SHOW TABLES;
```

Output yang diharapkan:
```
+------------------------+
| Tables_in_portfolio_kyy |
+------------------------+
| activity_logs          |
| contacts               |
| messages               |
| projects               |
| settings               |
| stats                  |
| users                  |
+------------------------+
```

### 3. Check Sample Data
```sql
SELECT * FROM projects;
SELECT * FROM settings;
SELECT * FROM stats;
```

---

## 📊 Struktur Tabel

### Projects Table
```sql
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    technologies JSON,
    rating INT DEFAULT 5,
    year INT,
    image_url VARCHAR(255),
    url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Kolom:**
- `id` - Primary key
- `title` - Judul project
- `description` - Deskripsi lengkap
- `technologies` - JSON array teknologi
- `rating` - Rating 1-5
- `year` - Tahun project
- `image_url` - URL gambar project
- `url` - URL live project
- `created_at` - Waktu dibuat
- `updated_at` - Waktu update terakhir

### Contacts Table
```sql
CREATE TABLE contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255) NOT NULL,
    message LONGTEXT NOT NULL,
    ip_address VARCHAR(45),
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    notes LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Kolom:**
- `id` - Primary key
- `name` - Nama kontak
- `email` - Email address
- `phone` - Nomor telepon
- `subject` - Subjek pesan
- `message` - Isi pesan
- `ip_address` - IP address pengirim
- `status` - Status pesan (new/read/replied/closed)
- `notes` - Catatan admin
- `created_at` - Waktu dibuat
- `updated_at` - Waktu update terakhir

### Messages Table
```sql
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT,
    sender VARCHAR(100),
    message_text LONGTEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
);
```

**Kolom:**
- `id` - Primary key
- `contact_id` - Foreign key ke contacts table
- `sender` - Pengirim pesan
- `message_text` - Isi pesan
- `is_read` - Sudah dibaca atau belum
- `created_at` - Waktu pesan dibuat

### Settings Table
```sql
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Stats Table
```sql
CREATE TABLE stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stat_key VARCHAR(100) UNIQUE NOT NULL,
    stat_value INT DEFAULT 0,
    description VARCHAR(255),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 🔐 Security Best Practices

### 1. Folder Database
Jangan expose folder database ke public:
```apache
# .htaccess
<FilesMatch "\.sql$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

### 2. Database User Privileges
Jangan gunakan `root` di production:
```sql
-- Create dedicated user
CREATE USER 'portfolio_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Grant specific privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON portfolio_kyy.* TO 'portfolio_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;
```

### 3. Use Prepared Statements
Semua database queries sudah menggunakan prepared statements di `db.php`:

```php
// AMAN - menggunakan prepared statement
$db->query("SELECT * FROM contacts WHERE email = ?", [$email], 's');

// TIDAK AMAN - string concatenation
$query = "SELECT * FROM contacts WHERE email = '$email'"; // SQL Injection!
```

### 4. Encrypt Passwords
```php
// Hashing password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Verify password
if (password_verify($input_password, $password_hash)) {
    // Password correct
}
```

---

## 📈 Query Examples

### Insert New Project
```php
$db->insert('projects', [
    'title' => 'New Project',
    'description' => 'Project description here',
    'technologies' => json_encode(['PHP', 'MySQL']),
    'rating' => 5,
    'year' => 2026,
    'url' => 'https://example.com'
]);

$project_id = $db->lastId();
```

### Get All Projects
```php
$projects = $db->fetchAll("SELECT * FROM projects ORDER BY year DESC");
```

### Get Single Contact
```php
$contact = $db->fetchOne(
    "SELECT * FROM contacts WHERE id = ?",
    [$contact_id],
    'i'
);
```

### Update Contact Status
```php
$db->update('contacts', 
    ['status' => 'replied', 'notes' => 'Replied via email'],
    "id = ?",
    [$contact_id]
);
```

### Delete Contact
```php
$db->delete('contacts', 'id = ?', [$contact_id]);
```

### Count Total Contacts
```php
$total = $db->count('contacts');

// With condition
$new_contacts = $db->count('contacts', "status = ?", ['new']);
```

---

## 🧪 Testing Database Connection

### Test via CLI
```bash
# Connect to MySQL
mysql -u root -p portfolio_kyy

# List tables
SHOW TABLES;

# Check data
SELECT COUNT(*) FROM projects;
SELECT COUNT(*) FROM contacts;
```

### Test via PHP Script
Create file `test_db.php`:

```php
<?php
include 'db.php';

echo "Testing database connection...\n";

// Test 1: Get projects
$projects = $db->fetchAll("SELECT * FROM projects");
echo "✓ Projects: " . count($projects) . "\n";

// Test 2: Get contacts
$contacts = $db->fetchAll("SELECT * FROM contacts");
echo "✓ Contacts: " . count($contacts) . "\n";

// Test 3: Insert test contact
$id = $db->insert('contacts', [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'phone' => '08123456789',
    'subject' => 'Test',
    'message' => 'This is a test message'
]);
echo "✓ Inserted contact ID: " . $id . "\n";

// Test 4: Update
$db->update('contacts', 
    ['status' => 'read'],
    "id = ?",
    [$id]
);
echo "✓ Updated contact\n";

// Test 5: Delete
$db->delete('contacts', 'id = ?', [$id]);
echo "✓ Deleted contact\n";

echo "\n✅ All database tests passed!\n";
?>
```

Run:
```bash
php test_db.php
```

---

## 🆘 Troubleshooting

### Error: "Access denied for user 'root'@'localhost'"
```bash
# Check if MySQL is running
# Windows: Services -> MySQL
# Linux: sudo systemctl status mysql

# Reset MySQL password (Windows)
C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld --skip-grant-tables
```

### Error: "Unknown database 'portfolio_kyy'"
```sql
-- Create database manually
CREATE DATABASE portfolio_kyy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Then import schema
SOURCE database.sql;
```

### Error: "Table 'contacts' doesn't exist"
- Run `database.sql` untuk create semua tables
- Verify dengan: `SHOW TABLES;`

### Error: "Can't create table" (Permission denied)
```bash
# Windows: Run as Administrator
# Linux/Mac: Check folder permissions
chmod -R 755 /var/lib/mysql/
```

### Slow Queries
Check indexes:
```sql
-- View indexes
SHOW INDEX FROM contacts;
SHOW INDEX FROM projects;

-- Analyze query performance
EXPLAIN SELECT * FROM contacts WHERE status = 'new';
```

---

## 📚 MySQL Commands Reference

```sql
-- Database
CREATE DATABASE database_name;
DROP DATABASE database_name;
USE database_name;
SHOW DATABASES;

-- Table
CREATE TABLE table_name (id INT PRIMARY KEY);
DROP TABLE table_name;
SHOW TABLES;
DESCRIBE table_name;
ALTER TABLE table_name ADD COLUMN new_col VARCHAR(100);

-- Data
INSERT INTO table_name (col1, col2) VALUES ('val1', 'val2');
SELECT * FROM table_name;
UPDATE table_name SET col1 = 'new_value' WHERE id = 1;
DELETE FROM table_name WHERE id = 1;

-- Utilities
SHOW CHARACTER SET;
SET CHARACTER SET utf8mb4;
SHOW COLLATION;
```

---

## 📖 Links & Resources

- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MySQLi Manual](https://www.php.net/manual/en/book.mysqli.php)
- [SQL Cheat Sheet](https://www.cheatsheetworld.com/cheatsheet/sql-mysql-cheatsheet/)
- [Database Design Best Practices](https://en.wikipedia.org/wiki/Database_normalization)

---

**Last Updated**: June 7, 2026
**Version**: 1.0.0
**Database Version**: MySQL 5.7+
