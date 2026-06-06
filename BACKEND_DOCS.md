# 📚 Backend PHP Documentation

## Overview
Backend PHP menyediakan REST API untuk aplikasi portofolio, termasuk manajemen project, contact forms, dan data statistik.

---

## 📂 File Structure

### `api.php` - REST API Hub
File utama yang menangani semua API endpoints.

**Fitur:**
- Project management (GET)
- Message storage (POST)
- Statistics endpoint
- Server health check (ping)

**Classes:**
```php
class Database
├── getProjects()
├── getProjectById($id)
├── saveMessage($data)
├── getMessages()
├── sanitize($data)
└── validateEmail($email)

class ApiResponse
├── success($data, $message, $code)
└── error($message, $code, $errors)
```

**CORS Headers:**
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
```

---

### `contact.php` - Contact Form Handler
Dedicated file untuk handling contact form submissions.

**Features:**
- Form validation
- Email sanitization
- JSON data storage
- Error response dengan detail

**Validation Rules:**
```php
- name: Required, not empty
- email: Required, valid email format
- phone: Required, valid phone format
- subject: Required, not empty
- message: Required, min 10 characters
```

**Response Format:**
```json
{
  "success": true/false,
  "message": "Status message",
  "data": { contact object or errors },
  "timestamp": "2026-06-07 10:30:00"
}
```

---

### `config.php` - Configuration
Centralized configuration file untuk aplikasi.

**Konfigurasi Utama:**
```php
ENVIRONMENT           // development atau production
DEBUG_MODE           // true/false
APP_NAME             // Nama aplikasi
APP_VERSION          // Versi aplikasi
ADMIN_EMAIL          // Email admin
CONTACT_EMAIL_ENABLED // Enable email sending
DATA_DIR             // Data storage directory
LOGS_DIR             // Logs directory
UPLOADS_DIR          // Upload directory
```

**Helper Functions:**
```php
dd($var)              // Dump dan die
log_message($msg)     // Log message
response_json(...)    // JSON response
sanitize($input)      // Sanitize input
validate_email(...)   // Email validation
get_client_ip()       // Get client IP
```

---

### `functions.php` - Utility Functions
Collection of helper functions untuk berbagai kebutuhan.

#### String Functions
```php
str_slug($string)           // Convert to slug
truncate($text, $limit)     // Truncate text
capitalize($text)           // Capitalize text
```

#### Date Functions
```php
format_date($date, $format) // Format date
time_ago($date)             // Time ago format
```

#### Array Functions
```php
array_by_key(&$array, $key) // Group by key
array_contains($array, $val) // Check contains
```

#### File Functions
```php
file_read($path)            // Read file
file_write($path, $content) // Write file
file_append($path, $content) // Append to file
file_delete($path)          // Delete file
dir_create($path)           // Create directory
```

#### JSON Functions
```php
json_read($file)            // Read JSON file
json_write($file, $data)    // Write JSON file
json_append($file, $item)   // Append to JSON
```

#### Validation Functions
```php
validate_required($value)   // Check not empty
validate_min_length(...)    // Min length check
validate_max_length(...)    // Max length check
validate_phone($phone)      // Phone validation
validate_url($url)          // URL validation
```

#### Security Functions
```php
encrypt($data)              // Encrypt data (base64)
decrypt($data)              // Decrypt data
generate_token($length)     // Generate random token
verify_csrf_token($token)   // CSRF verification
```

#### Cache Functions
```php
SimpleCache::set($key, $val, $ttl)  // Set cache
SimpleCache::get($key)              // Get cache
SimpleCache::forget($key)           // Delete cache
SimpleCache::flush()                // Clear all cache
```

---

## 🔄 API Workflow

### 1. Request Flow
```
Browser Request
    ↓
CORS Headers Check
    ↓
Action Router
    ↓
Database/Handler
    ↓
Validation
    ↓
Response Generation
    ↓
JSON Response
```

### 2. Data Flow - Send Message
```
Frontend Form
    ↓
JavaScript FormHandler
    ↓
PHPApiClient.post()
    ↓
contact.php
    ↓
Validation
    ↓
Sanitization
    ↓
JSON Storage
    ↓
Response to Frontend
```

### 3. Data Flow - Get Projects
```
PHPApiClient.get()
    ↓
api.php?action=get-projects
    ↓
