<?php
/**
 * Contrôleur pour la gestion des projets
 * 
 * @author Keyne - Expert en IA & ML
 * @version 1.0.0
 */

require_once '../config/config.php';

class ProjectController {
    private $db;
    private $projectModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->projectModel = new Project();
    }
    
    /**
     * Récupère tous les projets
     */
    public function getAllProjects() {
        header('Content-Type: application/json');
        
        try {
            $filters = [
                'category' => $_GET['category'] ?? 'all',
                'limit' => (int)($_GET['limit'] ?? 20),
                'offset' => (int)($_GET['offset'] ?? 0)
            ];
            
            $projects = $this->projectModel->getAll($filters);
            
            echo json_encode([
                'success' => true,
                'data' => $projects,
                'total' => $this->projectModel->count($filters['category'])
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur ProjectController::getAllProjects: " . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des projets'
            ]);
        }
    }
    
    /**
     * Récupère un projet par son ID
     */
    public function getProject($id) {
        header('Content-Type: application/json');
        
        try {
            $query = "SELECT * FROM projects WHERE id = ? AND status = 'published'";
            $project = $this->db->selectOne($query, [$id]);
            
            if (!$project) {
                throw new Exception('Projet non trouvé');
            }
            
            // Incrémenter le compteur de vues
            $this->incrementViews($id);
            
            // Traitement des données
            $project['tags'] = json_decode($project['tags'], true) ?? [];
            $project['created_at'] = date('d/m/Y', strtotime($project['created_at']));
            
            echo json_encode([
                'success' => true,
                'data' => $project
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur ProjectController::getProject: " . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Récupère les projets en vedette
     */
    public function getFeaturedProjects() {
        header('Content-Type: application/json');
        
        try {
            $query = "SELECT id, title, description, category, tags, image_url, github_url, demo_url, views_count, likes_count, created_at 
                     FROM projects 
                     WHERE status = 'published' AND featured = 1 
                     ORDER BY created_at DESC 
                     LIMIT 6";
            
            $projects = $this->db->select($query);
            
            // Traitement des données
            foreach ($projects as &$project) {
                $project['tags'] = json_decode($project['tags'], true) ?? [];
                $project['created_at'] = date('d/m/Y', strtotime($project['created_at']));
            }
            
            echo json_encode([
                'success' => true,
                'data' => $projects
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur ProjectController::getFeaturedProjects: " . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des projets en vedette'
            ]);
        }
    }
    
    /**
     * Recherche dans les projets
     */
    public function searchProjects() {
        header('Content-Type: application/json');
        
        try {
            $searchTerm = $_GET['q'] ?? '';
            $category = $_GET['category'] ?? 'all';
            $limit = (int)($_GET['limit'] ?? 10);
            
            if (strlen($searchTerm) < 2) {
                throw new Exception('Le terme de recherche doit contenir au moins 2 caractères');
            }
            
            // Construction de la requête de recherche
            $whereClause = "WHERE status = 'published' AND (title LIKE ? OR description LIKE ? OR JSON_SEARCH(tags, 'one', ?) IS NOT NULL)";
            $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
            
            if ($category !== 'all') {
                $whereClause .= " AND category = ?";
                $params[] = $category;
            }
            
            $query = "SELECT id, title, description, category, tags, image_url, github_url, demo_url, views_count, likes_count, created_at 
                     FROM projects 
                     $whereClause 
                     ORDER BY 
                        CASE WHEN title LIKE ? THEN 1 ELSE 2 END,
                        views_count DESC,
                        created_at DESC 
                     LIMIT ?";
            
            $params[] = "%$searchTerm%";
            $params[] = $limit;
            
            $projects = $this->db->select($query, $params);
            
            // Traitement des données
            foreach ($projects as &$project) {
                $project['tags'] = json_decode($project['tags'], true) ?? [];
                $project['created_at'] = date('d/m/Y', strtotime($project['created_at']));
                
                // Mise en évidence du terme recherché
                $project['title'] = $this->highlightSearchTerm($project['title'], $searchTerm);
                $project['description'] = $this->highlightSearchTerm($project['description'], $searchTerm);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $projects,
                'search_term' => $searchTerm,
                'total' => count($projects)
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur ProjectController::searchProjects: " . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Ajouter/retirer un like sur un projet
     */
    public function toggleLike() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }
            
            $projectId = (int)($_POST['project_id'] ?? 0);
            $clientIP = $this->getClientIP();
            
            if (!$projectId) {
                throw new Exception('ID de projet invalide');
            }
            
            // Vérifier si le projet existe
            $project = $this->db->selectOne("SELECT id, likes_count FROM projects WHERE id = ?", [$projectId]);
            if (!$project) {
                throw new Exception('Projet non trouvé');
            }
            
            // Vérifier si l'IP a déjà liké (simple vérification, en production utiliser un système plus robuste)
            $sessionKey = "liked_project_$projectId";
            $hasLiked = isset($_SESSION[$sessionKey]);
            
            if ($hasLiked) {
                // Retirer le like
                $this->db->update("UPDATE projects SET likes_count = likes_count - 1 WHERE id = ?", [$projectId]);
                unset($_SESSION[$sessionKey]);
                $liked = false;
            } else {
                // Ajouter le like
                $this->db->update("UPDATE projects SET likes_count = likes_count + 1 WHERE id = ?", [$projectId]);
                $_SESSION[$sessionKey] = true;
                $liked = true;
            }
            
            // Récupérer le nouveau nombre de likes
            $updatedProject = $this->db->selectOne("SELECT likes_count FROM projects WHERE id = ?", [$projectId]);
            
            echo json_encode([
                'success' => true,
                'liked' => $liked,
                'likes_count' => (int)$updatedProject['likes_count']
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur ProjectController::toggleLike: " . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Récupère les statistiques des projets
     */
    public function getProjectStats() {
        header('Content-Type: application/json');
        
        try {
            $stats = [
                'total_projects' => 0,
                'total_views' => 0,
                'total_likes' => 0,
                'categories' => [],
                'popular_tags' => []
            ];
            
            // Statistiques générales
            $generalStats = $this->db->selectOne("
                SELECT 
                    COUNT(*) as total_projects,
                    COALESCE(SUM(views_count), 0) as total_views,
                    COALESCE(SUM(likes_count), 0) as total_likes
                FROM projects 
                WHERE status = 'published'
            ");
            
            if ($generalStats) {
                $stats['total_projects'] = (int)$generalStats['total_projects'];
                $stats['total_views'] = (int)$generalStats['total_views'];
                $stats['total_likes'] = (int)$generalStats['total_likes'];
            }
            
            // Statistiques par catégorie
            $categoryStats = $this->db->select("
                SELECT 
                    category,
                    COUNT(*) as count,
                    COALESCE(SUM(views_count), 0) as views,
                    COALESCE(SUM(likes_count), 0) as likes
                FROM projects 
                WHERE status = 'published' 
                GROUP BY category 
                ORDER BY count DESC
            ");
            
            foreach ($categoryStats as $cat) {
                $stats['categories'][] = [
                    'name' => $cat['category'],
                    'count' => (int)$cat['count'],
                    'views' => (int)$cat['views'],
                    'likes' => (int)$cat['likes']
                ];
            }
            
            // Tags populaires (extraction des tags JSON)
            $projects = $this->db->select("SELECT tags FROM projects WHERE status = 'published' AND tags IS NOT NULL");
            $tagCounts = [];
            
            foreach ($projects as $project) {
                $tags = json_decode($project['tags'], true);
                if (is_array($tags)) {
                    foreach ($tags as $tag) {
                        $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                    }
                }
            }
            
            arsort($tagCounts);
            $stats['popular_tags'] = array_slice($tagCounts, 0, 10);
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur ProjectController::getProjectStats: " . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ]);
        }
    }
    
    /**
     * Méthodes utilitaires privées
     */
    
    private function getTotalProjects($category = 'all') {
        if ($category === 'all') {
            $result = $this->db->selectOne("SELECT COUNT(*) as count FROM projects WHERE status = 'published'");
        } else {
            $result = $this->db->selectOne("SELECT COUNT(*) as count FROM projects WHERE status = 'published' AND category = ?", [$category]);
        }
        
        return (int)($result['count'] ?? 0);
    }
    
    private function incrementViews($projectId) {
        // Vérifier si cette IP a déjà vu ce projet aujourd'hui (éviter le spam de vues)
        $sessionKey = "viewed_project_{$projectId}_" . date('Y-m-d');
        
        if (!isset($_SESSION[$sessionKey])) {
            $this->db->update("UPDATE projects SET views_count = views_count + 1 WHERE id = ?", [$projectId]);
            $_SESSION[$sessionKey] = true;
        }
    }
    
    private function highlightSearchTerm($text, $searchTerm) {
        return preg_replace('/(' . preg_quote($searchTerm, '/') . ')/i', '<mark>$1</mark>', $text);
    }
    
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

// Routage simple pour l'API
if (basename($_SERVER['PHP_SELF']) === 'ProjectController.php') {
    $controller = new ProjectController();
    $action = $_GET['action'] ?? 'getAllProjects';
    
    switch ($action) {
        case 'getAllProjects':
            $controller->getAllProjects();
            break;
            
        case 'getProject':
            $id = (int)($_GET['id'] ?? 0);
            if ($id) {
                $controller->getProject($id);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID requis']);
            }
            break;
            
        case 'getFeaturedProjects':
            $controller->getFeaturedProjects();
            break;
            
        case 'searchProjects':
            $controller->searchProjects();
            break;
            
        case 'toggleLike':
            $controller->toggleLike();
            break;
            
        case 'getProjectStats':
            $controller->getProjectStats();
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
            break;
    }
}
?>
