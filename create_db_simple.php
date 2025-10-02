<?php
/**
 * Script simplifié de création de base de données
 * Portfolio idriss_code - Sans conflits PDO
 */

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Création Base de Données - idriss_code</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f8fafc; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .step { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; }
        .error { background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; }
        .info { background: #f0f9ff; border-left: 4px solid #0ea5e9; color: #0c4a6e; }
        h1 { color: #1f2937; text-align: center; margin-bottom: 30px; }
        .btn { display: inline-block; padding: 12px 24px; background: #6366f1; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #4f46e5; }
        .btn-success { background: #10b981; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat { text-align: center; padding: 15px; background: #f9fafb; border-radius: 8px; }
        .stat-number { font-size: 24px; font-weight: bold; color: #6366f1; }
        .stat-label { font-size: 14px; color: #6b7280; }
    </style>
</head>
<body>
<div class='container'>
<h1>🗄️ Configuration Base de Données idriss_code</h1>";

try {
    // Configuration
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'idriss_code_portfolio';
    
    echo "<div class='step info'>";
    echo "<h3>📋 Configuration</h3>";
    echo "<ul>";
    echo "<li><strong>Serveur:</strong> $host</li>";
    echo "<li><strong>Utilisateur:</strong> $username</li>";
    echo "<li><strong>Base de données:</strong> $dbname</li>";
    echo "</ul>";
    echo "</div>";
    
    // Connexion et création de la base
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);
    
    echo "<div class='step success'>";
    echo "<h3>✅ Connexion MySQL réussie</h3>";
    echo "</div>";
    
    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $dbname");
    
    echo "<div class='step success'>";
    echo "<h3>✅ Base de données '$dbname' créée</h3>";
    echo "</div>";
    
    // Créer les tables une par une
    $tables = [
        'projects' => "CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            description TEXT NOT NULL,
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
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('new', 'read', 'replied') DEFAULT 'new',
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'settings' => "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value LONGTEXT,
            setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'analytics' => "CREATE TABLE IF NOT EXISTS analytics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_url VARCHAR(500) NOT NULL,
            referrer VARCHAR(500),
            user_agent TEXT,
            ip_address VARCHAR(45),
            session_id VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    $createdTables = 0;
    foreach ($tables as $tableName => $sql) {
        try {
            $pdo->exec($sql);
            $createdTables++;
        } catch (PDOException $e) {
            echo "<div class='step error'>";
            echo "<strong>Erreur table $tableName:</strong> " . $e->getMessage();
            echo "</div>";
        }
    }
    
    echo "<div class='step success'>";
    echo "<h3>✅ Tables créées ($createdTables/4)</h3>";
    echo "</div>";
    
    // Insérer des projets d'exemple
    $sampleProjects = [
        [
            'title' => 'Système de Gestion BDD Avancé',
            'description' => 'Architecture et optimisation de bases de données MySQL/PostgreSQL avec monitoring temps réel.',
            'category' => 'database',
            'tags' => '["MySQL", "PostgreSQL", "Performance", "Monitoring"]',
            'featured' => 1,
            'status' => 'published',
            'views_count' => 156,
            'likes_count' => 23
        ],
        [
            'title' => 'Vision par Ordinateur IA',
            'description' => 'Système de détection objets temps réel avec réseaux neurones convolutionnels.',
            'category' => 'ai',
            'tags' => '["Computer Vision", "CNN", "PyTorch", "Real-time"]',
            'featured' => 1,
            'status' => 'published',
            'views_count' => 203,
            'likes_count' => 31
        ],
        [
            'title' => 'Pipeline ETL Data Warehouse',
            'description' => 'Architecture complète extraction, transformation et chargement de données.',
            'category' => 'data',
            'tags' => '["ETL", "Data Warehouse", "Apache Airflow", "Big Data"]',
            'featured' => 1,
            'status' => 'published',
            'views_count' => 134,
            'likes_count' => 18
        ],
        [
            'title' => 'Prédiction ML Immobilier',
            'description' => 'Modèle machine learning pour prédire prix immobiliers avec données géospatiales.',
            'category' => 'ml',
            'tags' => '["Machine Learning", "XGBoost", "Geospatial", "Prediction"]',
            'featured' => 1,
            'status' => 'published',
            'views_count' => 189,
            'likes_count' => 27
        ]
    ];
    
    $insertedProjects = 0;
    foreach ($sampleProjects as $project) {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, category, tags, featured, status, views_count, likes_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $project['title'],
                $project['description'],
                $project['category'],
                $project['tags'],
                $project['featured'],
                $project['status'],
                $project['views_count'],
                $project['likes_count']
            ]);
            $insertedProjects++;
        } catch (PDOException $e) {
            // Ignorer si déjà existant
        }
    }
    
    // Insérer paramètres
    $settings = [
        ['site_title', 'idriss_code - Expert IA & ML', 'string'],
        ['contact_email', 'idriss_code@example.com', 'string'],
        ['contact_phone', '+33 1 23 45 67 89', 'string'],
        ['github_url', 'https://github.com/idriss_code', 'string'],
        ['linkedin_url', 'https://linkedin.com/in/idriss_code', 'string']
    ];
    
    $insertedSettings = 0;
    foreach ($settings as $setting) {
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)");
            $stmt->execute($setting);
            $insertedSettings++;
        } catch (PDOException $e) {
            // Ignorer si déjà existant
        }
    }
    
    // Statistiques finales
    $projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $contactCount = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
    $settingsCount = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    
    echo "<div class='step success'>";
    echo "<h3>📊 Données insérées</h3>";
    echo "<div class='grid'>";
    echo "<div class='stat'>";
    echo "<div class='stat-number'>$projectCount</div>";
    echo "<div class='stat-label'>Projets</div>";
    echo "</div>";
    echo "<div class='stat'>";
    echo "<div class='stat-number'>$contactCount</div>";
    echo "<div class='stat-label'>Messages</div>";
    echo "</div>";
    echo "<div class='stat'>";
    echo "<div class='stat-number'>$settingsCount</div>";
    echo "<div class='stat-label'>Paramètres</div>";
    echo "</div>";
    echo "<div class='stat'>";
    echo "<div class='stat-number'>4</div>";
    echo "<div class='stat-label'>Tables</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    echo "<div class='step success'>";
    echo "<h2>🎉 Configuration terminée avec succès!</h2>";
    echo "<p>Base de données <strong>idriss_code_portfolio</strong> prête à l'emploi.</p>";
    echo "<div style='text-align: center; margin-top: 20px;'>";
    echo "<a href='index.html' class='btn btn-success'>🌟 Accéder au Portfolio</a>";
    echo "<a href='test.php' class='btn'>🧪 Tester l'application</a>";
    echo "<a href='app/init.php' class='btn'>⚙️ Initialiser</a>";
    echo "</div>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='step error'>";
    echo "<h3>❌ Erreur de base de données</h3>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h4>🔧 Solutions:</h4>";
    echo "<ul>";
    echo "<li>Vérifiez que XAMPP est démarré (Apache + MySQL)</li>";
    echo "<li>Vérifiez que MySQL fonctionne sur le port 3306</li>";
    echo "<li>Redémarrez les services XAMPP</li>";
    echo "</ul>";
    echo "<a href='http://localhost/phpmyadmin' class='btn' target='_blank'>📊 phpMyAdmin</a>";
    echo "</div>";
}

echo "<div style='text-align: center; margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb;'>";
echo "<p style='color: #666;'>Configuration automatique - Portfolio idriss_code</p>";
echo "<p style='color: #666; font-size: 14px;'>Génie Informatique, Administration BDD & Machine Learning</p>";
echo "</div>";

echo "</div></body></html>";
?>
