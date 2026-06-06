<?php
/**
 * Helper Functions
 * Fungsi-fungsi utility untuk aplikasi
 */

// ===== STRING FUNCTIONS =====
function str_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function truncate($text, $limit = 100, $suffix = '...') {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . $suffix;
    }
    return $text;
}

function capitalize($text) {
    return ucfirst(strtolower($text));
}

// ===== DATE FUNCTIONS =====
function format_date($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function time_ago($date) {
    $timestamp = strtotime($date);
    $time_diff = time() - $timestamp;
    
    if ($time_diff < 60) {
        return 'baru saja';
    } elseif ($time_diff < 3600) {
        return floor($time_diff / 60) . ' menit lalu';
    } elseif ($time_diff < 86400) {
        return floor($time_diff / 3600) . ' jam lalu';
    } elseif ($time_diff < 604800) {
        return floor($time_diff / 86400) . ' hari lalu';
    } else {
        return date('d M Y', $timestamp);
    }
}

// ===== ARRAY FUNCTIONS =====
function array_by_key(&$array, $key) {
    $result = [];
    foreach ($array as $item) {
        if (isset($item[$key])) {
            $result[$item[$key]] = $item;
        }
    }
    return $result;
}

function array_contains($array, $value) {
    return in_array($value, $array);
}

// ===== FILE FUNCTIONS =====
function file_read($path) {
    if (!file_exists($path)) return false;
    return file_get_contents($path);
}

function file_write($path, $content) {
    return file_put_contents($path, $content);
}

function file_append($path, $content) {
    return file_put_contents($path, $content, FILE_APPEND);
}

function file_delete($path) {
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

function dir_create($path, $mode = 0755) {
    if (!is_dir($path)) {
        return mkdir($path, $mode, true);
    }
    return true;
}

// ===== JSON FUNCTIONS =====
function json_read($file) {
    if (!file_exists($file)) return [];
    $content = file_get_contents($file);
    return json_decode($content, true) ?? [];
}

function json_write($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function json_append($file, $item) {
    $data = json_read($file);
    $data[] = $item;
    return json_write($file, $data);
}

// ===== VALIDATION FUNCTIONS =====
function validate_required($value) {
    return !empty(trim($value));
}

function validate_min_length($value, $min) {
    return strlen($value) >= $min;
}

function validate_max_length($value, $max) {
    return strlen($value) <= $max;
}

function validate_phone($phone) {
    return preg_match('/^(\+62|0)[0-9]{9,12}$/', str_replace([' ', '-'], '', $phone));
}

function validate_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// ===== SECURITY FUNCTIONS =====
function encrypt($data) {
    // Simple encryption (gunakan OpenSSL di production)
    return base64_encode($data);
}

function decrypt($data) {
    // Simple decryption (gunakan OpenSSL di production)
    return base64_decode($data);
}

function generate_token($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ===== MATH FUNCTIONS =====
function percentage($current, $total) {
    return ($current / $total) * 100;
}

function round_up($value, $precision = 0) {
    $multiplier = 10 ** $precision;
    return ceil($value * $multiplier) / $multiplier;
}

// ===== API FUNCTIONS =====
function api_success($data, $message = 'Success', $code = 200) {
    http_response_code($code);
    return json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function api_error($message, $code = 400, $errors = []) {
    http_response_code($code);
    return json_encode([
        'success' => false,
        'message' => $message,
        'errors' => $errors,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

// ===== PAGINATION =====
function paginate($items, $page = 1, $per_page = 10) {
    $total = count($items);
    $pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;
    
    return [
        'items' => array_slice($items, $offset, $per_page),
        'total' => $total,
        'page' => $page,
        'pages' => $pages,
        'per_page' => $per_page
    ];
}

// ===== CACHE FUNCTIONS (Simple) =====
class SimpleCache {
    private static $cache = [];
    private static $ttl = [];

    public static function set($key, $value, $expiry = 3600) {
        self::$cache[$key] = $value;
        self::$ttl[$key] = time() + $expiry;
    }

    public static function get($key) {
        if (!isset(self::$cache[$key])) {
            return null;
        }
        
        if (isset(self::$ttl[$key]) && time() > self::$ttl[$key]) {
            unset(self::$cache[$key]);
            unset(self::$ttl[$key]);
            return null;
        }
        
        return self::$cache[$key];
    }

    public static function forget($key) {
        unset(self::$cache[$key]);
        unset(self::$ttl[$key]);
    }

    public static function flush() {
        self::$cache = [];
        self::$ttl = [];
    }
}
?>