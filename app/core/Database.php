<?php
/**
 * Classe de gestion de base de données avec PDO
 * 
 * @author Keyne - Expert en IA & ML
 * @version 1.0.0
 */

class Database {
    private static $instance = null;
    private $connection;
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    
    private function __construct() {
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->charset = DB_CHARSET;
        
        $this->connect();
    }
    
    /**
     * Singleton pattern pour une seule instance de connexion
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Établit la connexion à la base de données
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
            if (DEBUG_MODE) {
                error_log("Connexion à la base de données établie");
            }
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            
            if (DEBUG_MODE) {
                throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
            } else {
                throw new Exception("Erreur de connexion à la base de données");
            }
        }
    }
    
    /**
     * Retourne la connexion PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Exécute une requête SELECT
     */
    public function select($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur SELECT: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exécute une requête SELECT et retourne un seul résultat
     */
    public function selectOne($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur SELECT ONE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exécute une requête INSERT
     */
    public function insert($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute($params);
            
            if ($result) {
                return $this->connection->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur INSERT: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exécute une requête UPDATE
     */
    public function update($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute($params);
            
            if ($result) {
                return $stmt->rowCount();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur UPDATE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Exécute une requête DELETE
     */
    public function delete($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute($params);
            
            if ($result) {
                return $stmt->rowCount();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur DELETE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Commence une transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Valide une transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Annule une transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * Vérifie si une table existe
     */
    public function tableExists($tableName) {
        try {
            $query = "SHOW TABLES LIKE :table";
            $stmt = $this->connection->prepare($query);
            $stmt->execute(['table' => $tableName]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur vérification table: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crée les tables nécessaires à l'application
     */
    public function createTables() {
        $tables = [
            'contacts' => "
                CREATE TABLE IF NOT EXISTS contacts (
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
                    INDEX idx_status (status),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ",
            
            'projects' => "
                CREATE TABLE IF NOT EXISTS projects (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(200) NOT NULL,
                    description TEXT NOT NULL,
                    content LONGTEXT,
                    category ENUM('ml', 'ai', 'data', 'web') NOT NULL,
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
                    INDEX idx_status (status),
                    INDEX idx_featured (featured),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ",
            
            'blog_posts' => "
                CREATE TABLE IF NOT EXISTS blog_posts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(200) NOT NULL,
                    slug VARCHAR(250) UNIQUE NOT NULL,
                    excerpt TEXT,
                    content LONGTEXT NOT NULL,
                    featured_image VARCHAR(500),
                    category VARCHAR(100),
                    tags JSON,
                    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
                    views_count INT DEFAULT 0,
                    reading_time INT DEFAULT 0,
                    seo_title VARCHAR(200),
                    seo_description TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_slug (slug),
                    INDEX idx_status (status),
                    INDEX idx_category (category),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ",
            
            'analytics' => "
                CREATE TABLE IF NOT EXISTS analytics (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    page_url VARCHAR(500) NOT NULL,
                    referrer VARCHAR(500),
                    user_agent TEXT,
                    ip_address VARCHAR(45),
                    country VARCHAR(100),
                    city VARCHAR(100),
                    device_type ENUM('desktop', 'mobile', 'tablet'),
                    browser VARCHAR(100),
                    os VARCHAR(100),
                    session_id VARCHAR(100),
                    visit_duration INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_page_url (page_url),
                    INDEX idx_session_id (session_id),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ",
            
            'settings' => "
                CREATE TABLE IF NOT EXISTS settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    setting_key VARCHAR(100) UNIQUE NOT NULL,
                    setting_value LONGTEXT,
                    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
                    description TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_setting_key (setting_key)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            "
        ];
        
        try {
            foreach ($tables as $tableName => $sql) {
                $this->connection->exec($sql);
                if (DEBUG_MODE) {
                    error_log("Table '$tableName' créée ou vérifiée");
                }
            }
            
            // Insérer des données par défaut
            $this->insertDefaultData();
            
            return true;
        } catch (PDOException $e) {
            error_log("Erreur création tables: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Insert des données par défaut
     */
    private function insertDefaultData() {
        // Vérifier si des données existent déjà
        $projectsCount = $this->selectOne("SELECT COUNT(*) as count FROM projects");
        
        if ($projectsCount['count'] == 0) {
            // Insérer des projets d'exemple
            $sampleProjects = [
                [
                    'title' => 'Système de Recommandation IA',
                    'description' => 'Développement d\'un système de recommandation personnalisé utilisant des algorithmes de machine learning avancés.',
                    'category' => 'ml',
                    'tags' => '["Python", "TensorFlow", "Collaborative Filtering", "Deep Learning"]',
                    'featured' => 1,
                    'status' => 'published'
                ],
                [
                    'title' => 'Analyse Prédictive des Ventes',
                    'description' => 'Modèle de prédiction des ventes utilisant des techniques de time series et de régression.',
                    'category' => 'data',
                    'tags' => '["Python", "Scikit-learn", "Pandas", "Time Series"]',
                    'featured' => 1,
                    'status' => 'published'
                ],
                [
                    'title' => 'Chatbot Intelligent',
                    'description' => 'Assistant conversationnel basé sur NLP et transformers pour automatiser le support client.',
                    'category' => 'ai',
                    'tags' => '["NLP", "Transformers", "BERT", "Python"]',
                    'featured' => 1,
                    'status' => 'published'
                ]
            ];
            
            foreach ($sampleProjects as $project) {
                $this->insert(
                    "INSERT INTO projects (title, description, category, tags, featured, status) VALUES (?, ?, ?, ?, ?, ?)",
                    [$project['title'], $project['description'], $project['category'], $project['tags'], $project['featured'], $project['status']]
                );
            }
        }
        
        // Insérer des paramètres par défaut
        $settingsCount = $this->selectOne("SELECT COUNT(*) as count FROM settings");
        
        if ($settingsCount['count'] == 0) {
            $defaultSettings = [
                ['site_title', 'Keyne - Expert en IA & ML', 'string', 'Titre du site'],
                ['site_description', 'Expert en génie informatique, machine learning et systèmes intelligents', 'string', 'Description du site'],
                ['contact_email', 'keyne@example.com', 'string', 'Email de contact'],
                ['linkedin_url', 'https://linkedin.com/in/keyne', 'string', 'URL LinkedIn'],
                ['github_url', 'https://github.com/keyne', 'string', 'URL GitHub'],
                ['analytics_enabled', '1', 'boolean', 'Activer les analytics'],
                ['maintenance_mode', '0', 'boolean', 'Mode maintenance']
            ];
            
            foreach ($defaultSettings as $setting) {
                $this->insert(
                    "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)",
                    $setting
                );
            }
        }
    }
    
    /**
     * Empêche le clonage de l'instance
     */
    private function __clone() {}
    
    /**
     * Empêche la désérialisation de l'instance
     */
    public function __wakeup() {}
}
?>
