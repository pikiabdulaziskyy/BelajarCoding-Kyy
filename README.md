# 🎨 Portofolio Kyy - Full Stack Web Application

**Modern portfolio website dengan frontend HTML/CSS/JavaScript dan backend PHP**

## 📋 Daftar Isi
- [Fitur](#fitur)
- [Struktur Proyek](#struktur-proyek)
- [Setup & Instalasi](#setup--instalasi)
- [Dokumentasi API](#dokumentasi-api)
- [Teknologi](#teknologi)

---

## ✨ Fitur

### Frontend (HTML/CSS/JavaScript)
- ✅ **Responsive Design** - Mobile, tablet, dan desktop
- ✅ **Dark Mode Toggle** - Tema terang/gelap dengan local storage
- ✅ **Advanced Animations** - 16+ jenis animasi smooth
- ✅ **Particle Background** - Animasi partikel interaktif
- ✅ **Smooth Scrolling** - Navigasi halus antar section
- ✅ **Form Validation** - Validasi form real-time
- ✅ **Toast Notifications** - Notifikasi user feedback
- ✅ **Scroll Progress Bar** - Progress indicator
- ✅ **Modal System** - Detail modal untuk project
- ✅ **Keyboard Shortcuts** - Shortcut keyboard (Ctrl+K, Ctrl+/)

### Backend (PHP)
- ✅ **REST API** - Endpoint untuk data projects dan stats
- ✅ **Form Handler** - Proses submit form kontak
- ✅ **Data Validation** - Validasi email, telepon, dll
- ✅ **JSON Storage** - Penyimpanan data sederhana (simulasi DB)
- ✅ **CORS Support** - Cross-origin resource sharing
- ✅ **Error Handling** - Response error terstruktur

---

## 📁 Struktur Proyek

```
BelajarCoding-Kyy/
├── index.html              # File HTML utama
├── style.css               # Stylesheet (desktop + mobile)
├── script.js               # JavaScript (16+ kelas, animations)
│
├── api.php                 # REST API endpoints
├── contact.php             # Contact form handler
├── config.php              # Konfigurasi aplikasi
├── functions.php           # Helper functions
│
├── data/                   # Folder data storage (auto-created)
│   ├── messages.json       # Stored messages
│   └── contacts.json       # Stored contacts
│
├── logs/                   # Folder logs (auto-created)
└── README.md              # File ini

```

---

## 🚀 Setup & Instalasi

### Persyaratan
- PHP 7.0 atau lebih tinggi
- Web server (Apache, Nginx, atau PHP Built-in Server)
- Browser modern (Chrome, Firefox, Safari, Edge)

### Instalasi Lokal

#### Opsi 1: PHP Built-in Server (Recommended untuk testing)
```bash
cd c:\Users\Zoneter\Desktop\BelajarCoding-Kyy
php -S localhost:8000
```
Buka browser: `http://localhost:8000`

#### Opsi 2: XAMPP/WAMP
1. Copy folder ke `htdocs` (XAMPP) atau `www` (WAMP)
2. Jalankan Apache
3. Akses: `http://localhost/BelajarCoding-Kyy`

#### Opsi 3: Nginx
```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/BelajarCoding-Kyy;
    
    index index.html;
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📡 Dokumentasi API

### Base URL
```
http://localhost:8000/api.php
http://localhost:8000/contact.php
```

### Endpoints

#### 1. GET Projects
```
GET /api.php?action=get-projects

Response:
{
  "success": true,
  "message": "Projects retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Website Portofolio",
      "description": "...",
      "technologies": ["HTML", "CSS", "JavaScript"],
      "rating": 5,
      "year": 2026
    },
    ...
  ],
  "timestamp": "2026-06-07 10:30:00"
}
```

#### 2. GET Single Project
```
GET /api.php?action=get-project&id=1

Response: Single project object
```

#### 3. POST Contact Message
```
POST /contact.php

Body (JSON):
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "08123456789",
  "subject": "Konsultasi Website",
  "message": "Saya ingin membuat website untuk bisnis saya..."
}

Response:
{
  "success": true,
  "message": "Pesan berhasil dikirim! Kami akan segera merespon.",
  "data": {
    "id": "xyz123",
    "name": "John Doe",
    "email": "john@example.com",
    "timestamp": "2026-06-07 10:30:00",
    "status": "new"
  }
}
```

#### 4. GET Statistics
```
GET /api.php?action=stats

Response:
{
  "success": true,
  "data": {
    "total_projects": 3,
    "total_messages": 5,
    "average_rating": 4.7,
    "years_active": 2,
    "languages": ["HTML", "CSS", "JavaScript", "PHP", "MySQL"]
  }
}
```

#### 5. Ping Server
```
GET /api.php?action=ping

Response:
{
  "success": true,
  "message": "Server is running",
  "data": { "status": "ok" }
}
```

---

## 🛠️ Teknologi

### Frontend
| Teknologi | Versi | Penggunaan |
|-----------|-------|-----------|
| HTML5 | - | Struktur halaman |
| CSS3 | - | Styling & animations |
| JavaScript (Vanilla) | ES6+ | Interactivity & logic |
| Canvas API | - | Particle animation |
| Intersection Observer | - | Lazy animation |
| LocalStorage | - | Theme persistence |

### Backend
| Teknologi | Versi | Penggunaan |
|-----------|-------|-----------|
| PHP | 7.0+ | Server-side logic |
| JSON | - | Data storage |
| CORS | - | Cross-origin requests |

---

## 📊 JavaScript Classes

### Core Classes
- **PHPApiClient** - HTTP client untuk PHP API
- **FormHandler** - Form validation dan submission
- **ParticleAnimation** - Canvas-based particle effect
- **ThemeManager** - Dark/light mode management

### UI Classes
- **ProjectModal** - Modal popup untuk project detail
- **Notification** - Toast notifications
- **ScrollProgressBar** - Progress indicator
- **ConfettiAnimation** - Confetti celebration effect

### Animation Classes
- **FloatingAnimation** - Element floating effect
- **ScrollReveal** - Scroll-triggered animations
- **HoverCardAnimation** - Card hover effects
- **MenuAnimation** - Menu underline animation

### Utility Classes
- **ButtonManager** - Button hover & click effects
- **SmoothNavigation** - Smooth scroll navigation
- **KeyboardShortcuts** - Keyboard shortcuts handler
- **CounterAnimation** - Number counter animation
- **TextReveal** - Text reveal animation

---

## 🎯 Fitur Khusus

### Form Validation
```javascript
// Client-side validation
- Email format
- Phone number format (+62 atau 0)
- Message minimum length
- Empty field checking

// Server-side validation (PHP)
- Double check semua field
- Email verification
- Sanitization & escaping
```

### Data Storage
- **Messages**: `data/messages.json`
- **Contacts**: `data/contacts.json`
- Automatic folder creation
- JSON format untuk easy reading

### Error Handling
```
200 OK - Request berhasil
201 Created - Resource dibuat
400 Bad Request - Validasi gagal
404 Not Found - Resource tidak ditemukan
405 Method Not Allowed - HTTP method salah
422 Unprocessable Entity - Validasi error detail
500 Server Error - Internal error
```

---

## 🔒 Security Features

### Frontend
- XSS Protection via sanitization
- CSRF token validation ready
- Secure form submission

### Backend  
- Input sanitization (htmlspecialchars)
- Email validation
- CORS headers
- Error logging
- Input escaping

---

## 📚 Penggunaan API dari JavaScript

```javascript
// Inisialisasi API Client
const api = new PHPApiClient();

// Get projects
api.getProjects().then(response => {
  if (response.success) {
    console.log(response.data);
  }
});

// Send contact message
api.sendMessage({
  name: "John",
  email: "john@example.com",
  phone: "08123456789",
  subject: "Konsultasi",
  message: "Halo, saya tertarik..."
}).then(response => {
  if (response.success) {
    console.log("Message sent!");
  }
});

// Get stats
api.getStats().then(response => {
  console.log(response.data);
});

// Ping server
api.ping().then(response => {
  console.log(response.data.status);
});
```

---

## 🎨 Kustomisasi

### Warna Primary
Edit di `style.css`:
```css
--primary-color: #2563eb;
--primary-dark: #1d4ed8;
```

### Animasi Duration
Edit di `script.js`:
```javascript
animation: slideInUp 0.8s ease-out; /* Ubah 0.8s */
```

### Form Fields
Edit di `index.html` section contact form

### PHP Config
Edit di `config.php`:
```php
define('ADMIN_EMAIL', 'your-email@example.com');
define('APP_NAME', 'Nama Aplikasi Anda');
```

---

## 🐛 Troubleshooting

### CORS Error di Browser
✅ Sudah di-handle di `api.php` dengan CORS headers

### PHP File Not Executing
- Check PHP version: `php -v`
- Enable PHP extensions di php.ini

### Form Not Submitting
- Check browser console untuk error
- Verify folder `data/` exists dan writable
- Check browser Network tab

### Animations Not Smooth
- Update browser ke versi terbaru
- Check GPU acceleration di browser settings
- Reduce particle count jika FPS rendah

---

## 📈 Future Enhancements

- [ ] Database integration (MySQL)
- [ ] User authentication
- [ ] Admin dashboard
- [ ] Email notifications
- [ ] File upload support
- [ ] Search functionality
- [ ] Pagination
- [ ] Export to PDF

---

## 📞 Support & Contact

📧 Email: admin@example.com
🌐 Website: https://example.com
💼 LinkedIn: [Your Profile]

---

## 📄 License

© 2026 Piki Abdul Azis (Kyy). All Rights Reserved.

---

## 🙏 Credits

- Design Inspiration: Modern Portfolio Templates
- Animation Library: Inspired by popular animation patterns
- Icons & Colors: Tailwind CSS color palette

---

**Last Updated**: June 7, 2026
**Version**: 1.0.0
**Status**: Production Ready ✅