Database->getProjects()
    ↓
JSON Encode
    ↓
ApiResponse::success()
    ↓
Response to Frontend
```

---

## 💾 Data Storage

### Messages (messages.json)
```json
[
  {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Konsultasi Website",
    "message": "Saya ingin membuat website...",
    "timestamp": "2026-06-07 10:30:00",
    "status": "new"
  }
]
```

### Contacts (contacts.json)
```json
[
  {
    "id": "unique-id",
    "name": "Jane Smith",
    "email": "jane@example.com",
    "phone": "08123456789",
    "subject": "Project Proposal",
    "message": "Proposal untuk project baru...",
    "timestamp": "2026-06-07 11:00:00",
    "ip_address": "192.168.1.1",
    "status": "new"
  }
]
```

---

## ✅ Error Handling

### Standard Error Response
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": "Format email tidak valid",
    "phone": "Format telepon tidak valid"
  },
  "timestamp": "2026-06-07 10:30:00"
}
```

### HTTP Status Codes
```
200 OK                  - Request successful
201 Created             - Resource created
400 Bad Request         - Invalid request
404 Not Found           - Resource not found
405 Method Not Allowed  - Wrong HTTP method
422 Unprocessable       - Validation failed
500 Server Error        - Internal error
```

---

## 🔐 Security Measures

### Input Sanitization
```php
$clean = sanitize($input);
// Uses: htmlspecialchars, stripslashes, trim
```

### Email Validation
```php
if (!validate_email($email)) {
    // Invalid email
}
```

### Phone Validation
```php
// Format: +62 atau 0, minimum 10 digit
if (!validate_phone($phone)) {
    // Invalid phone
}
```

### CORS Protection
```php
header('Access-Control-Allow-Origin: *');
// Bisa direstrict ke domain tertentu
// header('Access-Control-Allow-Origin: https://example.com');
```

---

## 🚀 Performance Tips

### 1. Caching
```php
$projects = SimpleCache::get('projects');
if (!$projects) {
    $projects = $db->getProjects();
    SimpleCache::set('projects', $projects, 3600); // 1 hour
}
```

### 2. Lazy Loading
```php
// Get only necessary fields
$projects = array_map(function($p) {
    return [
        'id' => $p['id'],
        'title' => $p['title']
    ];
}, $projects);
```

### 3. Pagination
```php
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$result = paginate($items, $page, 10);
// Returns: items, total, pages, etc
```

---

## 🧪 Testing

### cURL Test Examples

#### GET Projects
```bash
curl "http://localhost:8000/api.php?action=get-projects"
```

#### POST Message
```bash
curl -X POST http://localhost:8000/contact.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "email": "test@example.com",
    "phone": "08123456789",
    "subject": "Test",
    "message": "Test message content here"
  }'
```

#### GET Stats
```bash
curl "http://localhost:8000/api.php?action=stats"
```

#### Ping Server
```bash
curl "http://localhost:8000/api.php?action=ping"
```

---

## 📋 Checklist Setup

- [ ] PHP 7.0+ installed
- [ ] Folder `data/` writable (chmod 755)
- [ ] Folder `logs/` writable (chmod 755)
- [ ] Server running (PHP built-in atau XAMPP)
- [ ] Test API endpoints dengan cURL
- [ ] Test form submission dari browser
- [ ] Check browser console untuk errors
- [ ] Verify JSON files created

---

## 🔧 Troubleshooting

### "Cannot write to data directory"
```bash
# Fix permissions
chmod -R 755 data/
chmod -R 755 logs/
```

### "JSON error: Syntax error"
```php
// Check JSON encoding
json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
```

### "CORS error in browser"
- Check CORS headers di api.php
- Verify request headers di browser Network tab
- Check server logs untuk errors

### "Email not sending"
```php
// Set ke true di config.php
define('CONTACT_EMAIL_ENABLED', true);
// Pastikan PHP mail() configured
```

---

## 📖 References

- [PHP Documentation](https://www.php.net/docs.php)
- [JSON Functions](https://www.php.net/manual/en/ref.json.php)
- [Filter Functions](https://www.php.net/manual/en/ref.filter.php)
- [File System Functions](https://www.php.net/manual/en/ref.filesystem.php)

---

**Last Updated**: June 7, 2026
**Version**: 1.0.0
**Author**: Piki Abdul Azis
