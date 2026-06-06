<?php
/**
 * Search & Filter Handler
 * Advanced search across projects, blog posts, skills
 */

require_once 'config.php';
require_once 'functions.php';

class Search {
    private $db;
    private $errors = [];

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Search projects
     */
    public function searchProjects($keyword, $technology = null, $year = null, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT * FROM projects WHERE title LIKE ? OR description LIKE ?";
            $params = ["%$keyword%", "%$keyword%"];

            if ($technology) {
                $query .= " AND technologies LIKE ?";
                $params[] = "%$technology%";
            }

            if ($year) {
                $query .= " AND year = ?";
                $params[] = $year;
            }

            $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->conn->prepare($query);
            $stmt->execute($params);
            
            return [
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $this->countProjects($keyword, $technology, $year)
            ];
        } catch (Exception $e) {
            $this->errors[] = "Error searching projects: " . $e->getMessage();
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Search blog posts
     */
    public function searchBlogPosts($keyword, $category = null, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT bp.*, u.username as author, bc.name as category 
                      FROM blog_posts bp 
                      LEFT JOIN users u ON bp.author_id = u.id 
                      LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                      WHERE bp.is_published = 1 AND (bp.title LIKE ? OR bp.content LIKE ?)";
            
            $params = ["%$keyword%", "%$keyword%"];

            if ($category) {
                $query .= " AND bp.category_id = ?";
                $params[] = $category;
            }

            $query .= " ORDER BY bp.published_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->conn->prepare($query);
            $stmt->execute($params);
            
            return [
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $this->countBlogPosts($keyword, $category)
            ];
        } catch (Exception $e) {
            $this->errors[] = "Error searching blog posts: " . $e->getMessage();
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Filter projects by technology
     */
    public function filterByTechnology($technology, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT * FROM projects WHERE technologies LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute(["%$technology%", $limit, $offset]);
            
            return [
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $this->countProjectsByTech($technology)
            ];
        } catch (Exception $e) {
            $this->errors[] = "Error filtering projects: " . $e->getMessage();
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Filter projects by year
     */
    public function filterByYear($year, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT * FROM projects WHERE year = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$year, $limit, $offset]);
            
            return [
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $this->countProjectsByYear($year)
            ];
        } catch (Exception $e) {
            $this->errors[] = "Error filtering projects: " . $e->getMessage();
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Get all available technologies
     */
    public function getAllTechnologies() {
        try {
            $query = "SELECT DISTINCT technologies FROM projects WHERE technologies IS NOT NULL";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute();
            
            $techs = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $technologies = json_decode($row['technologies'], true);
                if (is_array($technologies)) {
                    $techs = array_merge($techs, $technologies);
                }
            }
            
            return array_unique($techs);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get all available years
     */
    public function getAllYears() {
        try {
            $query = "SELECT DISTINCT year FROM projects WHERE year IS NOT NULL ORDER BY year DESC";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute();
            
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'year');
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Global search across all content
     */
    public function globalSearch($keyword, $limit = 10) {
        try {
            $results = [
                'projects' => [],
                'blog_posts' => [],
                'skills' => []
            ];

            // Search projects
            $query = "SELECT 'project' as type, id, title, description as content, null as slug FROM projects 
                      WHERE title LIKE ? OR description LIKE ? 
                      LIMIT ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute(["%$keyword%", "%$keyword%", $limit]);
            $results['projects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Search blog posts
            $query = "SELECT 'post' as type, id, title, content, slug FROM blog_posts 
                      WHERE is_published = 1 AND (title LIKE ? OR content LIKE ?) 
                      LIMIT ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute(["%$keyword%", "%$keyword%", $limit]);
            $results['blog_posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Search skills
            $query = "SELECT 'skill' as type, id, name as title, description as content, null as slug FROM skills 
                      WHERE name LIKE ? OR description LIKE ? 
                      LIMIT ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute(["%$keyword%", "%$keyword%", $limit]);
            $results['skills'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (Exception $e) {
            $this->errors[] = "Error in global search: " . $e->getMessage();
            return ['projects' => [], 'blog_posts' => [], 'skills' => []];
        }
    }

    /**
     * Count projects
     */
    private function countProjects($keyword, $technology = null, $year = null) {
        try {
            $query = "SELECT COUNT(*) as count FROM projects WHERE title LIKE ? OR description LIKE ?";
            $params = ["%$keyword%", "%$keyword%"];

            if ($technology) {
                $query .= " AND technologies LIKE ?";
                $params[] = "%$technology%";
            }

            if ($year) {
                $query .= " AND year = ?";
                $params[] = $year;
            }

            $stmt = $this->db->conn->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Count blog posts
     */
    private function countBlogPosts($keyword, $category = null) {
        try {
            $query = "SELECT COUNT(*) as count FROM blog_posts 
                      WHERE is_published = 1 AND (title LIKE ? OR content LIKE ?)";
            $params = ["%$keyword%", "%$keyword%"];

            if ($category) {
                $query .= " AND category_id = ?";
                $params[] = $category;
            }

            $stmt = $this->db->conn->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Count projects by technology
     */
    private function countProjectsByTech($technology) {
        try {
            $query = "SELECT COUNT(*) as count FROM projects WHERE technologies LIKE ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute(["%$technology%"]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Count projects by year
     */
    private function countProjectsByYear($year) {
        try {
            $query = "SELECT COUNT(*) as count FROM projects WHERE year = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->execute([$year]);
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
    $search = new Search();

    switch ($_GET['action']) {
        case 'search':
            $type = $_GET['type'] ?? 'all';
            $keyword = $_GET['q'] ?? '';
            $limit = intval($_GET['limit'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);

            if (empty($keyword)) {
                echo json_encode(['success' => false, 'message' => 'Keyword harus diisi']);
                break;
            }

            if ($type === 'all') {
                $results = $search->globalSearch($keyword, $limit);
                echo json_encode(['success' => true, 'data' => $results]);
            } elseif ($type === 'projects') {
                $results = $search->searchProjects($keyword, null, null, $limit, $offset);
                echo json_encode(['success' => true, 'data' => $results]);
            } elseif ($type === 'blog') {
                $results = $search->searchBlogPosts($keyword, null, $limit, $offset);
                echo json_encode(['success' => true, 'data' => $results]);
            }
            break;

        case 'filter_by_tech':
            $tech = $_GET['technology'] ?? '';
            $limit = intval($_GET['limit'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);
            
            if (empty($tech)) {
                echo json_encode(['success' => false, 'message' => 'Technology harus diisi']);
                break;
            }

            $results = $search->filterByTechnology($tech, $limit, $offset);
            echo json_encode(['success' => true, 'data' => $results]);
            break;

        case 'filter_by_year':
            $year = intval($_GET['year'] ?? 0);
            $limit = intval($_GET['limit'] ?? 10);
            $offset = intval($_GET['offset'] ?? 0);
            
            if ($year === 0) {
                echo json_encode(['success' => false, 'message' => 'Year harus valid']);
                break;
            }

            $results = $search->filterByYear($year, $limit, $offset);
            echo json_encode(['success' => true, 'data' => $results]);
            break;

        case 'get_technologies':
            $techs = $search->getAllTechnologies();
            echo json_encode(['success' => true, 'data' => $techs]);
            break;

        case 'get_years':
            $years = $search->getAllYears();
            echo json_encode(['success' => true, 'data' => $years]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action tidak dikenal']);
    }
    exit;
}
?>
