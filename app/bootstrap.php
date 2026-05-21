<?php
/**
 * Application Bootstrap - Initialise l'environnement et l'autoloader
 */

// Versions PHP requise
if (PHP_VERSION_ID < 70400) {
    die('PHP 7.4.0 ou supérieur requis');
}

// Définir les constantes
define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/app');
define('CONFIG_PATH', APP_PATH . '/config');
define('MODELS_PATH', APP_PATH . '/models');
define('CONTROLLERS_PATH', APP_PATH . '/controllers');
define('HELPERS_PATH', APP_PATH . '/helpers');

// Charger la configuration
require_once CONFIG_PATH . '/Config.php';

// Autoloader PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = APP_PATH . '/' . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Headers de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\';');

// CORS Headers
$allowed_origins = explode(',', Config::get('CORS_ALLOWED_ORIGINS', 'http://localhost:8000'));
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Error handling
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile:$errline");
    if (Config::get('APP_DEBUG')) {
        echo "Error: $errstr in $errfile:$errline";
    }
});

set_exception_handler(function ($exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
});
