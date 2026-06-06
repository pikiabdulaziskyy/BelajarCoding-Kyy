<?php
/**
 * Configuration File
 * Konfigurasi umum aplikasi
 */

// ===== ENVIRONMENT =====
define('ENVIRONMENT', 'development'); // production, development
define('DEBUG_MODE', ENVIRONMENT === 'development');

// ===== DATABASE CONFIG (Simulasi) =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_db');

// ===== APLIKASI CONFIG =====
define('APP_NAME', 'Portfolio Kyy');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Piki Abdul Azis');

// ===== CONTACT CONFIG =====
define('ADMIN_EMAIL', 'admin@example.com');
define('ADMIN_NAME', 'Admin Portfolio');
define('CONTACT_EMAIL_ENABLED', false); // Set to true jika ingin send email

// ===== FILE STORAGE =====
define('DATA_DIR', __DIR__ . '/data/');
define('LOGS_DIR', __DIR__ . '/logs/');
define('UPLOADS_DIR', __DIR__ . '/uploads/');

// Create directories if not exist
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!is_dir(LOGS_DIR)) mkdir(LOGS_DIR, 0755, true);
if (!is_dir(UPLOADS_DIR)) mkdir(UPLOADS_DIR, 0755, true);

// ===== TIMEZONE =====
date_default_timezone_set('Asia/Jakarta');

// ===== ERROR HANDLING =====
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

// ===== HELPER FUNCTIONS =====
function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

function log_message($message, $type = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $log_file = LOGS_DIR . date('Y-m-d') . '.log';
    $log_entry = "[$timestamp] [$type] $message\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

function response_json($success, $message, $data = [], $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function sanitize($input) {
    return htmlspecialchars(stripslashes(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function get_file_size($bytes) {
    $sizes = ['B', 'KB', 'MB', 'GB'];
    if ($bytes == 0) return 0;
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// ===== LOAD FUNCTIONS =====
require_once __DIR__ . '/functions.php';
?>