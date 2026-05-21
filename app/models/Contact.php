<?php
/**
 * Contact Model - Gestion messages de contact
 */

namespace App\Models;

class Contact
{
    protected $db;
    protected $table = 'contacts';

    public function __construct()
    {
        $this->db = \Database::getInstance();
    }

    public function create($data)
    {
        // Validation
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email invalide');
        }
        if (empty($data['message']) || strlen($data['message']) < 10) {
            throw new \Exception('Message minimum 10 caractères');
        }

        $contactData = [
            'name' => $data['name'] ?? 'Anonymous',
            'email' => $data['email'],
            'subject' => $data['subject'] ?? 'Contact',
            'message' => $data['message'],
            'status' => 'new',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->insert($this->table, $contactData);
    }

    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $conditions = [];

        if (isset($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
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

    public function updateStatus($id, $status)
    {
        $valid = ['new', 'read', 'replied'];
        if (!in_array($status, $valid)) {
            throw new \Exception('Statut invalide');
        }

        return $this->db->update(
            $this->table,
            ['status' => $status],
            'id = ?',
            [$id]
        );
    }
}
