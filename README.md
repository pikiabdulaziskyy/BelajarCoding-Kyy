# 🎨 Portofolio Kyy v2.0 - Full Stack Web Application

**Professional portfolio website with Complete CMS, Blog, Admin Panel, Authentication, and Advanced Features**

> 📝 **Version 2.0** - Major upgrade with 10+ new features!

---

## 📋 Quick Links

- 🚀 [Setup Guide](SETUP_GUIDE.md) - Step-by-step installation
- ✨ [Features List](FEATURES.md) - Complete feature documentation
- 📚 [API Docs](BACKEND_DOCS.md) - Backend API reference
- 🔐 [Security](SETUP_GUIDE.md#-security-configuration) - Security best practices

---

## ✨ Key Features v2.0

### 🔐 Authentication & Admin
- User registration & login with bcrypt security
- Admin dashboard with statistics
- CSRF token protection
- Role-based access control

### 📝 Blog System
- Full CRUD operations
- Categories & tags support
- Comment moderation
- Full-text search
- View counter

### 💼 Portfolio Features
- Skills showcase with proficiency levels
- Experience timeline
- Project gallery with filtering
- Technology-based filters
- Year-based filtering

### ⭐ Testimonials & Reviews
- Client testimonials with 5-star rating
- Featured testimonials display
- Client company & photo support
- Approval workflow

### 📧 Newsletter System
- Email subscription management
- Verification tokens
- Subscriber tracking
- Unsubscribe functionality

### 🔍 Advanced Search
- Global search across all content
- Real-time search suggestions
- Filter projects by technology
- Filter projects by year
- Full-text MySQL search

### 📱 User Experience
- Fully responsive design (mobile-first)
- Smooth animations & transitions
- Real-time form validation
- Error handling & notifications
- Accessible forms & navigation

---

## 📁 Struktur Proyek

```
BelajarCoding-Kyy/
├── 📄 index.html           # Homepage (v2.0)
├── 📄 login.php            # Login & Register
├── 🎨 style.css            # Complete stylesheet
├── ⚙️ script.js            # Frontend functionality
│
├── 🔐 auth.php             # Authentication handler
├── 📝 blog.php             # Blog system
├── 💼 portfolio.php        # Skills, experiences, testimonials
├── 🔍 search.php           # Search & filter
├── 📞 contact.php          # Contact form
├── 🔌 api.php              # REST API endpoints
│
├── ⚙️ config.php           # Configuration
├── 🗄️ db.php               # Database connection
├── 🛠️ functions.php        # Helper functions
│
├── 📋 admin/               # Admin panel
│   ├── index.php           # Dashboard
│   ├── projects.php        # Manage projects
│   ├── blog.php            # Manage blog
│   ├── skills.php          # Manage skills
│   ├── experiences.php     # Manage experiences
│   └── ...                 # Other management pages
│
├── 💾 database.sql         # Complete schema (v2.0)
├── 🌱 seed-data.php        # Sample data loader
├── 📖 README.md            # This file
├── 🚀 SETUP_GUIDE.md       # Installation guide
├── ✨ FEATURES.md          # Feature documentation
└── 📚 BACKEND_DOCS.md      # API documentation
```

---

## 🚀 Quick Start

### Minimum Requirements
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)

### Installation (5 minutes)

```bash
# 1. Clone / Download
git clone https://github.com/yourusername/portofolio-kyy.git
cd BelajarCoding-Kyy

# 2. Setup database
mysql -u root -p < database.sql

# 3. Configure
# Edit config.php with your database credentials

# 4. Load sample data (optional)
php seed-data.php

# 5. Access
# Visit: http://localhost/BelajarCoding-Kyy/
```

**Full setup guide:** [SETUP_GUIDE.md](SETUP_GUIDE.md)

---

## 🎯 Frontend Features

| Feature | Status | Details |
|---------|--------|---------|
| Responsive Design | ✅ | Mobile, tablet, desktop |
| Search Bar | ✅ | Real-time global search |
| Project Gallery | ✅ | With filters & tags |
| Blog Section | ✅ | Full articles & comments |
| Skills Display | ✅ | Progress bars & levels |
| Timeline | ✅ | Work experience chronological |
| Testimonials | ✅ | 5-star reviews carousel |
| Contact Form | ✅ | Email & validation |
| Newsletter | ✅ | Email subscription |
| Dark Mode | 🔄 | Coming in v2.1 |

