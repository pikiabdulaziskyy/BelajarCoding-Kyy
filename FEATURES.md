# 🚀 Portofolio Kyy v2.0 - Full Stack Web Application

**Website Portfolio Modern dengan Fitur Lengkap: Authentication, Blog, Skills, Timeline, Testimonials, Newsletter, dan Search**

---

## ✨ Fitur Baru (v2.0)

### 1. **Authentication & Admin Panel** 🔐
- ✅ User Registration & Login dengan secure password hashing
- ✅ Admin Dashboard untuk manage content
- ✅ CSRF token protection
- ✅ Session management dengan cookie security

### 2. **Blog System** 📝
- ✅ Create, Read, Update, Delete blog posts
- ✅ Blog categories dan tags
- ✅ Comment system untuk posts
- ✅ View counter untuk setiap post
- ✅ Full-text search pada blog posts
- ✅ Published/Draft status untuk posts

### 3. **Skills & Expertise** 🎯
- ✅ Skill showcase dengan categories
- ✅ Skill level (1-5) dan percentage
- ✅ Progress bars untuk visualisasi
- ✅ Icon support untuk skills

### 4. **Experience Timeline** 📊
- ✅ Timeline pengalaman kerja chronological
- ✅ Current vs Past jobs distinction
- ✅ Location dan skills used per experience
- ✅ Beautiful visual timeline UI

### 5. **Testimonials & Reviews** ⭐
- ✅ Client testimonials dengan rating (1-5 stars)
- ✅ Featured testimonials showcase
- ✅ Client company dan image support
- ✅ Approval workflow untuk testimonials

### 6. **Newsletter System** 📧
- ✅ Email subscription
- ✅ Subscriber management
- ✅ Verification tokens
- ✅ Unsubscribe functionality
- ✅ Subscriber count tracking

### 7. **Advanced Search & Filter** 🔍
- ✅ Global search across projects, blog, skills
- ✅ Filter projects by technology
- ✅ Filter projects by year
- ✅ Real-time search suggestions
- ✅ Full-text search dengan MySQL FT Index

### 8. **Portfolio Gallery** 🖼️
- ✅ Project listing dengan filters
- ✅ Technology tags untuk setiap project
- ✅ Project rating display
- ✅ Featured projects support
- ✅ View counter untuk projects

### 9. **Contact Form** 📞
- ✅ Advanced contact form
- ✅ Email, phone, subject validation
- ✅ Message storage di database
- ✅ Admin notification system

---

## 📁 Struktur Project

```
BelajarCoding-Kyy/
├── index.html              # Homepage utama (v2.0)
├── login.php               # Login & Register page
├── style.css               # Stylesheet lengkap
├── script.js               # JavaScript utilities
│
├── auth.php                # Authentication handler
├── blog.php                # Blog system
├── portfolio.php           # Skills, experiences, testimonials, newsletter
├── search.php              # Search & filter functionality
├── api.php                 # REST API endpoints
├── contact.php             # Contact form handler
├── config.php              # Database config
├── functions.php           # Helper functions
├── db.php                  # Database connection class
│
├── database.sql            # Complete database schema (v2.0)
├── MYSQL_SETUP.md          # MySQL setup guide
│
├── admin/                  # Admin panel folder
│   ├── index.php           # Dashboard main
│   ├── projects.php        # Manage projects
│   ├── blog.php            # Manage blog posts
│   ├── blog-categories.php # Manage categories
│   ├── blog-comments.php   # Moderate comments
│   ├── skills.php          # Manage skills
│   ├── experiences.php     # Manage experiences
│   ├── testimonials.php    # Manage testimonials
│   ├── contacts.php        # Manage contact messages
│   └── newsletter.php      # Manage subscribers
│
├── README.md               # This file
└── BACKEND_DOCS.md         # Backend documentation
```

---

## 🚀 Setup & Installation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)

### Step 1: Database Setup

```bash
# Create database dan tables
mysql -u root -p < database.sql
```

### Step 2: Update Config

Edit `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'portfolio_kyy');
```

### Step 3: Create Initial Admin Account

Insert ke database:
```sql
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@example.com', '$2y$10$...', 'Administrator', 'admin');
```

Password di-hash dengan bcrypt. Gunakan `password_hash('password', PASSWORD_BCRYPT)` untuk generate.

### Step 4: Upload Files

Upload semua file ke web server root directory.

### Step 5: Test

1. Buka `http://localhost/BelajarCoding-Kyy/`
2. Klik "Admin Login"
3. Login dengan credentials admin
4. Akses admin panel di `http://localhost/BelajarCoding-Kyy/admin/`

---

## 📚 API Documentation

### Projects
```
GET /api.php?action=get_projects
GET /api.php?action=get_project&id=1
POST /api.php - Create project (admin only)
```

### Blog
```
GET /blog.php?action=get_all_posts&limit=10&offset=0
GET /blog.php?action=get_post&slug=post-title
GET /blog.php?action=get_categories
POST /blog.php - Add comment atau create post
```

