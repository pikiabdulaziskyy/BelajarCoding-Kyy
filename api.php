<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// ===== DATABASE SIMULASI =====
class Database {
    private $projects = [
        [
            'id' => 1,
            'title' => 'Website Portofolio',
            'description' => 'Website personal untuk menampilkan proyek, pengalaman, dan kontak.',
            'image' => 'portfolio.jpg',
            'technologies' => ['HTML', 'CSS', 'JavaScript'],
            'link' => 'https://example.com/portfolio',
            'rating' => 5,
            'year' => 2026
        ],
        [
            'id' => 2,
            'title' => 'Landing Page',
            'description' => 'Halaman promosi dengan desain bersih dan tombol CTA yang efektif.',
            'image' => 'landing.jpg',
            'technologies' => ['HTML', 'CSS', 'JavaScript', 'PHP'],
            'link' => 'https://example.com/landing',
            'rating' => 4,
            'year' => 2026
        ],
        [
            'id' => 3,
            'title' => 'Blog Responsif',
            'description' => 'Blog sederhana yang dapat dibaca dengan nyaman di perangkat apa pun.',
            'image' => 'blog.jpg',
            'technologies' => ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL'],
            'link' => 'https://example.com/blog',
            'rating' => 5,
            'year' => 2026
        ]
    ];

    private $messages = [];
    private $dataFile = 'messages.json';

    public function __construct() {
        if (file_exists($this->dataFile)) {
            $this->messages = json_decode(file_get_contents($this->dataFile), true) ?? [];
        }
    }

    public function getProjects() {
        return $this->projects;
    }

    public function getProjectById($id) {
        foreach ($this->projects as $project) {
            if ($project['id'] == $id) {
                return $project;
            }
        }
        return null;
    }

    public function saveMessage($data) {
        $message = [
            'id' => count($this->messages) + 1,
            'name' => $this->sanitize($data['name'] ?? ''),
            'email' => $this->sanitize($data['email'] ?? ''),
            'subject' => $this->sanitize($data['subject'] ?? ''),
            'message' => $this->sanitize($data['message'] ?? ''),
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'new'
        ];

        $this->messages[] = $message;
        file_put_contents($this->dataFile, json_encode($this->messages, JSON_PRETTY_PRINT));
        
        return $message;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function sanitize($data) {
        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

// ===== RESPONSE HELPER =====
class ApiResponse {
    public static function success($data, $message = 'Success', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }

    public static function error($message, $code = 400, $errors = []) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}

// ===== ROUTER =====
$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($request) {
        case 'get-projects':
            $projects = $db->getProjects();
            ApiResponse::success($projects, 'Projects retrieved successfully');
            break;

        case 'get-project':
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            if (!$id) {
                ApiResponse::error('Project ID is required', 400);
            }
            $project = $db->getProjectById($id);
            if (!$project) {
                ApiResponse::error('Project not found', 404);
            }
            ApiResponse::success($project, 'Project retrieved successfully');
            break;

        case 'send-message':
            if ($method !== 'POST') {
                ApiResponse::error('Method not allowed', 405);
            }

            $input = json_decode(file_get_contents('php://input'), true);

            // Validasi
            $errors = [];
            if (empty($input['name'])) {
                $errors['name'] = 'Nama tidak boleh kosong';
            }
            if (empty($input['email'])) {
                $errors['email'] = 'Email tidak boleh kosong';
            } elseif (!$db->validateEmail($input['email'])) {
                $errors['email'] = 'Format email tidak valid';
            }
            if (empty($input['subject'])) {
                $errors['subject'] = 'Subjek tidak boleh kosong';
            }
            if (empty($input['message'])) {
                $errors['message'] = 'Pesan tidak boleh kosong';
            }

            if (!empty($errors)) {
                ApiResponse::error('Validasi gagal', 422, $errors);
            }

            $message = $db->saveMessage($input);
            ApiResponse::success($message, 'Pesan berhasil dikirim', 201);
            break;

        case 'get-messages':
            $messages = $db->getMessages();
            ApiResponse::success($messages, 'Messages retrieved successfully');
            break;

        case 'stats':
            $stats = [
                'total_projects' => count($db->getProjects()),
                'total_messages' => count($db->getMessages()),
                'average_rating' => 4.7,
                'years_active' => 2,
                'languages' => ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL']
            ];
            ApiResponse::success($stats, 'Statistics retrieved successfully');
            break;

        case 'ping':
            ApiResponse::success(['status' => 'ok'], 'Server is running');
            break;

        default:
            ApiResponse::error('Action not found', 404);
    }
} catch (Exception $e) {
    ApiResponse::error('Server error: ' . $e->getMessage(), 500);
}
?>