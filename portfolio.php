<?php
/**
 * Portfolio Features
 * Skills, Experiences, Testimonials, Newsletter
 */

require_once 'config.php';
require_once 'functions.php';
require_once 'auth.php';

class Portfolio {
    private $db;
    private $errors = [];

    public function __construct() {
        $this->db = new Database();
    }

    // ==================== SKILLS ====================

    /**
     * Get all skills
     */
    public function getSkills($category = null) {
        try {
            if ($category) {
                $query = "SELECT * FROM skills WHERE category = ? ORDER BY category, level DESC";
                $stmt = $this->db->conn->prepare($query);
                $stmt->execute([$category]);
            } else {
                $query = "SELECT * FROM skills ORDER BY category, level DESC";
                $stmt = $this->db->conn->prepare($query);
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error getting skills: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Add skill (admin only)
     */
    public function addSkill($name, $category, $level, $percentage, $description = '', $icon = '') {
        if (!Authentication::isAdmin()) {
            $this->errors[] = "Hanya admin yang bisa menambah skill";
            return false;
        }

        try {
            $query = "INSERT INTO skills (name, category, level, percentage, description, icon) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$name, $category, $level, $percentage, $description, $icon]);
            return $this->db->conn->lastInsertId();
        } catch (Exception $e) {
            $this->errors[] = "Error adding skill: " . $e->getMessage();
            return false;
        }
    }

    // ==================== EXPERIENCES ====================

    /**
     * Get all experiences
     */
    public function getExperiences() {
        try {
            $query = "SELECT * FROM experiences ORDER BY start_date DESC";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error getting experiences: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Add experience (admin only)
     */
    public function addExperience($title, $company, $description, $start_date, $end_date, $location, $skills_used = []) {
        if (!Authentication::isAdmin()) {
            $this->errors[] = "Hanya admin yang bisa menambah experience";
            return false;
        }

        try {
            $is_current = ($end_date === null || $end_date === '') ? 1 : 0;
            $skills_json = json_encode($skills_used);
            
            $query = "INSERT INTO experiences (title, company, description, start_date, end_date, is_current, location, skills_used) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$title, $company, $description, $start_date, $end_date ?: null, $is_current, $location, $skills_json]);
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error adding experience: " . $e->getMessage();
            return false;
        }
    }

    // ==================== TESTIMONIALS ====================

    /**
     * Get featured testimonials
     */
    public function getFeaturedTestimonials() {
        try {
            $query = "SELECT * FROM testimonials WHERE is_featured = 1 AND is_approved = 1 ORDER BY created_at DESC LIMIT 6";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error getting testimonials: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Get all testimonials with pagination
     */
    public function getTestimonials($limit = 10, $offset = 0) {
        try {
            $query = "SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->errors[] = "Error getting testimonials: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Add testimonial
     */
    public function addTestimonial($client_name, $client_company, $rating, $testimonial_text, $client_image = '') {
        if (empty($client_name) || empty($testimonial_text) || !$rating) {
            $this->errors[] = "Semua field harus diisi";
            return false;
        }

        if ($rating < 1 || $rating > 5) {
            $this->errors[] = "Rating harus antara 1-5";
            return false;
        }

        try {
            $query = "INSERT INTO testimonials (client_name, client_company, rating, testimonial_text, client_image) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$client_name, $client_company, $rating, $testimonial_text, $client_image]);
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error adding testimonial: " . $e->getMessage();
            return false;
        }
    }

    // ==================== NEWSLETTER ====================

    /**
     * Subscribe to newsletter
     */
    public function subscribeNewsletter($email, $name = '') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Email tidak valid";
            return false;
        }

        try {
            // Check if already subscribed
            $check = "SELECT id FROM newsletter_subscribers WHERE email = ?";
            $stmt = $this->db->conn->prepare($check);
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $this->errors[] = "Email sudah terdaftar";
                return false;
            }

            $token = bin2hex(random_bytes(16));
            $query = "INSERT INTO newsletter_subscribers (email, name, verification_token) VALUES (?, ?, ?)";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$email, $name, $token]);
            
            // TODO: Send verification email
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error subscribing: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribeNewsletter($email) {
        try {
            $query = "UPDATE newsletter_subscribers SET is_active = 0, unsubscribed_at = CURRENT_TIMESTAMP WHERE email = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$email]);
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error unsubscribing: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get active subscribers count
     */
    public function getSubscribersCount() {
        try {
            $query = "SELECT COUNT(*) as count FROM newsletter_subscribers WHERE is_active = 1";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get errors
     */
    public function getErrors() {
        return $this->errors;
    }
}

// API Endpoints
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    $portfolio = new Portfolio();

    switch ($_GET['action']) {
        case 'get_skills':
            $category = $_GET['category'] ?? null;
            $skills = $portfolio->getSkills($category);
            echo json_encode(['success' => true, 'data' => $skills]);
            break;

        case 'get_experiences':
            $experiences = $portfolio->getExperiences();
            echo json_encode(['success' => true, 'data' => $experiences]);
            break;

        case 'get_testimonials':
            $type = $_GET['type'] ?? 'featured';
            if ($type === 'featured') {
                $testimonials = $portfolio->getFeaturedTestimonials();
            } else {
                $limit = intval($_GET['limit'] ?? 10);
                $offset = intval($_GET['offset'] ?? 0);
                $testimonials = $portfolio->getTestimonials($limit, $offset);
            }
            echo json_encode(['success' => true, 'data' => $testimonials]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action tidak dikenal']);
    }
    exit;
}

// POST endpoints
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $portfolio = new Portfolio();

    switch ($_POST['action']) {
        case 'subscribe_newsletter':
            if ($portfolio->subscribeNewsletter($_POST['email'] ?? '', $_POST['name'] ?? '')) {
                echo json_encode(['success' => true, 'message' => 'Subscription berhasil']);
            } else {
                echo json_encode(['success' => false, 'errors' => $portfolio->getErrors()]);
            }
            break;

        case 'unsubscribe_newsletter':
            if ($portfolio->unsubscribeNewsletter($_POST['email'] ?? '')) {
                echo json_encode(['success' => true, 'message' => 'Unsubscribe berhasil']);
            } else {
                echo json_encode(['success' => false, 'errors' => $portfolio->getErrors()]);
            }
            break;

        case 'add_testimonial':
            if ($portfolio->addTestimonial(
                $_POST['client_name'] ?? '',
                $_POST['client_company'] ?? '',
                intval($_POST['rating'] ?? 0),
                $_POST['testimonial_text'] ?? '',
                $_POST['client_image'] ?? ''
            )) {
                echo json_encode(['success' => true, 'message' => 'Testimonial submitted']);
            } else {
                echo json_encode(['success' => false, 'errors' => $portfolio->getErrors()]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action tidak dikenal']);
    }
    exit;
}
?>
