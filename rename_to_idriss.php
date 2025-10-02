<?php
/**
 * Script pour remplacer "Keyne" par "idriss_code" dans tous les fichiers
 */

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Renommage en idriss_code</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .step { margin: 15px 0; padding: 15px; border-radius: 5px; }
        .success { background: #ecfdf5; border-left: 4px solid #10b981; }
        .info { background: #f0f9ff; border-left: 4px solid #0ea5e9; }
        .warning { background: #fffbeb; border-left: 4px solid #f59e0b; }
        h1 { color: #1f2937; text-align: center; }
        pre { background: #f3f4f6; padding: 10px; border-radius: 5px; font-size: 12px; }
    </style>
</head>
<body>
<h1>🔄 Renommage Keyne → idriss_code</h1>";

$replacements = [
    'Keyne' => 'idriss_code',
    'keyne' => 'idriss_code',
    'KEYNE' => 'IDRISS_CODE',
    'keyne_portfolio' => 'idriss_code_portfolio',
    'keyne-ai.com' => 'idriss-code.com',
    'keyne@example.com' => 'idriss_code@example.com',
    'github.com/keyne' => 'github.com/idriss_code',
    'linkedin.com/in/keyne' => 'linkedin.com/in/idriss_code',
    'twitter.com/keyne_ai' => 'twitter.com/idriss_code_ai',
    'Portfolio Keyne' => 'Portfolio idriss_code',
    'Keyne AI' => 'idriss_code AI',
    'keyne-portfolio' => 'idriss-code-portfolio'
];

$filesToProcess = [
    'index.html',
    'README.md',
    'INSTALLATION_XAMPP.md',
    'composer.json',
    'manifest.json',
    'sitemap.xml',
    'create_database.sql',
    'setup_database.php',
    'test.php',
    'start.php',
    'app/init.php',
    'app/config/config.php',
    'app/core/Database.php',
    'app/controllers/ContactController.php',
    'app/controllers/ProjectController.php',
    'app/models/Contact.php',
    'app/models/Project.php',
    'app/helpers/functions.php',
    'assets/js/main.js'
];

$totalReplacements = 0;
$processedFiles = 0;

foreach ($filesToProcess as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $originalContent = $content;
        $fileReplacements = 0;
        
        foreach ($replacements as $search => $replace) {
            $newContent = str_replace($search, $replace, $content);
            $count = substr_count($content, $search);
            if ($count > 0) {
                $fileReplacements += $count;
                $content = $newContent;
            }
        }
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "<div class='step success'>";
            echo "<strong>✅ $file</strong><br>";
            echo "Remplacements effectués: $fileReplacements";
            echo "</div>";
            $totalReplacements += $fileReplacements;
        } else {
            echo "<div class='step info'>";
            echo "<strong>ℹ️ $file</strong><br>";
            echo "Aucun remplacement nécessaire";
            echo "</div>";
        }
        $processedFiles++;
    } else {
        echo "<div class='step warning'>";
        echo "<strong>⚠️ $file</strong><br>";
        echo "Fichier non trouvé";
        echo "</div>";
    }
}

echo "<div class='step success'>";
echo "<h2>🎉 Renommage terminé!</h2>";
echo "<p><strong>Fichiers traités:</strong> $processedFiles</p>";
echo "<p><strong>Remplacements total:</strong> $totalReplacements</p>";
echo "<h3>📋 Résumé des changements:</h3>";
echo "<ul>";
foreach ($replacements as $search => $replace) {
    echo "<li><code>$search</code> → <code>$replace</code></li>";
}
echo "</ul>";
echo "</div>";

echo "<div class='step info'>";
echo "<h3>🔄 Actions suivantes:</h3>";
echo "<ol>";
echo "<li>Mettez à jour la base de données: <code>idriss_code_portfolio</code></li>";
echo "<li>Vérifiez les liens et URLs personnalisés</li>";
echo "<li>Testez l'application: <a href='test.php'>test.php</a></li>";
echo "<li>Accédez au portfolio: <a href='index.html'>index.html</a></li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>
