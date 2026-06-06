<?php
/**
 * Admin Dashboard
 * Main dashboard untuk mengelola website
 */

session_start();
require_once '../config.php';
require_once '../functions.php';
require_once '../auth.php';

// Check if user is admin
if (!Authentication::isAdmin()) {
    header("Location: ../index.php");
    exit;
}

$user = Authentication::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portofolio Kyy</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* Admin Dashboard Styles */
        .admin-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: #1a1a1a;
            color: white;
            padding: 20px;
            position: fixed;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
        }

        .admin-sidebar h3 {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            text-transform: uppercase;
            opacity: 0.7;
        }

        .admin-sidebar ul {
            list-style: none;
        }

        .admin-sidebar li {
            margin: 5px 0;
        }

        .admin-sidebar a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background: #0066ff;
        }

        .admin-main {
            margin-left: 250px;
            padding: 20px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card h4 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #0066ff;
        }

        .admin-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .admin-table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .admin-table tr:hover {
            background: #f9f9f9;
        }

        .btn-group {
            display: flex;
            gap: 5px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.85rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-sm.edit {
            background: #0066ff;
            color: white;
        }

        .btn-sm.delete {
            background: #ff3333;
            color: white;
        }

        .btn-sm:hover {
            opacity: 0.8;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2>Admin Panel</h2>
            <p style="font-size: 0.9rem; opacity: 0.8;">Welcome, <?php echo htmlspecialchars($user['username']); ?></p>
            
            <h3>Dashboard</h3>
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="projects.php">Projects</a></li>
            </ul>

            <h3>Content</h3>
            <ul>
                <li><a href="blog.php">Blog Posts</a></li>
                <li><a href="blog-categories.php">Blog Categories</a></li>
                <li><a href="blog-comments.php">Comments</a></li>
            </ul>

            <h3>Portfolio</h3>
            <ul>
                <li><a href="skills.php">Skills</a></li>
                <li><a href="experiences.php">Experiences</a></li>
                <li><a href="testimonials.php">Testimonials</a></li>
            </ul>

            <h3>Messages</h3>
            <ul>
                <li><a href="contacts.php">Contact Messages</a></li>
                <li><a href="newsletter.php">Newsletter</a></li>
            </ul>

            <h3>Account</h3>
            <ul>
                <li><a href="../contact.php?action=logout">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <p><?php echo date('d F Y, H:i'); ?></p>
            </div>

            <!-- Statistics -->
            <div class="admin-stats">
                <div class="stat-card">
                    <h4>Total Projects</h4>
                    <div class="number" id="stat-projects">0</div>
                </div>
                <div class="stat-card">
                    <h4>Blog Posts</h4>
                    <div class="number" id="stat-posts">0</div>
                </div>
                <div class="stat-card">
                    <h4>Contact Messages</h4>
                    <div class="number" id="stat-messages">0</div>
                </div>
                <div class="stat-card">
                    <h4>Newsletter Subscribers</h4>
                    <div class="number" id="stat-subscribers">0</div>
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="admin-section">
                <h2>Recent Contact Messages</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="messages-list">
                        <tr>
                            <td colspan="6" style="text-align: center; color: #999;">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Recent Blog Posts -->
            <div class="admin-section">
                <h2>Recent Blog Posts</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Published</th>
                            <th>Views</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="posts-list">
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Load statistics
        async function loadStatistics() {
            try {
                const response = await fetch('../api.php?action=get_stats');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('stat-projects').textContent = data.data.projects || 0;
                    document.getElementById('stat-posts').textContent = data.data.posts || 0;
                    document.getElementById('stat-messages').textContent = data.data.messages || 0;
                    document.getElementById('stat-subscribers').textContent = data.data.subscribers || 0;
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        }

        // Load recent messages
        async function loadRecentMessages() {
            try {
                const response = await fetch('../api.php?action=get_contacts&limit=5');
                const data = await response.json();
                if (data.success && data.data.length > 0) {
                    const html = data.data.map(msg => `
                        <tr>
                            <td>${msg.name}</td>
                            <td>${msg.email}</td>
                            <td>${msg.subject}</td>
                            <td>${new Date(msg.created_at).toLocaleDateString('id-ID')}</td>
                            <td><span class="badge badge-${msg.status}">${msg.status}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="contacts.php?id=${msg.id}" class="btn-sm edit">View</a>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                    document.getElementById('messages-list').innerHTML = html;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        // Load recent posts
        async function loadRecentPosts() {
            try {
                const response = await fetch('../blog.php?action=get_all_posts&limit=5');
                const data = await response.json();
                if (data.success && data.data.length > 0) {
                    const html = data.data.map(post => `
                        <tr>
                            <td>${post.title}</td>
                            <td>${post.author}</td>
                            <td>${post.published_at ? 'Yes' : 'No'}</td>
                            <td>${post.view_count}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="blog.php?edit=${post.id}" class="btn-sm edit">Edit</a>
                                    <button class="btn-sm delete" onclick="deletePost(${post.id})">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                    document.getElementById('posts-list').innerHTML = html;
                }
            } catch (error) {
                console.error('Error loading posts:', error);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadStatistics();
            loadRecentMessages();
            loadRecentPosts();
        });
    </script>
</body>
</html>
