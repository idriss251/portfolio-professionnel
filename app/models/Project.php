<?php
/**
 * Project Model - Gestion des projets du portfolio
 */

namespace App\Models;

class Project
{
    protected $db;
    protected $table = 'projects';

    public function __construct()
    {
        $this->db = \Database::getInstance();
    }

    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $conditions = [];

        // Filtre statut
        if (isset($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }

        // Filtre catégorie
        if (isset($filters['category'])) {
            $conditions[] = "category = ?";
            $params[] = $filters['category'];
        }

        // Filtre vedette
        if (isset($filters['featured'])) {
            $conditions[] = "featured = ?";
            $params[] = $filters['featured'] ? 1 : 0;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    public function create($data)
    {
        if (empty($data['title'])) {
            throw new \Exception('Titre requis');
        }

        $projectData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'content' => $data['content'] ?? '',
            'category' => $data['category'] ?? 'web',
            'tags' => $data['tags'] ? json_encode($data['tags']) : null,
            'image_url' => $data['image_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'demo_url' => $data['demo_url'] ?? null,
            'featured' => $data['featured'] ?? 0,
            'status' => $data['status'] ?? 'draft',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->insert($this->table, $projectData);
    }

    public function update($id, $data)
    {
        $allowed = ['title', 'description', 'content', 'category', 'tags', 
                    'image_url', 'github_url', 'demo_url', 'featured', 'status'];
        $updateData = array_intersect_key($data, array_flip($allowed));
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        if (isset($updateData['tags']) && is_array($updateData['tags'])) {
            $updateData['tags'] = json_encode($updateData['tags']);
        }

        return $this->db->update(
            $this->table,
            $updateData,
            'id = ?',
            [$id]
        );
    }

    public function delete($id)
    {
        return $this->db->delete(
            $this->table,
            'id = ?',
            [$id]
        );
    }

    public function addLike($id)
    {
        $project = $this->getById($id);
        if (!$project) return false;

        return $this->db->update(
            $this->table,
            ['likes_count' => $project['likes_count'] + 1],
            'id = ?',
            [$id]
        );
    }
}
