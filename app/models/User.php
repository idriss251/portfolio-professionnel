<?php
/**
 * User Model - Gestion utilisateurs et authentification
 */

namespace App\Models;

class User
{
    protected $db;
    protected $table = 'users';

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
        
        if (empty($data['password']) || strlen($data['password']) < 6) {
            throw new \Exception('Mot de passe minimum 6 caractères');
        }

        // Vérifier email unique
        if ($this->findByEmail($data['email'])) {
            throw new \Exception('Email déjà utilisé');
        }

        $userData = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'name' => $data['name'] ?? 'User',
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->insert($this->table, $userData);
    }

    public function findByEmail($email)
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE email = ?",
            [$email]
        );
    }

    public function findById($id)
    {
        return $this->db->fetch(
            "SELECT id, email, name, role, created_at FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function updateProfile($id, $data)
    {
        $allowed = ['name', 'bio', 'avatar'];
        $updateData = array_intersect_key($data, array_flip($allowed));
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->update(
            $this->table,
            $updateData,
            'id = ?',
            [$id]
        );
    }
}
