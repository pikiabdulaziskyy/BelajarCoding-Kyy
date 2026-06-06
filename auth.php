<?php
/**
 * Authentication & Authorization Handler
 * Handles login, register, session management
 */

require_once 'config.php';
require_once 'functions.php';

class Authentication {
    private $db;
    private $errors = [];
    private $success = [];

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Register user baru
     */
    public function register($username, $email, $password, $password_confirm, $full_name) {
        // Validasi
        if (empty($username) || empty($email) || empty($password)) {
            $this->errors[] = "Semua field harus diisi";
            return false;
        }

        if ($password !== $password_confirm) {
            $this->errors[] = "Password tidak cocok";
            return false;
        }

        if (strlen($password) < 6) {
            $this->errors[] = "Password minimal 6 karakter";
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Email tidak valid";
            return false;
        }

        // Cek username/email sudah ada
        if ($this->userExists($username, $email)) {
            return false;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert ke database
        try {
            $query = "INSERT INTO users (username, email, password, full_name, role) 
                      VALUES (?, ?, ?, ?, 'user')";
            
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$username, $email, $hashed_password, $full_name]);

            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Error registrasi: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            $this->errors[] = "Email dan password harus diisi";
            return false;
        }

        try {
            $query = "SELECT id, username, email, password, role, is_active FROM users WHERE email = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->errors[] = "Email tidak ditemukan";
                return false;
            }

            if (!$user['is_active']) {
                $this->errors[] = "Akun tidak aktif";
                return false;
            }

            if (!password_verify($password, $user['password'])) {
                $this->errors[] = "Password salah";
                return false;
            }

            // Update last login
            $update = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->db->conn->prepare($update);
            $stmt->execute([$user['id']]);

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            $this->success[] = "Login berhasil!";
            return true;

        } catch (Exception $e) {
            $this->errors[] = "Error login: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    /**
     * Check if user sudah login
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Check if user adalah admin
     */
    public static function isAdmin() {
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Get current user data
     */
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    /**
     * Check if user atau email sudah ada
     */
    private function userExists($username, $email) {
        try {
            $query = "SELECT id FROM users WHERE username = ? OR email = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $this->errors[] = "Username atau email sudah terdaftar";
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->errors[] = "Error checking user: " . $e->getMessage();
            return true;
        }
    }

    /**
     * Get errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get success messages
     */
    public function getSuccess() {
        return $this->success;
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

// Handle login/register form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $auth = new Authentication();

    if ($action === 'login') {
        if (Authentication::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            if ($auth->login($_POST['email'] ?? '', $_POST['password'] ?? '')) {
                echo json_encode(['success' => true, 'message' => 'Login berhasil']);
            } else {
                echo json_encode(['success' => false, 'errors' => $auth->getErrors()]);
            }
        } else {
            echo json_encode(['success' => false, 'errors' => ['CSRF token tidak valid']]);
        }
    } elseif ($action === 'register') {
        if (Authentication::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            if ($auth->register(
                $_POST['username'] ?? '',
                $_POST['email'] ?? '',
                $_POST['password'] ?? '',
                $_POST['password_confirm'] ?? '',
                $_POST['full_name'] ?? ''
            )) {
                echo json_encode(['success' => true, 'message' => 'Registrasi berhasil']);
            } else {
                echo json_encode(['success' => false, 'errors' => $auth->getErrors()]);
            }
        } else {
            echo json_encode(['success' => false, 'errors' => ['CSRF token tidak valid']]);
        }
    }
    exit;
}
?>
