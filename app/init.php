<?php
/**
 * Script d'initialisation de l'application
 * 
 * @author Keyne - Expert en IA & ML
 * @version 1.0.0
 */

require_once 'config/config.php';

// Détecter si on est en mode web ou CLI
$isWeb = isset($_SERVER['HTTP_HOST']);

if ($isWeb) {
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Initialisation - Portfolio Keyne</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f8fafc; }
            .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            .step { margin: 20px 0; padding: 15px; background: #f0f9ff; border-left: 4px solid #0ea5e9; border-radius: 5px; }
            .success { background: #ecfdf5; border-color: #10b981; }
            .error { background: #fef2f2; border-color: #ef4444; }
            .warning { background: #fffbeb; border-color: #f59e0b; }
            h1 { color: #1f2937; text-align: center; }
            .btn { display: inline-block; padding: 10px 20px; background: #6366f1; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
            .btn:hover { background: #4f46e5; }
            pre { background: #1f2937; color: #e5e7eb; padding: 15px; border-radius: 5px; overflow-x: auto; }
        </style>
    </head>
    <body>
    <div class='container'>
    <h1>🚀 Initialisation Portfolio Keyne</h1>";
}

try {
    if ($isWeb) {
        echo "<div class='step'>Initialisation de l'application Keyne Portfolio...</div>";
    } else {
        echo "🚀 Initialisation de l'application Keyne Portfolio...\n\n";
    }
    
    // Vérification de l'environnement PHP
    echo "✓ Vérification de l'environnement PHP...\n";
    if (version_compare(PHP_VERSION, '7.4.0', '<')) {
        throw new Exception('PHP 7.4 ou supérieur requis. Version actuelle: ' . PHP_VERSION);
    }
    echo "  - Version PHP: " . PHP_VERSION . " ✓\n";
    
    // Vérification des extensions requises
    $requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'openssl', 'mbstring'];
    foreach ($requiredExtensions as $ext) {
        if (!extension_loaded($ext)) {
            throw new Exception("Extension PHP requise manquante: $ext");
        }
        echo "  - Extension $ext: ✓\n";
    }
    
    // Création des dossiers nécessaires
    echo "\n📁 Création des dossiers...\n";
    $directories = [
        'logs' => LOG_PATH,
        'cache' => CACHE_PATH,
        'uploads' => UPLOAD_PATH,
        'tmp' => ROOT_PATH . '/tmp'
    ];
    
    foreach ($directories as $name => $path) {
        if (!is_dir($path)) {
            if (mkdir($path, 0755, true)) {
                echo "  - Dossier $name créé: $path ✓\n";
            } else {
                throw new Exception("Impossible de créer le dossier: $path");
            }
        } else {
            echo "  - Dossier $name existe: $path ✓\n";
        }
    }
    
    // Test de connexion à la base de données
    echo "\n🗄️ Connexion à la base de données...\n";
    $db = Database::getInstance();
    echo "  - Connexion établie ✓\n";
    
    // Création des tables
    echo "\n🔧 Création des tables de base de données...\n";
    if ($db->createTables()) {
        echo "  - Tables créées/vérifiées ✓\n";
    } else {
        throw new Exception("Erreur lors de la création des tables");
    }
    
    // Vérification des tables créées
    $tables = ['contacts', 'projects', 'blog_posts', 'analytics', 'settings'];
    foreach ($tables as $table) {
        if ($db->tableExists($table)) {
            echo "  - Table $table: ✓\n";
        } else {
            echo "  - Table $table: ❌\n";
        }
    }
    
    // Insertion de données de test (si en mode développement)
    if (DEBUG_MODE) {
        echo "\n📊 Insertion de données de test...\n";
        
        // Vérifier si des projets existent déjà
        $projectCount = $db->selectOne("SELECT COUNT(*) as count FROM projects");
        
        if ($projectCount['count'] < 6) {
            $sampleProjects = [
                [
                    'title' => 'Vision par Ordinateur - Détection d\'Objets',
                    'description' => 'Système de détection et classification d\'objets en temps réel utilisant des réseaux de neurones convolutionnels (CNN) et YOLO.',
                    'content' => 'Ce projet implémente un système complet de vision par ordinateur capable de détecter et classifier plus de 80 types d\'objets différents en temps réel. Utilisant l\'architecture YOLO (You Only Look Once) optimisée avec PyTorch, le système atteint une précision de 95% avec une latence inférieure à 50ms.',
                    'category' => 'ai',
                    'tags' => '["Computer Vision", "YOLO", "PyTorch", "OpenCV", "Real-time Detection"]',
                    'github_url' => 'https://github.com/keyne/object-detection',
                    'demo_url' => 'https://demo.keyne-ai.com/object-detection',
                    'featured' => 1,
                    'status' => 'published'
                ],
                [
                    'title' => 'Prédiction de Prix Immobiliers avec ML',
                    'description' => 'Modèle de machine learning pour prédire les prix immobiliers en utilisant des algorithmes de régression avancés et des données géospatiales.',
                    'content' => 'Développement d\'un modèle prédictif utilisant Random Forest et XGBoost pour estimer les prix immobiliers. Le modèle intègre plus de 50 variables incluant la localisation, les caractéristiques du bien, et les données de marché.',
                    'category' => 'ml',
                    'tags' => '["Machine Learning", "XGBoost", "Random Forest", "Real Estate", "Geospatial Data"]',
                    'github_url' => 'https://github.com/keyne/real-estate-prediction',
                    'demo_url' => 'https://demo.keyne-ai.com/real-estate',
                    'featured' => 1,
                    'status' => 'published'
                ],
                [
                    'title' => 'Analyse de Sentiment Multi-langues',
                    'description' => 'Outil d\'analyse de sentiment supportant 15 langues utilisant des transformers pré-entraînés et du fine-tuning personnalisé.',
                    'content' => 'Système d\'analyse de sentiment basé sur BERT multilingue avec fine-tuning sur des datasets spécialisés. Capable d\'analyser le sentiment dans 15 langues avec une précision moyenne de 92%.',
                    'category' => 'ai',
                    'tags' => '["NLP", "BERT", "Sentiment Analysis", "Multilingual", "Transformers"]',
                    'github_url' => 'https://github.com/keyne/multilingual-sentiment',
                    'demo_url' => 'https://demo.keyne-ai.com/sentiment',
                    'featured' => 1,
                    'status' => 'published'
                ],
                [
                    'title' => 'Dashboard Analytics en Temps Réel',
                    'description' => 'Tableau de bord interactif pour visualiser des métriques business en temps réel avec des prédictions automatisées.',
                    'content' => 'Dashboard développé avec React et D3.js connecté à une API Python/FastAPI. Intègre des modèles de prédiction pour anticiper les tendances et alerter sur les anomalies.',
                    'category' => 'data',
                    'tags' => '["Data Visualization", "React", "D3.js", "FastAPI", "Real-time Analytics"]',
                    'github_url' => 'https://github.com/keyne/analytics-dashboard',
                    'demo_url' => 'https://demo.keyne-ai.com/dashboard',
                    'featured' => 0,
                    'status' => 'published'
                ],
                [
                    'title' => 'Système de Recommandation E-commerce',
                    'description' => 'Moteur de recommandation hybride combinant filtrage collaboratif et content-based pour optimiser les ventes.',
                    'content' => 'Implémentation d\'un système de recommandation hybride utilisant des techniques de collaborative filtering, content-based filtering et deep learning pour personnaliser l\'expérience utilisateur.',
                    'category' => 'ml',
                    'tags' => '["Recommendation System", "Collaborative Filtering", "Deep Learning", "E-commerce", "Personalization"]',
                    'github_url' => 'https://github.com/keyne/recommendation-engine',
                    'demo_url' => 'https://demo.keyne-ai.com/recommendations',
                    'featured' => 0,
                    'status' => 'published'
                ],
                [
                    'title' => 'Optimisation de Chaîne Logistique par IA',
                    'description' => 'Algorithmes d\'optimisation pour réduire les coûts logistiques de 25% en utilisant l\'apprentissage par renforcement.',
                    'content' => 'Développement d\'algorithmes d\'optimisation basés sur l\'apprentissage par renforcement pour optimiser les routes de livraison, la gestion des stocks et la planification des ressources.',
                    'category' => 'ai',
                    'tags' => '["Reinforcement Learning", "Supply Chain", "Optimization", "Logistics", "Cost Reduction"]',
                    'github_url' => 'https://github.com/keyne/supply-chain-optimization',
                    'demo_url' => 'https://demo.keyne-ai.com/logistics',
                    'featured' => 0,
                    'status' => 'published'
                ]
            ];
            
            foreach ($sampleProjects as $project) {
                $id = $db->insert(
                    "INSERT INTO projects (title, description, content, category, tags, github_url, demo_url, featured, status, views_count, likes_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $project['title'],
                        $project['description'],
                        $project['content'],
                        $project['category'],
                        $project['tags'],
                        $project['github_url'],
                        $project['demo_url'],
                        $project['featured'],
                        $project['status'],
                        rand(50, 500), // Vues aléatoires
                        rand(5, 50)    // Likes aléatoires
                    ]
                );
                
                if ($id) {
                    echo "  - Projet '{$project['title']}' ajouté (ID: $id) ✓\n";
                }
            }
        }
        
        // Vérifier et ajouter des paramètres manquants
        $settings = [
            'site_maintenance' => '0',
            'analytics_google_id' => '',
            'social_twitter' => 'https://twitter.com/keyne_ai',
            'social_linkedin' => 'https://linkedin.com/in/keyne',
            'social_github' => 'https://github.com/keyne',
            'contact_phone' => '+33 1 23 45 67 89',
            'skills_years_experience' => '5',
            'skills_projects_completed' => '50+',
            'skills_technologies_mastered' => '15+',
            'about_short_description' => 'Expert en Intelligence Artificielle et Machine Learning avec 5+ années d\'expérience dans le développement de solutions innovantes.',
            'about_detailed_bio' => 'Ingénieur spécialisé en génie informatique avec une expertise approfondie en machine learning, deep learning et systèmes intelligents. Passionné par l\'innovation technologique et la résolution de problèmes complexes à travers l\'IA.',
            'services_consultation_price' => '150€/heure',
            'services_development_price' => 'Sur devis',
            'services_training_price' => '200€/heure'
        ];
        
        foreach ($settings as $key => $value) {
            $existing = $db->selectOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
            if (!$existing) {
                $db->insert(
                    "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'string', ?)",
                    [$key, $value, "Paramètre: $key"]
                );
                echo "  - Paramètre '$key' ajouté ✓\n";
            }
        }
    }
    
    // Vérification des permissions
    echo "\n🔐 Vérification des permissions...\n";
    $writableDirs = [LOG_PATH, CACHE_PATH, UPLOAD_PATH];
    
    foreach ($writableDirs as $dir) {
        if (is_writable($dir)) {
            echo "  - $dir: Écriture autorisée ✓\n";
        } else {
            echo "  - $dir: ⚠️ Permissions d'écriture manquantes\n";
        }
    }
    
    // Test de création de fichier
    $testFile = LOG_PATH . '/test_' . time() . '.txt';
    if (file_put_contents($testFile, 'Test d\'écriture') !== false) {
        unlink($testFile);
        echo "  - Test d'écriture: ✓\n";
    } else {
        echo "  - Test d'écriture: ❌\n";
    }
    
    // Génération du fichier .htaccess si nécessaire
    echo "\n⚙️ Configuration serveur web...\n";
    $htaccessPath = ROOT_PATH . '/.htaccess';
    
    if (!file_exists($htaccessPath)) {
        $htaccessContent = "# Configuration Apache pour le portfolio Keyne
RewriteEngine On

# Redirection HTTPS (production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Protection des fichiers sensibles
<Files ~ \"^\\.(htaccess|htpasswd|ini|log|sh|sql)$\">
    Order allow,deny
    Deny from all
</Files>

# Protection du dossier app
<Directory \"app\">
    Order allow,deny
    Deny from all
</Directory>

# Protection du dossier logs
<Directory \"logs\">
    Order allow,deny
    Deny from all
</Directory>

# Compression GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache des ressources statiques
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType image/png \"access plus 1 month\"
    ExpiresByType image/jpg \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 month\"
    ExpiresByType image/gif \"access plus 1 month\"
    ExpiresByType image/svg+xml \"access plus 1 month\"
</IfModule>

# Headers de sécurité
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
    Header always set Permissions-Policy \"geolocation=(), microphone=(), camera=()\"
</IfModule>
";
        
        if (file_put_contents($htaccessPath, $htaccessContent)) {
            echo "  - Fichier .htaccess créé ✓\n";
        } else {
            echo "  - ⚠️ Impossible de créer le fichier .htaccess\n";
        }
    } else {
        echo "  - Fichier .htaccess existe ✓\n";
    }
    
    // Résumé final
    if ($isWeb) {
        echo "<div class='step success'>";
        echo "<h2>🎉 Initialisation terminée avec succès!</h2>";
        echo "<h3>📋 Résumé:</h3>";
        echo "<ul>";
        echo "<li>Application: " . APP_NAME . " v" . APP_VERSION . "</li>";
        echo "<li>Environnement: " . ENVIRONMENT . "</li>";
        echo "<li>Base de données: " . DB_NAME . "</li>";
        echo "<li>URL: " . APP_URL . "</li>";
        echo "<li>Debug: " . (DEBUG_MODE ? 'Activé' : 'Désactivé') . "</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div class='step success'>";
        echo "<h3>🚀 L'application est prête à être utilisée!</h3>";
        echo "<a href='../index.html' class='btn'>🌟 Accéder au Portfolio</a>";
        echo "<a href='../test.php' class='btn'>🧪 Tester l'application</a>";
        echo "<a href='../start.php' class='btn'>📋 Page d'accueil</a>";
        echo "</div>";
        
        if (DEBUG_MODE) {
            echo "<div class='step warning'>";
            echo "<h3>🔧 Mode développement activé:</h3>";
            echo "<ul>";
            echo "<li>Logs détaillés activés</li>";
            echo "<li>Données de test insérées</li>";
            echo "<li>Erreurs affichées</li>";
            echo "</ul>";
            echo "</div>";
        }
        
        echo "<div class='step'>";
        echo "<h3>📚 Prochaines étapes:</h3>";
        echo "<ol>";
        echo "<li>Configurez vos paramètres SMTP dans config.php</li>";
        echo "<li>Personnalisez les informations de contact</li>";
        echo "<li>Ajoutez vos vrais projets</li>";
        echo "<li>Configurez votre serveur web (Apache/Nginx)</li>";
        echo "<li>Activez HTTPS en production</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "🎉 Initialisation terminée avec succès!\n\n";
        echo "📋 Résumé:\n";
        echo "  - Application: " . APP_NAME . " v" . APP_VERSION . "\n";
        echo "  - Environnement: " . ENVIRONMENT . "\n";
        echo "  - Base de données: " . DB_NAME . "\n";
        echo "  - URL: " . APP_URL . "\n";
        echo "  - Debug: " . (DEBUG_MODE ? 'Activé' : 'Désactivé') . "\n\n";
        
        echo "🚀 L'application est prête à être utilisée!\n";
        echo "   Accédez à: " . APP_URL . "\n\n";
        
        if (DEBUG_MODE) {
            echo "🔧 Mode développement activé:\n";
            echo "   - Logs détaillés activés\n";
            echo "   - Données de test insérées\n";
            echo "   - Erreurs affichées\n\n";
        }
        
        echo "📚 Prochaines étapes:\n";
        echo "   1. Configurez vos paramètres SMTP dans config.php\n";
        echo "   2. Personnalisez les informations de contact\n";
        echo "   3. Ajoutez vos vrais projets\n";
        echo "   4. Configurez votre serveur web (Apache/Nginx)\n";
        echo "   5. Activez HTTPS en production\n\n";
    }
    
} catch (Exception $e) {
    if ($isWeb) {
        echo "<div class='step error'>";
        echo "<h3>❌ Erreur lors de l'initialisation:</h3>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        
        if (DEBUG_MODE) {
            echo "<h4>📍 Trace de l'erreur:</h4>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        
        echo "<h4>🔧 Solutions possibles:</h4>";
        echo "<ul>";
        echo "<li>Vérifiez la configuration de la base de données</li>";
        echo "<li>Assurez-vous que PHP a les bonnes permissions</li>";
        echo "<li>Vérifiez que toutes les extensions PHP sont installées</li>";
        echo "<li>Consultez les logs d'erreur du serveur</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "\n❌ Erreur lors de l'initialisation:\n";
        echo "   " . $e->getMessage() . "\n\n";
        
        if (DEBUG_MODE) {
            echo "📍 Trace de l'erreur:\n";
            echo $e->getTraceAsString() . "\n\n";
        }
        
        echo "🔧 Solutions possibles:\n";
        echo "   - Vérifiez la configuration de la base de données\n";
        echo "   - Assurez-vous que PHP a les bonnes permissions\n";
        echo "   - Vérifiez que toutes les extensions PHP sont installées\n";
        echo "   - Consultez les logs d'erreur du serveur\n\n";
    }
    
    exit(1);
}

// Fermeture HTML pour le mode web
if ($isWeb) {
    echo "</div></body></html>";
}
?>
