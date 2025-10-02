-- Script de création de la base de données pour Portfolio Keyne
-- Génie Informatique, Administration BDD & Machine Learning

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS keyne_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
USE keyne_portfolio;

-- Table des projets
CREATE TABLE IF NOT EXISTS projects (
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
    INDEX idx_status (status),
    INDEX idx_featured (featured),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des contacts
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des articles de blog
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des analytics
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des paramètres
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des projets d'exemple
INSERT INTO projects (title, description, content, category, tags, github_url, demo_url, featured, status, views_count, likes_count) VALUES
('Système de Gestion de Base de Données Avancé', 'Architecture et optimisation de bases de données MySQL/PostgreSQL avec monitoring en temps réel et sauvegarde automatique.', 'Conception et implémentation d\'un système de gestion de base de données haute performance avec réplication maître-esclave, partitioning automatique et monitoring des performances. Inclut un dashboard d\'administration pour la surveillance des métriques critiques.', 'database', '["MySQL", "PostgreSQL", "Monitoring", "Replication", "Performance Tuning"]', 'https://github.com/keyne/advanced-db-management', 'https://demo.keyne-ai.com/db-admin', 1, 'published', 245, 18),

('Vision par Ordinateur - Détection d\'Objets', 'Système de détection et classification d\'objets en temps réel utilisant des réseaux de neurones convolutionnels (CNN) et YOLO.', 'Ce projet implémente un système complet de vision par ordinateur capable de détecter et classifier plus de 80 types d\'objets différents en temps réel. Utilisant l\'architecture YOLO (You Only Look Once) optimisée avec PyTorch, le système atteint une précision de 95% avec une latence inférieure à 50ms.', 'ai', '["Computer Vision", "YOLO", "PyTorch", "OpenCV", "Real-time Detection"]', 'https://github.com/keyne/object-detection', 'https://demo.keyne-ai.com/object-detection', 1, 'published', 189, 25),

('Pipeline ETL et Data Warehouse', 'Architecture complète d\'extraction, transformation et chargement de données avec entrepôt de données optimisé pour l\'analytique.', 'Développement d\'un pipeline ETL robuste gérant plusieurs sources de données (APIs, fichiers CSV, bases relationnelles) avec transformation en temps réel et stockage dans un data warehouse optimisé. Inclut des tableaux de bord pour le monitoring des flux de données.', 'data', '["ETL", "Data Warehouse", "Apache Airflow", "Spark", "Business Intelligence"]', 'https://github.com/keyne/etl-pipeline', 'https://demo.keyne-ai.com/etl-dashboard', 1, 'published', 156, 14),

('Prédiction de Prix Immobiliers avec ML', 'Modèle de machine learning pour prédire les prix immobiliers en utilisant des algorithmes de régression avancés et des données géospatiales.', 'Développement d\'un modèle prédictif utilisant Random Forest et XGBoost pour estimer les prix immobiliers. Le modèle intègre plus de 50 variables incluant la localisation, les caractéristiques du bien, et les données de marché.', 'ml', '["Machine Learning", "XGBoost", "Random Forest", "Real Estate", "Geospatial Data"]', 'https://github.com/keyne/real-estate-prediction', 'https://demo.keyne-ai.com/real-estate', 1, 'published', 203, 22),

('Analyse de Sentiment Multi-langues', 'Outil d\'analyse de sentiment supportant 15 langues utilisant des transformers pré-entraînés et du fine-tuning personnalisé.', 'Système d\'analyse de sentiment basé sur BERT multilingue avec fine-tuning sur des datasets spécialisés. Capable d\'analyser le sentiment dans 15 langues avec une précision moyenne de 92%.', 'ai', '["NLP", "BERT", "Sentiment Analysis", "Multilingual", "Transformers"]', 'https://github.com/keyne/multilingual-sentiment', 'https://demo.keyne-ai.com/sentiment', 1, 'published', 167, 19),

('Système de Recommandation E-commerce', 'Moteur de recommandation hybride combinant filtrage collaboratif et content-based pour optimiser les ventes.', 'Implémentation d\'un système de recommandation hybride utilisant des techniques de collaborative filtering, content-based filtering et deep learning pour personnaliser l\'expérience utilisateur.', 'ml', '["Recommendation System", "Collaborative Filtering", "Deep Learning", "E-commerce", "Personalization"]', 'https://github.com/keyne/recommendation-engine', 'https://demo.keyne-ai.com/recommendations', 0, 'published', 134, 16);

-- Insertion des paramètres par défaut
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('site_title', 'Keyne - Expert en Génie Informatique & Machine Learning', 'string', 'Titre du site'),
('site_description', 'Expert en génie informatique, administration de bases de données et machine learning', 'string', 'Description du site'),
('contact_email', 'keyne@example.com', 'string', 'Email de contact'),
('contact_phone', '+33 1 23 45 67 89', 'string', 'Téléphone de contact'),
('linkedin_url', 'https://linkedin.com/in/keyne', 'string', 'URL LinkedIn'),
('github_url', 'https://github.com/keyne', 'string', 'URL GitHub'),
('twitter_url', 'https://twitter.com/keyne_ai', 'string', 'URL Twitter'),
('analytics_enabled', '1', 'boolean', 'Activer les analytics'),
('maintenance_mode', '0', 'boolean', 'Mode maintenance'),
('skills_years_experience', '5', 'number', 'Années d\'expérience'),
('skills_projects_completed', '50', 'number', 'Projets complétés'),
('skills_technologies_mastered', '15', 'number', 'Technologies maîtrisées'),
('about_short_description', 'Expert en génie informatique, administration de bases de données et machine learning avec 5+ années d\'expérience dans le développement de solutions innovantes.', 'string', 'Description courte'),
('about_detailed_bio', 'Ingénieur spécialisé en génie informatique avec une expertise approfondie en administration de bases de données et machine learning. Passionné par l\'optimisation des systèmes d\'information et le développement de solutions d\'IA pour transformer les données en avantages concurrentiels.', 'string', 'Biographie détaillée'),
('services_consultation_price', '150€/heure', 'string', 'Prix consultation'),
('services_development_price', 'Sur devis', 'string', 'Prix développement'),
('services_training_price', '200€/heure', 'string', 'Prix formation');

-- Affichage du résumé
SELECT 'Base de données créée avec succès!' as message;
SELECT 'Tables créées:' as info;
SELECT TABLE_NAME as tables FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'keyne_portfolio';
SELECT 'Projets insérés:' as info;
SELECT COUNT(*) as nombre_projets FROM projects;
SELECT 'Paramètres configurés:' as info;
SELECT COUNT(*) as nombre_parametres FROM settings;
