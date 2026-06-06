<?php
/**
 * Sample Data Loader
 * Populate database dengan sample data untuk testing
 */

require_once 'config.php';

$db = new Database();

echo "🔄 Loading sample data...\n\n";

try {
    // 1. Create Admin User
    echo "1️⃣ Creating admin user...\n";
    $admin_password = password_hash('admin123', PASSWORD_BCRYPT);
    $db->conn->prepare("INSERT IGNORE INTO users (username, email, password, full_name, role, is_active) VALUES (?, ?, ?, ?, ?, 1)")
        ->execute(['admin', 'admin@example.com', $admin_password, 'Administrator', 'admin']);
    echo "✅ Admin user created\n\n";

    // 2. Insert Skills
    echo "2️⃣ Creating skills...\n";
    $skills_data = [
        ['PHP', 'Backend', 5, 95, 'Expert in PHP development', 'fab fa-php'],
        ['JavaScript', 'Frontend', 5, 90, 'Advanced JavaScript & ES6+', 'fab fa-js'],
        ['React', 'Frontend', 4, 80, 'React with hooks', 'fab fa-react'],
        ['MySQL', 'Database', 5, 90, 'Database design & optimization', 'fas fa-database'],
        ['HTML/CSS', 'Frontend', 5, 100, 'Semantic HTML & Modern CSS', 'fab fa-html5'],
        ['REST API', 'Backend', 5, 85, 'RESTful API design', 'fas fa-code'],
    ];

    $insert_skill = $db->conn->prepare("INSERT IGNORE INTO skills (name, category, level, percentage, description, icon) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($skills_data as $skill) {
        $insert_skill->execute($skill);
    }
    echo "✅ " . count($skills_data) . " skills created\n\n";

    // 3. Insert Experiences
    echo "3️⃣ Creating experiences...\n";
    $experiences_data = [
        [
            'Full Stack Developer',
            'Tech Startup Inc',
            'Develop dan maintain web applications menggunakan PHP dan React',
            '2024-01-15',
            null,
            1,
            'Jakarta',
            json_encode(['PHP', 'React', 'MySQL', 'REST API'])
        ],
        [
            'Frontend Developer',
            'Digital Agency XYZ',
            'Create responsive UI dan implement interactive features',
            '2023-06-01',
            '2023-12-31',
            0,
            'Bandung',
            json_encode(['JavaScript', 'React', 'CSS'])
        ],
        [
            'Junior Web Developer',
            'Freelance Projects',
            'Build custom websites untuk berbagai klien',
            '2022-01-01',
            '2023-05-31',
            0,
            'Remote',
            json_encode(['PHP', 'HTML', 'CSS', 'JavaScript'])
        ],
    ];

    $insert_exp = $db->conn->prepare("INSERT INTO experiences (title, company, description, start_date, end_date, is_current, location, skills_used) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($experiences_data as $exp) {
        $insert_exp->execute($exp);
    }
    echo "✅ " . count($experiences_data) . " experiences created\n\n";

    // 4. Insert Blog Categories
    echo "4️⃣ Creating blog categories...\n";
    $categories_data = [
        ['Web Development', 'web-development', 'Tips & tricks untuk web development', 'fas fa-code'],
        ['JavaScript', 'javascript', 'Artikel tentang JavaScript & ES6+', 'fab fa-js'],
        ['PHP', 'php', 'Tutorial PHP untuk pemula hingga advanced', 'fab fa-php'],
        ['Design', 'design', 'UI/UX design principles', 'fas fa-palette'],
    ];

    $insert_cat = $db->conn->prepare("INSERT IGNORE INTO blog_categories (name, slug, description, icon) VALUES (?, ?, ?, ?)");
    foreach ($categories_data as $cat) {
        $insert_cat->execute($cat);
    }
    echo "✅ " . count($categories_data) . " blog categories created\n\n";

    // 5. Insert Blog Posts
    echo "5️⃣ Creating blog posts...\n";
    $posts_data = [
        [
            'Getting Started with PHP',
            'getting-started-with-php',
            '<p>Panduan lengkap untuk memulai belajar PHP dari nol. PHP adalah bahasa server-side yang powerful untuk membuat dynamic websites...</p>',
            'Pelajari dasar-dasar PHP dengan contoh praktis dan best practices',
            1,
            1,
            'blog1.jpg',
            1,
            1,
            '2026-06-01 10:00:00'
        ],
        [
            'React Hooks Tutorial',
            'react-hooks-tutorial',
            '<p>Pelajari cara menggunakan React Hooks untuk manage state dan lifecycle di functional components...</p>',
            'Master React Hooks dengan contoh real-world',
            2,
            1,
            'blog2.jpg',
            1,
            1,
            '2026-05-28 14:30:00'
        ],
        [
            'CSS Grid vs Flexbox',
            'css-grid-vs-flexbox',
            '<p>Perbandingan CSS Grid dan Flexbox, kapan menggunakan yang mana...</p>',
            'Memahami perbedaan dan use case CSS Grid dan Flexbox',
            4,
            1,
            'blog3.jpg',
            1,
            1,
            '2026-05-25 09:15:00'
        ],
    ];

    $insert_post = $db->conn->prepare("INSERT INTO blog_posts (title, slug, content, excerpt, category_id, author_id, featured_image, view_count, is_published, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($posts_data as $post) {
        $insert_post->execute($post);
    }
    echo "✅ " . count($posts_data) . " blog posts created\n\n";

    // 6. Insert Projects
    echo "6️⃣ Creating projects...\n";
    $projects_data = [
        [
            'E-Commerce Platform',
            'Platform e-commerce modern dengan fitur lengkap untuk bisnis online',
            json_encode(['PHP', 'React', 'MySQL', 'Stripe']),
            5,
            2026,
            'ecommerce.jpg',
            'https://example.com/ecommerce',
            'Shopping',
            1,
            0,
            1
        ],
        [
            'SaaS Dashboard',
            'Dashboard analytics dengan real-time data visualization',
            json_encode(['React', 'Node.js', 'MongoDB', 'Chart.js']),
            5,
            2025,
            'dashboard.jpg',
            'https://example.com/saas',
            'Software',
            1,
            50,
            1
        ],
        [
            'Mobile App API',
            'REST API untuk mobile application dengan authentication',
            json_encode(['PHP', 'MySQL', 'JWT', 'REST']),
            4,
            2025,
            'api.jpg',
            'https://example.com/api',
            'Backend',
            0,
            120,
            1
        ],
    ];

    $insert_proj = $db->conn->prepare("INSERT INTO projects (title, description, technologies, rating, year, image_url, url, category, is_featured, view_count, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($projects_data as $proj) {
        $insert_proj->execute($proj);
    }
    echo "✅ " . count($projects_data) . " projects created\n\n";

    // 7. Insert Testimonials
    echo "7️⃣ Creating testimonials...\n";
    $testimonials_data = [
        [
            'Budi Santoso',
            'PT Tech Solutions',
            5,
            'Kyy berhasil membuat website kami menjadi modern dan user-friendly. Sangat profesional dan responsive terhadap feedback!',
            'client1.jpg',
            1,
            1,
            1
        ],
        [
            'Siti Rahma',
            'Digital Agency Maju',
            5,
            'Kerjasama dengan Kyy sangat memuaskan. Code quality tinggi dan dokumentasi lengkap. Recommended!',
            'client2.jpg',
            2,
            1,
            1
        ],
        [
            'Ahmad Wijaya',
            'Startup Inovatif',
            4,
            'Kyy mengerti requirement dengan baik dan deliver tepat waktu. Kualitas kerjanya luar biasa.',
            'client3.jpg',
            3,
            1,
            1
        ],
    ];

    $insert_testi = $db->conn->prepare("INSERT INTO testimonials (client_name, client_company, rating, testimonial_text, client_image, project_id, is_approved, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($testimonials_data as $testi) {
        $insert_testi->execute($testi);
    }
    echo "✅ " . count($testimonials_data) . " testimonials created\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ All sample data loaded successfully!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "📝 Next Steps:\n";
    echo "1. Visit: http://localhost/BelajarCoding-Kyy/login.php\n";
    echo "2. Login with:\n";
    echo "   Email: admin@example.com\n";
    echo "   Password: admin123\n";
    echo "3. Access admin panel at: http://localhost/BelajarCoding-Kyy/admin/\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