---

## 🔧 Backend API

### Available Endpoints

```
GET  /api.php?action=get_projects
GET  /blog.php?action=get_all_posts
GET  /portfolio.php?action=get_skills
GET  /search.php?action=search&q=keyword
POST /auth.php - Login/Register
POST /contact.php - Submit message
POST /portfolio.php - Subscribe newsletter
```

**Full API docs:** [BACKEND_DOCS.md](BACKEND_DOCS.md)

---

## 🛠️ Technology Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling & responsive
- **JavaScript** - Vanilla JS (no frameworks)
- **Fetch API** - Ajax requests

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL** - Relational database
- **PDO** - Database abstraction
- **REST** - API architecture

### Security
- **Bcrypt** - Password hashing
- **CSRF Tokens** - Protection
- **Prepared Statements** - SQL injection prevention
- **Session Management** - Secure authentication

---

## 📊 Database Schema

10 main tables:
- `users` - User accounts
- `projects` - Portfolio projects
- `skills` - Skills with categories
- `experiences` - Work history
- `blog_posts` - Blog articles
- `blog_categories` - Categories
- `blog_comments` - Comments
- `testimonials` - Reviews
- `newsletter_subscribers` - Emails
- `contacts` - Messages

**Full schema:** [database.sql](database.sql)

---

## 🔐 Security Features

✅ **Password Security**
- Bcrypt hashing with salt
- Minimum 6 character requirement

✅ **CSRF Protection**
- Token generation & validation
- Session-based token storage

✅ **SQL Injection Prevention**
- Prepared statements
- Parameter binding with PDO

✅ **XSS Protection**
- HTML escaping
- Content-type headers

✅ **Session Security**
- HTTP-only cookies
- Session timeout
- Secure session handling

---

## 📱 Responsive Breakpoints

```
Mobile:  < 640px
Tablet:  640px - 900px
Desktop: > 900px
```

All sections are fully responsive and mobile-optimized.

---

## 🧪 Testing

### Test Admin Panel
1. Go to: `http://localhost/BelajarCoding-Kyy/login.php`
2. Login with: `admin@example.com` / `admin123`
3. Access: `http://localhost/BelajarCoding-Kyy/admin/`

### Test Features
- [ ] Create blog post
- [ ] Upload project
- [ ] Add skill
- [ ] Submit contact form
- [ ] Subscribe to newsletter
- [ ] Search functionality
- [ ] Filter projects

---

## 📈 Performance

- Optimized queries with indexing
- Lazy loading for images
- Caching strategies
- Compressed CSS & JavaScript
- Database query optimization

---

## 🐛 Known Issues

None currently. Please report any bugs on GitHub issues.

---

## 🔮 Roadmap v2.1

- [ ] Dark mode toggle
- [ ] Email notifications
- [ ] Advanced analytics
- [ ] Social media integration
- [ ] Multi-language support
- [ ] Progressive Web App (PWA)
- [ ] Two-factor authentication
- [ ] Backup & restore functionality

---

## 📞 Support

1. **Documentation**: Check [FEATURES.md](FEATURES.md) and [SETUP_GUIDE.md](SETUP_GUIDE.md)
2. **Issues**: Open GitHub issue
3. **Discussions**: Start a discussion
4. **Email**: admin@example.com

---

## 📄 License

© 2026 Kyy. All rights reserved.

This project is for educational and commercial use.

---

## 👏 Credits

**Version History:**
- v1.0 (Initial) - Basic portfolio
- v2.0 (Current) - Full CMS with blog, admin panel, advanced features

**Built with ❤️ by Kyy**

---

## ⭐ Show Your Support

If you found this project helpful, please consider giving it a star! ⭐

```
Your support motivates further development!
```

---

**Last Updated:** June 7, 2026  
**Status:** Production Ready ✅  
**Latest Version:** 2.0.0
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

📧 Email: PikiAbdulAzisKyy@gmail.com
🌐 Website: -
💼 LinkedIn: -

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
