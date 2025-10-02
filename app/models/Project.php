<?php
/**
 * Modèle Project pour la gestion des projets
 * 
 * @author Keyne - Expert en IA & ML
 * @version 1.0.0
 */

class Project {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère tous les projets avec filtres optionnels
     */
    public function getAll($filters = []) {
        $whereClause = "WHERE status = 'published'";
        $params = [];
        
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $whereClause .= " AND category = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['featured'])) {
            $whereClause .= " AND featured = 1";
        }
        
        $orderBy = "ORDER BY featured DESC, created_at DESC";
        
        if (!empty($filters['limit'])) {
            $orderBy .= " LIMIT " . (int)$filters['limit'];
        }
        
        if (!empty($filters['offset'])) {
            $orderBy .= " OFFSET " . (int)$filters['offset'];
        }
        
        $query = "SELECT id, title, description, category, tags, image_url, github_url, demo_url, featured, views_count, likes_count, created_at 
                 FROM projects 
                 $whereClause 
                 $orderBy";
        
        $projects = $this->db->select($query, $params);
        
        // Traitement des données
        foreach ($projects as &$project) {
            $project['tags'] = json_decode($project['tags'], true) ?? [];
            $project['created_at_formatted'] = date('d/m/Y', strtotime($project['created_at']));
        }
        
        return $projects;
    }
    
    /**
     * Récupère un projet par son ID
     */
    public function getById($id) {
        $query = "SELECT * FROM projects WHERE id = ? AND status = 'published'";
        $project = $this->db->selectOne($query, [$id]);
        
        if ($project) {
            $project['tags'] = json_decode($project['tags'], true) ?? [];
            $project['created_at_formatted'] = date('d/m/Y', strtotime($project['created_at']));
        }
        
        return $project;
    }
    
    /**
     * Recherche dans les projets
     */
    public function search($searchTerm, $category = 'all', $limit = 10) {
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
            $project['created_at_formatted'] = date('d/m/Y', strtotime($project['created_at']));
        }
        
        return $projects;
    }
    
    /**
     * Incrémente le compteur de vues
     */
    public function incrementViews($id) {
        $sessionKey = "viewed_project_{$id}_" . date('Y-m-d');
        
        if (!isset($_SESSION[$sessionKey])) {
            $this->db->update("UPDATE projects SET views_count = views_count + 1 WHERE id = ?", [$id]);
            $_SESSION[$sessionKey] = true;
            return true;
        }
        
        return false;
    }
    
    /**
     * Toggle like sur un projet
     */
    public function toggleLike($id) {
        $sessionKey = "liked_project_$id";
        $hasLiked = isset($_SESSION[$sessionKey]);
        
        if ($hasLiked) {
            // Retirer le like
            $this->db->update("UPDATE projects SET likes_count = likes_count - 1 WHERE id = ?", [$id]);
            unset($_SESSION[$sessionKey]);
            $liked = false;
        } else {
            // Ajouter le like
            $this->db->update("UPDATE projects SET likes_count = likes_count + 1 WHERE id = ?", [$id]);
            $_SESSION[$sessionKey] = true;
            $liked = true;
        }
        
        // Récupérer le nouveau nombre de likes
        $project = $this->db->selectOne("SELECT likes_count FROM projects WHERE id = ?", [$id]);
        
        return [
            'liked' => $liked,
            'likes_count' => (int)$project['likes_count']
        ];
    }
    
    /**
     * Récupère les statistiques des projets
     */
    public function getStats() {
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
        
        return $stats;
    }
    
    /**
     * Crée un nouveau projet
     */
    public function create($data) {
        $query = "INSERT INTO projects (title, description, content, category, tags, image_url, github_url, demo_url, featured, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($query, [
            $data['title'],
            $data['description'],
            $data['content'] ?? '',
            $data['category'],
            json_encode($data['tags'] ?? []),
            $data['image_url'] ?? '',
            $data['github_url'] ?? '',
            $data['demo_url'] ?? '',
            $data['featured'] ?? 0,
            $data['status'] ?? 'draft'
        ]);
    }
    
    /**
     * Met à jour un projet
     */
    public function update($id, $data) {
        $query = "UPDATE projects SET 
                 title = ?, description = ?, content = ?, category = ?, tags = ?, 
                 image_url = ?, github_url = ?, demo_url = ?, featured = ?, status = ?,
                 updated_at = CURRENT_TIMESTAMP
                 WHERE id = ?";
        
        return $this->db->update($query, [
            $data['title'],
            $data['description'],
            $data['content'] ?? '',
            $data['category'],
            json_encode($data['tags'] ?? []),
            $data['image_url'] ?? '',
            $data['github_url'] ?? '',
            $data['demo_url'] ?? '',
            $data['featured'] ?? 0,
            $data['status'] ?? 'draft',
            $id
        ]);
    }
    
    /**
     * Supprime un projet
     */
    public function delete($id) {
        return $this->db->delete("DELETE FROM projects WHERE id = ?", [$id]);
    }
    
    /**
     * Compte le nombre total de projets
     */
    public function count($category = 'all') {
        if ($category === 'all') {
            $result = $this->db->selectOne("SELECT COUNT(*) as count FROM projects WHERE status = 'published'");
        } else {
            $result = $this->db->selectOne("SELECT COUNT(*) as count FROM projects WHERE status = 'published' AND category = ?", [$category]);
        }
        
        return (int)($result['count'] ?? 0);
    }
}
?>
