-- ============================================
-- Portfolio Kyy - Database Schema (v2.0)
-- ============================================
-- Author: Piki Abdul Azis
-- Version: 2.0.0
-- Last Updated: June 7, 2026
-- Features: Auth, Blog, Skills, Timeline, Testimonials, Newsletter
-- ============================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS portfolio_kyy;
USE portfolio_kyy;

-- ============================================
-- TABLE: users (Authentication)
-- ============================================
-- Menyimpan data user untuk login
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: projects
-- ============================================
-- Menyimpan data project portofolio
CREATE TABLE IF NOT EXISTS projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    technologies JSON,
    rating INT DEFAULT 5,
    year INT,
    image_url VARCHAR(255),
    url VARCHAR(255),
    category VARCHAR(100),
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_year (year),
    INDEX idx_rating (rating),
    INDEX idx_category (category),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: skills
-- ============================================
-- Menyimpan data skill dengan level
CREATE TABLE IF NOT EXISTS skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100),
    level INT CHECK (level >= 1 AND level <= 5),
    percentage INT CHECK (percentage >= 0 AND percentage <= 100),
    description TEXT,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_level (level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: experiences
-- ============================================
-- Menyimpan timeline pengalaman kerja
CREATE TABLE IF NOT EXISTS experiences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    description LONGTEXT,
    start_date DATE NOT NULL,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    location VARCHAR(255),
    skills_used JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_start_date (start_date),
    INDEX idx_current (is_current)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: blog_categories
-- ============================================
-- Kategori untuk blog posts
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: blog_posts
-- ============================================
-- Menyimpan artikel blog
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt VARCHAR(500),
    category_id INT,
    author_id INT NOT NULL,
    featured_image VARCHAR(255),
    view_count INT DEFAULT 0,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_published (is_published),
    INDEX idx_category (category_id),
    FULLTEXT INDEX ft_title_content (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: blog_comments
-- ============================================
-- Komentar untuk blog posts
CREATE TABLE IF NOT EXISTS blog_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    author_name VARCHAR(255) NOT NULL,
    author_email VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: testimonials
-- ============================================
-- Menyimpan testimonial klien dengan rating
CREATE TABLE IF NOT EXISTS testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(255) NOT NULL,
    client_company VARCHAR(255),
    client_image VARCHAR(255),
    rating INT CHECK (rating >= 1 AND rating <= 5),
    testimonial_text LONGTEXT NOT NULL,
    project_id INT,
    is_featured BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    INDEX idx_rating (rating),
    INDEX idx_featured (is_featured),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: newsletter_subscribers
-- ============================================
-- Menyimpan subscriber newsletter
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: contacts
-- ============================================
-- Menyimpan data kontak dari form
CREATE TABLE IF NOT EXISTS contacts (
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FULLTEXT INDEX ft_message (message, subject)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: messages
-- ============================================
-- Menyimpan pesan sistem dan logs
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT,
    sender VARCHAR(100),
    message_text LONGTEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    INDEX idx_contact_id (contact_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: stats
-- ============================================
-- Menyimpan statistik aplikasi
CREATE TABLE IF NOT EXISTS stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stat_key VARCHAR(100) UNIQUE NOT NULL,
    stat_value INT DEFAULT 0,
    description VARCHAR(255),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: settings
-- ============================================
-- Menyimpan setting aplikasi
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: activity_logs
-- ============================================
-- Menyimpan log aktivitas sistem
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    entity_type VARCHAR(50),
    entity_id INT,
    description LONGTEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INITIAL DATA
-- ============================================

-- Insert sample projects
INSERT INTO projects (title, description, technologies, rating, year, url) VALUES
(
    'Website Portofolio',
    'Portfolio website dengan HTML5, CSS3, JavaScript ES6+, dan PHP Backend dengan MySQL Database. Fitur lengkap termasuk dark mode, animasi smooth, form validation, dan API REST.',
    '["HTML5", "CSS3", "JavaScript", "PHP", "MySQL"]',
    5,
    2026,
    'https://example.com/portfolio'
),
(
    'E-Commerce Platform',
    'Platform e-commerce dengan fitur produk, shopping cart, checkout, payment gateway integration, dan admin dashboard untuk manajemen inventory.',
    '["PHP", "MySQL", "JavaScript", "Bootstrap"]',
    5,
    2025,
    'https://example.com/ecommerce'
),
(
    'Mobile App - Todo List',
    'Aplikasi mobile untuk task management dengan React Native, real-time sync dengan Firebase, dan offline capability.',
    '["React Native", "Firebase", "JavaScript"]',
    4,
    2024,
    'https://example.com/todoapp'
);

-- Insert initial settings
INSERT INTO settings (setting_key, setting_value, setting_type) VALUES
('app_name', 'Portfolio Kyy', 'string'),
('app_version', '1.0.0', 'string'),
('admin_email', 'admin@example.com', 'string'),
('contact_email_enabled', 'true', 'boolean'),
('items_per_page', '10', 'number'),
('timezone', 'Asia/Jakarta', 'string');

-- Insert initial stats
INSERT INTO stats (stat_key, stat_value, description) VALUES
('total_projects', 3, 'Total number of projects'),
('total_contacts', 0, 'Total contact submissions'),
('total_messages', 0, 'Total messages received'),
('website_visitors', 0, 'Total website visitors');

-- ============================================
-- VIEWS (Optional)
-- ============================================

-- View: Project Summary
CREATE OR REPLACE VIEW project_summary AS
SELECT 
    id,
    title,
    SUBSTRING(description, 1, 100) as description_short,
    JSON_LENGTH(technologies) as tech_count,
    rating,
    year,
    created_at
FROM projects
ORDER BY year DESC;

-- View: Contact Summary
CREATE OR REPLACE VIEW contact_summary AS
SELECT 
    id,
    name,
    email,
    subject,
    status,
    DATE(created_at) as contact_date,
    COUNT(*) OVER() as total_contacts
FROM contacts
ORDER BY created_at DESC;

-- ============================================
-- STORED PROCEDURES
-- ============================================

-- Procedure: Get project statistics
DELIMITER //
CREATE PROCEDURE sp_get_project_stats()
BEGIN
    SELECT 
        COUNT(*) as total_projects,
        AVG(rating) as average_rating,
        MAX(year) as latest_year,
        MIN(year) as earliest_year
    FROM projects;
END //
DELIMITER ;

-- Procedure: Get contact statistics
DELIMITER //
CREATE PROCEDURE sp_get_contact_stats()
BEGIN
    SELECT 
        COUNT(*) as total_contacts,
        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_contacts,
        SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied_contacts
    FROM contacts;
END //
DELIMITER ;

-- ============================================
-- INDEXES (Performance Optimization)
-- ============================================
-- Sudah ada di CREATE TABLE statements
-- Berikut adalah summary:
-- projects: idx_year, idx_rating
-- contacts: idx_email, idx_status, idx_created_at, FULLTEXT ft_message
-- messages: idx_contact_id, idx_created_at
-- users: idx_username, idx_email
-- activity_logs: idx_user_id, idx_created_at, idx_action

-- ============================================
-- END OF SCHEMA
-- ============================================
