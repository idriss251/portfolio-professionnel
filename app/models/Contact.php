<?php
/**
 * Modèle Contact pour la gestion des messages de contact
 * 
 * @author Keyne - Expert en IA & ML
 * @version 1.0.0
 */

class Contact {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Crée un nouveau message de contact
     */
    public function create($data) {
        $query = "INSERT INTO contacts (name, email, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($query, [
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message'],
            $data['ip_address'],
            $data['user_agent']
        ]);
    }
    
    /**
     * Récupère tous les messages de contact
     */
    public function getAll($limit = 50, $offset = 0, $status = null) {
        $whereClause = "";
        $params = [];
        
        if ($status) {
            $whereClause = "WHERE status = ?";
            $params[] = $status;
        }
        
        $query = "SELECT * FROM contacts $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $contacts = $this->db->select($query, $params);
        
        // Formatage des dates
        foreach ($contacts as &$contact) {
            $contact['created_at_formatted'] = date('d/m/Y à H:i', strtotime($contact['created_at']));
            $contact['updated_at_formatted'] = date('d/m/Y à H:i', strtotime($contact['updated_at']));
        }
        
        return $contacts;
    }
    
    /**
     * Récupère un message par son ID
     */
    public function getById($id) {
        $contact = $this->db->selectOne("SELECT * FROM contacts WHERE id = ?", [$id]);
        
        if ($contact) {
            $contact['created_at_formatted'] = date('d/m/Y à H:i', strtotime($contact['created_at']));
            $contact['updated_at_formatted'] = date('d/m/Y à H:i', strtotime($contact['updated_at']));
        }
        
        return $contact;
    }
    
    /**
     * Met à jour le statut d'un message
     */
    public function updateStatus($id, $status) {
        $validStatuses = ['new', 'read', 'replied'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        return $this->db->update("UPDATE contacts SET status = ? WHERE id = ?", [$status, $id]);
    }
    
    /**
     * Marque un message comme lu
     */
    public function markAsRead($id) {
        return $this->updateStatus($id, 'read');
    }
    
    /**
     * Marque un message comme répondu
     */
    public function markAsReplied($id) {
        return $this->updateStatus($id, 'replied');
    }
    
    /**
     * Supprime un message
     */
    public function delete($id) {
        return $this->db->delete("DELETE FROM contacts WHERE id = ?", [$id]);
    }
    
    /**
     * Compte le nombre de messages par statut
     */
    public function countByStatus() {
        $result = $this->db->select("
            SELECT status, COUNT(*) as count 
            FROM contacts 
            GROUP BY status
        ");
        
        $counts = [
            'new' => 0,
            'read' => 0,
            'replied' => 0,
            'total' => 0
        ];
        
        foreach ($result as $row) {
            $counts[$row['status']] = (int)$row['count'];
            $counts['total'] += (int)$row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Vérification anti-spam
     */
    public function checkSpam($data) {
        // Vérifier la fréquence d'envoi depuis la même IP
        $recentMessages = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM contacts WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [$data['ip_address']]
        );
        
        if ($recentMessages['count'] >= 3) {
            return 'rate_limit';
        }
        
        // Vérifier les mots-clés de spam
        $spamKeywords = ['viagra', 'casino', 'lottery', 'winner', 'congratulations', 'click here', 'free money', 'make money fast'];
        $content = strtolower($data['message'] . ' ' . $data['subject']);
        
        foreach ($spamKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return 'spam_keyword';
            }
        }
        
        // Vérifier si le message est trop court ou trop long
        if (strlen($data['message']) < 10) {
            return 'too_short';
        }
        
        if (strlen($data['message']) > 5000) {
            return 'too_long';
        }
        
        return 'clean';
    }
    
    /**
     * Recherche dans les messages
     */
    public function search($searchTerm, $limit = 20) {
        $query = "SELECT * FROM contacts 
                 WHERE name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?
                 ORDER BY created_at DESC 
                 LIMIT ?";
        
        $searchPattern = "%$searchTerm%";
        $contacts = $this->db->select($query, [$searchPattern, $searchPattern, $searchPattern, $searchPattern, $limit]);
        
        // Formatage des dates
        foreach ($contacts as &$contact) {
            $contact['created_at_formatted'] = date('d/m/Y à H:i', strtotime($contact['created_at']));
        }
        
        return $contacts;
    }
    
    /**
     * Statistiques des messages
     */
    public function getStats() {
        $stats = [];
        
        // Messages par jour (7 derniers jours)
        $dailyStats = $this->db->select("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM contacts 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ");
        
        $stats['daily'] = $dailyStats;
        
        // Messages par statut
        $stats['by_status'] = $this->countByStatus();
        
        // Messages ce mois-ci
        $monthlyCount = $this->db->selectOne("
            SELECT COUNT(*) as count
            FROM contacts 
            WHERE YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())
        ");
        
        $stats['this_month'] = (int)$monthlyCount['count'];
        
        // Temps de réponse moyen (pour les messages répondus)
        $avgResponseTime = $this->db->selectOne("
            SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours
            FROM contacts 
            WHERE status = 'replied' AND updated_at > created_at
        ");
        
        $stats['avg_response_hours'] = round((float)$avgResponseTime['avg_hours'], 1);
        
        return $stats;
    }
    
    /**
     * Valide les données d'un contact
     */
    public function validate($data) {
        $errors = [];
        
        // Nom
        if (empty($data['name'])) {
            $errors[] = 'Le nom est requis';
        } elseif (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
            $errors[] = 'Le nom doit contenir entre 2 et 100 caractères';
        }
        
        // Email
        if (empty($data['email'])) {
            $errors[] = 'L\'email est requis';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format d\'email invalide';
        }
        
        // Sujet
        if (empty($data['subject'])) {
            $errors[] = 'Le sujet est requis';
        } elseif (strlen($data['subject']) < 5 || strlen($data['subject']) > 200) {
            $errors[] = 'Le sujet doit contenir entre 5 et 200 caractères';
        }
        
        // Message
        if (empty($data['message'])) {
            $errors[] = 'Le message est requis';
        } elseif (strlen($data['message']) < 10 || strlen($data['message']) > 2000) {
            $errors[] = 'Le message doit contenir entre 10 et 2000 caractères';
        }
        
        return $errors;
    }
}
?>
