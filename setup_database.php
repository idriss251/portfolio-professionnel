<?php
/**
 * Script de création automatique de la base de données
 * Portfolio Keyne - Génie Informatique, Administration BDD & ML
 */

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuration Base de Données - Portfolio Keyne</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f8fafc; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .step { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; }
        .error { background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; }
        .info { background: #f0f9ff; border-left: 4px solid #0ea5e9; color: #0c4a6e; }
        .warning { background: #fffbeb; border-left: 4px solid #f59e0b; color: #92400e; }
        h1 { color: #1f2937; text-align: center; margin-bottom: 30px; }
        .btn { display: inline-block; padding: 12px 24px; background: #6366f1; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #4f46e5; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        pre { background: #1f2937; color: #e5e7eb; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat { text-align: center; padding: 15px; background: #f9fafb; border-radius: 8px; }
        .stat-number { font-size: 24px; font-weight: bold; color: #6366f1; }
        .stat-label { font-size: 14px; color: #6b7280; }
    </style>
</head>
<body>
<div class='container'>
<h1>🗄️ Configuration Base de Données</h1>";

try {
    // Configuration de connexion
    $host = 'localhost';
    $username = 'root';
    $password = ''; // Mot de passe vide pour XAMPP par défaut
    $dbname = 'keyne_portfolio';
    
    echo "<div class='step info'>";
    echo "<h3>📋 Paramètres de connexion</h3>";
    echo "<ul>";
    echo "<li><strong>Serveur:</strong> $host</li>";
    echo "<li><strong>Utilisateur:</strong> $username</li>";
    echo "<li><strong>Base de données:</strong> $dbname</li>";
    echo "</ul>";
    echo "</div>";
    
    // Connexion sans spécifier de base de données pour la créer
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);
    
    echo "<div class='step success'>";
    echo "<h3>✅ Connexion au serveur MySQL réussie</h3>";
    echo "</div>";
    
    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "<div class='step success'>";
    echo "<h3>✅ Base de données '$dbname' créée</h3>";
    echo "</div>";
    
    // Se connecter à la base de données créée
    $pdo->exec("USE $dbname");
    
    // Lire et exécuter le script SQL
    $sqlFile = __DIR__ . '/create_database.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Diviser le script en requêtes individuelles
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        $executedQueries = 0;
        $errors = [];
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^(--|CREATE DATABASE|USE|SELECT.*as (message|info|tables|nombre_))/i', $query)) {
                try {
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $stmt->closeCursor(); // Libérer les ressources
                    $executedQueries++;
                } catch (PDOException $e) {
                    $errors[] = "Erreur dans la requête: " . substr($query, 0, 50) . "... - " . $e->getMessage();
                }
            }
        }
        
        echo "<div class='step success'>";
        echo "<h3>✅ Script SQL exécuté</h3>";
        echo "<p><strong>Requêtes exécutées:</strong> $executedQueries</p>";
        if (!empty($errors)) {
            echo "<p><strong>Erreurs:</strong> " . count($errors) . "</p>";
            foreach ($errors as $error) {
                echo "<p style='color: #dc2626; font-size: 12px;'>$error</p>";
            }
        }
        echo "</div>";
    }
    
    // Vérifier les tables créées
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='step success'>";
    echo "<h3>📊 Tables créées (" . count($tables) . ")</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "<li><strong>$table:</strong> $count enregistrements</li>";
    }
    echo "</ul>";
    echo "</div>";
    
    // Statistiques
    $stats = [
        'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
        'contacts' => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
        'settings' => $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn(),
        'blog_posts' => $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn()
    ];
    
    echo "<div class='step info'>";
    echo "<h3>📈 Statistiques</h3>";
    echo "<div class='grid'>";
    foreach ($stats as $table => $count) {
        echo "<div class='stat'>";
        echo "<div class='stat-number'>$count</div>";
        echo "<div class='stat-label'>" . ucfirst($table) . "</div>";
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";
    
    // Quelques projets d'exemple
    $projects = $pdo->query("SELECT title, category, views_count, likes_count FROM projects LIMIT 3")->fetchAll();
    
    if (!empty($projects)) {
        echo "<div class='step info'>";
        echo "<h3>🚀 Projets d'exemple créés</h3>";
        echo "<ul>";
        foreach ($projects as $project) {
            echo "<li><strong>{$project['title']}</strong> ({$project['category']}) - {$project['views_count']} vues, {$project['likes_count']} likes</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
    echo "<div class='step success'>";
    echo "<h2>🎉 Configuration terminée avec succès!</h2>";
    echo "<p>Votre base de données est maintenant prête à être utilisée.</p>";
    echo "<div style='text-align: center; margin-top: 20px;'>";
    echo "<a href='index.html' class='btn btn-success'>🌟 Accéder au Portfolio</a>";
    echo "<a href='test.php' class='btn'>🧪 Tester l'application</a>";
    echo "<a href='app/init.php' class='btn'>⚙️ Initialiser l'app</a>";
    echo "</div>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='step error'>";
    echo "<h3>❌ Erreur de connexion</h3>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h4>🔧 Solutions possibles:</h4>";
    echo "<ul>";
    echo "<li>Vérifiez que XAMPP est démarré (Apache + MySQL)</li>";
    echo "<li>Vérifiez les paramètres de connexion</li>";
    echo "<li>Assurez-vous que MySQL fonctionne sur le port 3306</li>";
    echo "<li>Vérifiez les permissions utilisateur</li>";
    echo "</ul>";
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='http://localhost/phpmyadmin' class='btn' target='_blank'>📊 Ouvrir phpMyAdmin</a>";
    echo "<a href='start.php' class='btn'>🏠 Retour à l'accueil</a>";
    echo "</div>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='step error'>";
    echo "<h3>❌ Erreur générale</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<div style='text-align: center; margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb;'>";
echo "<p style='color: #666;'>Configuration automatique - Portfolio Keyne</p>";
echo "<p style='color: #666; font-size: 14px;'>Génie Informatique, Administration BDD & Machine Learning</p>";
echo "</div>";

echo "</div></body></html>";
?>
