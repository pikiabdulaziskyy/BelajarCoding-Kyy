<?php
/**
 * Database Connection Handler
 * 
 * File untuk menangani koneksi database MySQL
 * Menggunakan MySQLi dengan prepared statements untuk security
 * 
 * @package Portfolio
 * @version 1.0.0
 */

// Error handling
error_reporting(E_ALL);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_kyy');
define('DB_PORT', 3306);

// Create connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if ($mysqli->connect_error) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => 'Could not connect to database: ' . $mysqli->connect_error,
        'timestamp' => date('Y-m-d H:i:s')
    ]));
}

// Set charset to UTF-8
$mysqli->set_charset('utf8mb4');

// Set timezone
date_default_timezone_set('Asia/Jakarta');

/**
 * Class Database
 * 
 * Wrapper untuk MySQLi dengan prepared statements
 * Menghandle semua database operations
 */
class Database {
    private $conn;
    private $last_error;
    private $last_query;
    
    public function __construct($mysqli) {
        $this->conn = $mysqli;
    }
    
    /**
     * Execute query dengan prepared statement
     * 
     * @param string $query SQL query dengan placeholders (?)
     * @param array $params Parameter values
     * @param string $types Parameter types (s=string, i=integer, d=double, b=blob)
     * @return object|array|bool Result atau error
     */
    public function query($query, $params = [], $types = '') {
        try {
            $this->last_query = $query;
            
            if (empty($params)) {
                $result = $this->conn->query($query);
                if (!$result) {
                    throw new Exception($this->conn->error);
                }
                return $result;
            }
            
            // Prepared statement
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception($this->conn->error);
            }
            
            // Auto-detect types jika tidak diberikan
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            
            // Bind parameters
            $bind_params = array_merge([$types], $params);
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($bind_params));
            
            // Execute
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
            
            $result = $stmt->get_result();
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Get single row
     */
    public function fetchOne($query, $params = [], $types = '') {
        $result = $this->query($query, $params, $types);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Get multiple rows
     */
    public function fetchAll($query, $params = [], $types = '') {
        $result = $this->query($query, $params, $types);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    /**
     * Insert record
     */
    public function insert($table, $data) {
        $keys = array_keys($data);
        $values = array_values($data);
        
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $columns = implode(',', $keys);
        
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $types = $this->getTypes($values);
        $result = $this->query($query, $values, $types);
        
        if ($result) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Update record
     */
    public function update($table, $data, $where, $where_params = []) {
        $sets = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $sets[] = "$key = ?";
            $values[] = $value;
        }
        
        $set_string = implode(',', $sets);
        $query = "UPDATE $table SET $set_string WHERE $where";
        
        $all_params = array_merge($values, $where_params);
        $types = $this->getTypes($all_params);
        
        return $this->query($query, $all_params, $types) !== false;
    }
    
    /**
     * Delete record
     */
    public function delete($table, $where, $params = []) {
        $query = "DELETE FROM $table WHERE $where";
        $types = $this->getTypes($params);
        return $this->query($query, $params, $types) !== false;
    }
    
    /**
     * Get last insert ID
     */
    public function lastId() {
        return $this->conn->insert_id;
    }
    
    /**
     * Get last error
     */
    public function getError() {
        return $this->last_error ?: $this->conn->error;
    }
    
    /**
     * Get last query
     */
    public function getLastQuery() {
        return $this->last_query;
    }
    
    /**
     * Escape string
     */
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    /**
     * Get row count
     */
    public function count($table, $where = '1=1', $params = []) {
        $query = "SELECT COUNT(*) as total FROM $table WHERE $where";
        $result = $this->fetchOne($query, $params);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->conn->begin_transaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->conn->rollback();
    }
    
    /**
     * Helper: Auto-detect parameter types
     */
    private function getTypes($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 's';
            }
        }
        return $types;
    }
    
    /**
     * Helper: Create reference values for bind_param
     * 
     * bind_param memerlukan reference values, bukan langsung array
     * Function ini mengconvert array ke reference values
     */
    private function refValues($arr) {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }
}

// Create Database instance
$db = new Database($mysqli);

// Helper function: Get database instance
function getDatabase() {
    global $db;
    return $db;
}

/**
 * Close database connection when script ends
 */
register_shutdown_function(function() {
    global $mysqli;
    if ($mysqli) {
        $mysqli->close();
    }
});

?>
