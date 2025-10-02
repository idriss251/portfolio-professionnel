<?php
/**
 * Configuration principale de l'application
 * 
 * @author idriss_code - Expert en IA & ML
 * @version 1.0.0
 */

// Définir les constantes de l'application
define('APP_NAME', 'idriss_code - AI & ML Expert');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/mon-site-professionnel');

// Configuration de la base de données (XAMPP)
define('DB_HOST', 'localhost');
define('DB_NAME', 'idriss_code_portfolio');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mot de passe vide par défaut sur XAMPP
define('DB_CHARSET', 'utf8mb4');

// Configuration des chemins (adaptés pour XAMPP)
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('VIEWS_PATH', APP_PATH . '/views');
define('CONTROLLERS_PATH', APP_PATH . '/controllers');
define('MODELS_PATH', APP_PATH . '/models');

// Configuration de sécurité
define('ENCRYPTION_KEY', 'your-secret-encryption-key-here');
define('JWT_SECRET', 'your-jwt-secret-key-here');
define('SESSION_LIFETIME', 3600); // 1 heure

// Configuration email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@idriss-code.com');
define('SMTP_FROM_NAME', 'idriss_code AI Expert');

// Configuration de l'environnement
define('ENVIRONMENT', 'development'); // development, production
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Configuration des logs
define('LOG_PATH', ROOT_PATH . '/logs');
define('LOG_LEVEL', DEBUG_MODE ? 'DEBUG' : 'ERROR');

// Configuration des uploads
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Configuration API
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // Requêtes par heure
define('API_TIMEOUT', 30); // Secondes

// Configuration cache
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 heure
define('CACHE_PATH', ROOT_PATH . '/tmp/cache');

// Timezone
date_default_timezone_set('Europe/Paris');

// Gestion des erreurs
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Headers de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

if (!DEBUG_MODE) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Configuration des sessions
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', !DEBUG_MODE ? 1 : 0);
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Démarrage de session sécurisé
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader simple
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/core/',
        APP_PATH . '/helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Fonction d'aide pour les URLs
function url($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

// Fonction d'aide pour les assets
function asset($path) {
    return APP_URL . '/assets/' . ltrim($path, '/');
}

// Fonction d'aide pour l'environnement
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Fonction d'aide pour la configuration
function config($key, $default = null) {
    $config = [
        'app.name' => APP_NAME,
        'app.version' => APP_VERSION,
        'app.url' => APP_URL,
        'app.debug' => DEBUG_MODE,
        'db.host' => DB_HOST,
        'db.name' => DB_NAME,
        'db.user' => DB_USER,
        'db.pass' => DB_PASS,
        'mail.host' => SMTP_HOST,
        'mail.port' => SMTP_PORT,
        'mail.username' => SMTP_USERNAME,
        'mail.password' => SMTP_PASSWORD,
    ];
    
    return $config[$key] ?? $default;
}

// Chargement des helpers
require_once APP_PATH . '/helpers/functions.php';

// Initialisation de l'application
if (DEBUG_MODE) {
    error_log("Application initialisée - " . date('Y-m-d H:i:s'));
}
?>
