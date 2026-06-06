<?php
/**
 * Blog Management System
 * Handles blog posts, categories, comments
 */

require_once 'config.php';
require_once 'functions.php';
require_once 'auth.php';

class Blog {
    private $db;
    private $errors = [];

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Create blog post (admin only)
     */
    public function createPost($title, $content, $excerpt, $category_id, $featured_image) {
        if (!Authentication::isAdmin()) {
            $this->errors[] = "Hanya admin yang bisa membuat post";
            return false;
        }

        if (empty($title) || empty($content)) {
            $this->errors[] = "Judul dan konten harus diisi";
            return false;
        }

        $user = Authentication::getCurrentUser();
        $slug = $this->generateSlug($title);

        try {
            $query = "INSERT INTO blog_posts (title, slug, content, excerpt, category_id, author_id, featured_image, is_published) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
            
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$title, $slug, $content, $excerpt, $category_id, $user['id'], $featured_image]);
            
            return $this->db->conn->lastInsertId();
        } catch (Exception $e) {
            $this->errors[] = "Error membuat post: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update blog post
     */
    public function updatePost($post_id, $title, $content, $excerpt, $category_id, $featured_image = null) {
        if (!Authentication::isAdmin()) {
            $this->errors[] = "Hanya admin yang bisa edit post";
            return false;
        }

        try {
            if ($featured_image) {
                $query = "UPDATE blog_posts SET title = ?, content = ?, excerpt = ?, category_id = ?, featured_image = ? WHERE id = ?";
                $stmt = $this->db->conn->prepare($query);
                $stmt->execute([$title, $content, $excerpt, $category_id, $featured_image, $post_id]);
            } else {
                $query = "UPDATE blog_posts SET title = ?, content = ?, excerpt = ?, category_id = ? WHERE id = ?";
                $stmt = $this->db->conn->prepare($query);
                $stmt->execute([$title, $content, $excerpt, $category_id, $post_id]);
            }
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error update post: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Publish blog post
     */
    public function publishPost($post_id) {
        if (!Authentication::isAdmin()) {
            $this->errors[] = "Hanya admin yang bisa publish post";
            return false;
        }

        try {
            $query = "UPDATE blog_posts SET is_published = 1, published_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$post_id]);
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error publishing post: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get all published posts
     */
    public function getAllPosts($limit = 10, $offset = 0) {
        try {
            $query = "SELECT bp.*, u.username as author, bc.name as category 
                      FROM blog_posts bp 
                      LEFT JOIN users u ON bp.author_id = u.id 
                      LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                      WHERE bp.is_published = 1 
                      ORDER BY bp.published_at DESC 
                      LIMIT ? OFFSET ?";
            
            $stmt = $this->db->conn->prepare($query);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error getting posts: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Get single post by slug
     */
    public function getPostBySlug($slug) {
        try {
            $query = "SELECT bp.*, u.username as author, u.email, bc.name as category 
                      FROM blog_posts bp 
                      LEFT JOIN users u ON bp.author_id = u.id 
                      LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                      WHERE bp.slug = ? AND bp.is_published = 1";
            
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$slug]);
            
            // Update view count
            if ($stmt->rowCount() > 0) {
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->incrementViewCount($post['id']);
                return $post;
            }
            return null;
        } catch (Exception $e) {
            $this->errors[] = "Error getting post: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Get posts by category
     */
    public function getPostsByCategory($category_id, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT bp.*, u.username as author, bc.name as category 
                      FROM blog_posts bp 
                      LEFT JOIN users u ON bp.author_id = u.id 
                      LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                      WHERE bp.category_id = ? AND bp.is_published = 1 
                      ORDER BY bp.published_at DESC 
                      LIMIT ? OFFSET ?";
            
            $stmt = $this->db->conn->prepare($query);
            $stmt->bindParam(1, $category_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            $stmt->bindParam(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error getting posts: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Search posts
     */
    public function searchPosts($keyword, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT bp.*, u.username as author, bc.name as category 
                      FROM blog_posts bp 
                      LEFT JOIN users u ON bp.author_id = u.id 
                      LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                      WHERE bp.is_published = 1 AND MATCH(bp.title, bp.content) AGAINST(? IN BOOLEAN MODE) 
                      ORDER BY bp.published_at DESC 
                      LIMIT ? OFFSET ?";
            
            $stmt = $this->db->conn->prepare($query);
            $stmt->bindParam(1, $keyword);
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            $stmt->bindParam(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error searching posts: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Add comment to post
     */
    public function addComment($post_id, $author_name, $author_email, $content) {
        if (empty($author_name) || empty($author_email) || empty($content)) {
            $this->errors[] = "Semua field harus diisi";
            return false;
        }

        if (!filter_var($author_email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Email tidak valid";
            return false;
        }

        try {
            $query = "INSERT INTO blog_comments (post_id, author_name, author_email, content) 
                      VALUES (?, ?, ?, ?)";
            
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$post_id, $author_name, $author_email, $content]);
            
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error adding comment: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get comments for post
     */
    public function getComments($post_id) {
        try {
            $query = "SELECT * FROM blog_comments WHERE post_id = ? AND is_approved = 1 ORDER BY created_at DESC";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$post_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error getting comments: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        try {
            $query = "SELECT * FROM blog_categories ORDER BY name ASC";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error getting categories: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Generate URL-friendly slug
     */
    private function generateSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        return $slug;
    }

    /**
     * Increment view count
     */
    private function incrementViewCount($post_id) {
        try {
            $query = "UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$post_id]);
        } catch (Exception $e) {
            // Silent fail
        }
    }

    /**
     * Get errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Delete post (admin only)
     */
    public function deletePost($post_id) {
        if (!Authentication::isAdmin()) {
            $this->errors[] = "Hanya admin yang bisa delete post";
            return false;
        }

        try {
            $query = "DELETE FROM blog_posts WHERE id = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$post_id]);
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error deleting post: " . $e->getMessage();
            return false;
        }
    }
}

// API endpoints
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    $blog = new Blog();

    switch ($_GET['action']) {
        case 'get_all_posts':
            $limit = intval($_GET['limit'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);
            $posts = $blog->getAllPosts($limit, $offset);
            echo json_encode(['success' => true, 'data' => $posts]);
            break;

        case 'get_post':
            $slug = $_GET['slug'] ?? '';
            $post = $blog->getPostBySlug($slug);
            if ($post) {
                echo json_encode(['success' => true, 'data' => $post]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Post tidak ditemukan']);
            }
            break;

        case 'get_categories':
            $categories = $blog->getAllCategories();
            echo json_encode(['success' => true, 'data' => $categories]);
            break;

        case 'get_comments':
            $post_id = intval($_GET['post_id'] ?? 0);
            $comments = $blog->getComments($post_id);
            echo json_encode(['success' => true, 'data' => $comments]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action tidak dikenal']);
    }
    exit;
}
?>
