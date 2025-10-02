<?php
/**
 * Script de démarrage rapide pour XAMPP
 * 
 * @author Keyne - Expert en IA & ML
 * @version 1.0.0
 */

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Démarrage - Portfolio Keyne</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            color: #333;
        }
        h1 { 
            color: #6366f1; 
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 18px;
        }
        .step {
            background: #f8fafc;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            border-left: 5px solid #6366f1;
        }
        .step h3 {
            color: #1f2937;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #6366f1;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 10px 10px 0;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #4f46e5;
            transform: translateY(-2px);
        }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .btn-warning { background: #f59e0b; }
        .btn-warning:hover { background: #d97706; }
        .status {
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-weight: 500;
        }
        .status.success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        .status.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        .status.warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #f59e0b;
        }
        .code {
            background: #1f2937;
            color: #e5e7eb;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🚀 Portfolio Keyne - IA & ML Expert</h1>";
echo "<p class='subtitle'>Application moderne et professionnelle prête à l'emploi</p>";

// Vérifier si XAMPP est détecté
$isXampp = strpos($_SERVER['DOCUMENT_ROOT'] ?? '', 'xampp') !== false || 
           strpos($_SERVER['SERVER_SOFTWARE'] ?? '', 'Apache') !== false;

if ($isXampp) {
    echo "<div class='status success'>✅ XAMPP détecté - Environnement de développement prêt</div>";
} else {
    echo "<div class='status warning'>⚠️ XAMPP non détecté - Assurez-vous d'utiliser XAMPP</div>";
}

echo "<div class='step'>
<h3>📋 Étapes d'installation</h3>
<p>Suivez ces étapes pour configurer votre portfolio :</p>

<div class='grid'>
    <div class='card'>
        <div class='icon'>🗄️</div>
        <h4>1. Base de données</h4>
        <p>Créez la base de données MySQL</p>
        <a href='http://localhost/phpmyadmin' target='_blank' class='btn'>phpMyAdmin</a>
    </div>
    
    <div class='card'>
        <div class='icon'>⚙️</div>
        <h4>2. Initialisation</h4>
        <p>Configurez l'application</p>
        <a href='app/init.php' class='btn btn-warning'>Initialiser</a>
    </div>
    
    <div class='card'>
        <div class='icon'>🧪</div>
        <h4>3. Tests</h4>
        <p>Vérifiez le fonctionnement</p>
        <a href='test.php' class='btn btn-success'>Tester</a>
    </div>
</div>
</div>";

echo "<div class='step'>
<h3>🗄️ Configuration de la base de données</h3>
<p>Exécutez cette commande SQL dans phpMyAdmin :</p>
<div class='code'>CREATE DATABASE keyne_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</div>
<p>Ou utilisez l'interface graphique de phpMyAdmin pour créer une base nommée <strong>keyne_portfolio</strong></p>
</div>";

echo "<div class='step'>
<h3>📁 Structure du projet</h3>
<p>Votre application contient :</p>
<ul>
    <li><strong>Frontend moderne</strong> - HTML5, CSS3, JavaScript responsive</li>
    <li><strong>Backend PHP</strong> - Architecture MVC avec base de données MySQL</li>
    <li><strong>API RESTful</strong> - Endpoints pour projets et contact</li>
    <li><strong>Sécurité avancée</strong> - Protection CSRF, validation, anti-spam</li>
    <li><strong>Optimisations</strong> - SEO, performance, PWA ready</li>
</ul>
</div>";

echo "<div class='step'>
<h3>🎯 Fonctionnalités incluses</h3>
<div class='grid'>
    <div>
        <h4>🎨 Design professionnel</h4>
        <ul>
            <li>Interface moderne et responsive</li>
            <li>Animations fluides</li>
            <li>Thème IA/ML spécialisé</li>
        </ul>
    </div>
    <div>
        <h4>💼 Sections complètes</h4>
        <ul>
            <li>Hero avec animation réseau neuronal</li>
            <li>À propos et expertise</li>
            <li>Projets filtrables</li>
            <li>Services et contact</li>
        </ul>
    </div>
    <div>
        <h4>🔧 Backend robuste</h4>
        <ul>
            <li>Gestion des projets</li>
            <li>Système de contact</li>
            <li>Analytics intégrées</li>
            <li>Administration</li>
        </ul>
    </div>
</div>
</div>";

// Vérifier l'état de l'installation
$configExists = file_exists('app/config/config.php');
$dbConfigured = false;

if ($configExists) {
    try {
        require_once 'app/config/config.php';
        $db = Database::getInstance();
        $dbConfigured = $db->tableExists('projects');
    } catch (Exception $e) {
        // Base non configurée
    }
}

echo "<div class='step'>
<h3>📊 État de l'installation</h3>";

if ($configExists) {
    echo "<div class='status success'>✅ Configuration trouvée</div>";
} else {
    echo "<div class='status error'>❌ Configuration manquante</div>";
}

if ($dbConfigured) {
    echo "<div class='status success'>✅ Base de données configurée</div>";
    echo "<p><strong>🎉 Installation terminée !</strong> Votre application est prête.</p>";
    echo "<a href='index.html' class='btn btn-success' style='font-size: 18px; padding: 15px 30px;'>🚀 Accéder au Portfolio</a>";
} else {
    echo "<div class='status warning'>⚠️ Base de données non configurée</div>";
    echo "<p>Cliquez sur <strong>Initialiser</strong> pour configurer la base de données.</p>";
}

echo "</div>";

echo "<div class='step'>
<h3>📚 Documentation</h3>
<p>Consultez la documentation complète :</p>
<a href='README.md' class='btn' target='_blank'>📖 README</a>
<a href='INSTALLATION_XAMPP.md' class='btn' target='_blank'>🔧 Guide XAMPP</a>
</div>";

echo "<div class='step'>
<h3>🔗 Liens utiles</h3>
<div class='grid'>
    <div>
        <h4>🌐 Application</h4>
        <a href='index.html' class='btn'>Portfolio</a>
        <a href='app/controllers/ProjectController.php?action=getAllProjects' class='btn' target='_blank'>API Projets</a>
    </div>
    <div>
        <h4>🛠️ Administration</h4>
        <a href='http://localhost/phpmyadmin' class='btn' target='_blank'>phpMyAdmin</a>
        <a href='test.php' class='btn'>Tests</a>
    </div>
    <div>
        <h4>📋 Monitoring</h4>
        <a href='logs/' class='btn' target='_blank'>Logs</a>
        <a href='app/init.php' class='btn'>Réinitialiser</a>
    </div>
</div>
</div>";

echo "<div style='text-align: center; margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb;'>
<p style='color: #666;'>Développé avec ❤️ par <strong>Keyne</strong> - Expert en IA & Machine Learning</p>
<p style='color: #666; font-size: 14px;'>Version " . (defined('APP_VERSION') ? APP_VERSION : '1.0.0') . " - " . date('Y') . "</p>
</div>";

echo "</div></body></html>";
?>