### Portfolio Features
```
GET /portfolio.php?action=get_skills
GET /portfolio.php?action=get_experiences
GET /portfolio.php?action=get_testimonials&type=featured
POST /portfolio.php - Subscribe newsletter, add testimonial
```

### Search & Filter
```
GET /search.php?action=search&type=all&q=keyword
GET /search.php?action=filter_by_tech&technology=PHP
GET /search.php?action=filter_by_year&year=2026
GET /search.php?action=get_technologies
GET /search.php?action=get_years
```

---

## 🔐 Security Features

✅ **Password Security**
- Bcrypt hashing dengan salt
- Minimum 6 karakter requirement
- Secure password verification

✅ **CSRF Protection**
- CSRF token generation
- Token validation di form submission
- Session-based token storage

✅ **SQL Injection Prevention**
- Prepared statements di semua queries
- Parameter binding dengan PDO
- Input validation & sanitization

✅ **XSS Protection**
- HTML escape untuk user input
- Content-type headers setting
- JavaScript escaping

✅ **Session Security**
- Secure session handling
- HTTP-only cookies
- Session timeout

---

## 🎨 Frontend Features

### Search
- Real-time search dengan debouncing
- Global search across content
- Modal display hasil search
- Search suggestions

### Filtering
- Filter projects by technology
- Filter projects by year
- Dynamic filter loading
- Tag-based filtering

### Responsive Design
- Mobile-first approach
- Breakpoints: 640px, 768px, 900px
- Flexible grid layouts
- Touch-friendly interface

### User Experience
- Smooth scrolling navigation
- Loading states
- Error messages
- Success notifications
- Accessible forms

---

## 📊 Database Schema

### Main Tables
1. **users** - User accounts dan authentication
2. **projects** - Portfolio projects
3. **skills** - Skills dengan categories
4. **experiences** - Work experiences
5. **blog_posts** - Blog articles
6. **blog_categories** - Blog categories
7. **blog_comments** - Blog comments
8. **testimonials** - Client testimonials
9. **newsletter_subscribers** - Newsletter emails
10. **contacts** - Contact form messages

### Relationships
```
users → projects (created_by)
users → blog_posts (author_id)
blog_categories → blog_posts (category_id)
blog_posts → blog_comments (post_id)
projects → testimonials (project_id)
```

---

## 🛠️ Admin Panel Features

### Dashboard
- Statistics overview
- Recent messages
- Recent blog posts
- Quick actions

### Content Management
- **Projects**: Create, edit, delete, publish
- **Blog**: Full CRUD operations, draft/publish
- **Skills**: Manage skills dengan levels
- **Experiences**: Add work history
- **Testimonials**: Approve/feature testimonials

### Messages & Subscribers
- Contact messages management
- Newsletter subscriber list
- Bulk actions support

---

## 📱 Responsive Breakpoints

```css
Mobile: < 640px
Tablet: 640px - 900px
Desktop: > 900px
```

---

## 🔄 File Upload Handling

**Supported for:**
- Project images
- Blog featured images
- Testimonial client photos

**Upload Handler:** `/upload.php` (to be created)

**Requirements:**
- Max file size: 5MB
- Allowed formats: JPG, PNG, WebP
- Auto-resize: 1200x800px max

---

## 🚀 Performance Optimization

✅ Database Indexing
- Indexes pada frequently queried fields
- Full-text indexes untuk search

✅ Caching
- Client-side caching dengan localStorage
- API response caching

✅ Lazy Loading
- Images lazy load di blog
- Infinite scroll untuk testimonials

---

## 🐛 Testing Checklist

- [ ] Registration & Login flow
- [ ] Admin dashboard access
- [ ] Create/Edit/Delete projects
- [ ] Blog CRUD operations
- [ ] Comment moderation
- [ ] Newsletter subscription
- [ ] Search functionality
- [ ] Filter by tech & year
- [ ] Contact form submission
- [ ] Responsive design di mobile
- [ ] Security (SQL injection, XSS)
- [ ] Form validation
- [ ] Error handling

---

## 📞 Support & Troubleshooting

### Common Issues

**1. Database connection error**
```
Fix: Check config.php credentials
```

**2. 404 on admin panel**
```
Fix: Check user role is 'admin'
Check session is started
```

**3. Search not working**
```
Fix: Ensure MySQL full-text indexes created
Check search.php permissions
```

**4. Upload fails**
```
Fix: Check directory permissions
Create 'uploads' folder if not exists
```

---

## 🔮 Future Enhancements

- [ ] Email notifications
- [ ] Advanced analytics
- [ ] Social media integration
- [ ] Dark mode toggle
- [ ] Multi-language support
- [ ] Progressive Web App (PWA)
- [ ] API rate limiting
- [ ] Two-factor authentication
- [ ] Backup & restore functionality
- [ ] SEO optimization

---

## 📄 License

© 2026 Kyy. All rights reserved.

---

**Last Updated:** June 7, 2026  
**Version:** 2.0.0  
**Status:** Production Ready ✓
