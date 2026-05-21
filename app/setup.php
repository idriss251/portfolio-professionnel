<?php
/**
 * Setup - Initialisation base de données et données démo
 */

require_once dirname(__DIR__) . '/app/bootstrap.php';

$db = \Database::getInstance();

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Setup - API Portfolio</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f8fafc; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .step { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; }
        .error { background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; }
        .info { background: #f0f9ff; border-left: 4px solid #0ea5e9; color: #0c4a6e; }
        h1 { color: #1f2937; text-align: center; }
        .btn { display: inline-block; padding: 12px 24px; background: #6366f1; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
    </style>
</head>
<body>
<div class='container'>
<h1>🚀 Initialisation API Portfolio</h1>";

try {
    // Créer les tables
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(100),
            bio TEXT,
            avatar VARCHAR(500),
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'projects' => "CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            content LONGTEXT,
            category ENUM('ml', 'ai', 'data', 'web', 'database') NOT NULL,
            tags JSON,
            image_url VARCHAR(500),
            github_url VARCHAR(500),
            demo_url VARCHAR(500),
            featured BOOLEAN DEFAULT FALSE,
            status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
            views_count INT DEFAULT 0,
            likes_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'contacts' => "CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(150) NOT NULL,
            subject VARCHAR(200),
            message TEXT NOT NULL,
            status ENUM('new', 'read', 'replied') DEFAULT 'new',
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    $createdTables = 0;
    foreach ($tables as $tableName => $sql) {
        try {
            $db->query($sql);
            $createdTables++;
            echo "<div class='step success'>✅ Table $tableName créée</div>";
        } catch (PDOException $e) {
            echo "<div class='step info'>ℹ️ Table $tableName existe déjà</div>";
        }
    }
    
    // Insérer l'utilisateur admin
    $adminEmail = 'admin@idriss-code.com';
    $adminPassword = password_hash('admin2024_secure', PASSWORD_BCRYPT);
    
    try {
        $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$adminEmail]);
        if (!$existing) {
            $db->insert('users', [
                'email' => $adminEmail,
                'password' => $adminPassword,
                'name' => 'Idriss Admin',
                'role' => 'admin'
            ]);
            echo "<div class='step success'>✅ Utilisateur admin créé</div>";
        } else {
            echo "<div class='step info'>ℹ️ Admin existe déjà</div>";
        }
    } catch (Exception $e) {
        echo "<div class='step error'>❌ Erreur création admin: " . $e->getMessage() . "</div>";
    }
    
    // Insérer des projets d'exemple
    $projects = [
        [
            'title' => 'Vision par Ordinateur IA',
            'description' => 'Système de détection objets temps réel avec réseaux neurones convolutionnels.',
            'category' => 'ai',
            'tags' => '["Computer Vision", "CNN", "PyTorch"]',
            'featured' => 1,
            'status' => 'published'
        ],
        [
            'title' => 'Pipeline ETL Data',
            'description' => 'Architecture extraction, transformation et chargement de données.',
            'category' => 'data',
            'tags' => '["ETL", "Data Warehouse", "Apache Airflow"]',
            'featured' => 1,
            'status' => 'published'
        ],
        [
            'title' => 'Prédiction ML Immobilier',
            'description' => 'Modèle machine learning pour prédire prix immobiliers.',
            'category' => 'ml',
            'tags' => '["XGBoost", "Prediction", "Real Estate"]',
            'featured' => 1,
            'status' => 'published'
        ]
    ];
    
    $projectCount = 0;
    foreach ($projects as $project) {
        try {
            $existing = $db->fetch("SELECT id FROM projects WHERE title = ?", [$project['title']]);
            if (!$existing) {
                $db->insert('projects', array_merge($project, [
                    'content' => '',
                    'image_url' => null,
                    'github_url' => null,
                    'demo_url' => null
                ]));
                $projectCount++;
            }
        } catch (Exception $e) {}
    }
    
    echo "<div class='step success'>✅ $projectCount projets d'exemple créés</div>";
    
    echo "<div class='step success'><h2>🎉 Setup complété!</h2><p>Base de données prête.</p></div>";
    echo "<div style='text-align: center; margin-top: 30px;'>";
    echo "<a href='../api.php' class='btn'>Voir API</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='step error'><h3>❌ Erreur: " . $e->getMessage() . "</h3></div>";
}

echo "</div></body></html>";
