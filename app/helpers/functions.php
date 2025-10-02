<?php
/**
 * Fonctions utilitaires globales
 * 
 * @author Keyne - Expert en IA & ML
 * @version 1.0.0
 */

/**
 * Sécurise une chaîne pour l'affichage HTML
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Génère un token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie un token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirige vers une URL
 */
function redirect($url, $statusCode = 302) {
    header("Location: $url", true, $statusCode);
    exit;
}

/**
 * Retourne une réponse JSON
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Valide une adresse email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Génère un slug à partir d'une chaîne
 */
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[àáâãäå]/u', 'a', $string);
    $string = preg_replace('/[èéêë]/u', 'e', $string);
    $string = preg_replace('/[ìíîï]/u', 'i', $string);
    $string = preg_replace('/[òóôõö]/u', 'o', $string);
    $string = preg_replace('/[ùúûü]/u', 'u', $string);
    $string = preg_replace('/[ç]/u', 'c', $string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Formate une date en français
 */
function formatDateFr($date, $format = 'd/m/Y') {
    $months = [
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
    ];
    
    $days = [
        1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi',
        5 => 'vendredi', 6 => 'samedi', 0 => 'dimanche'
    ];
    
    $timestamp = is_string($date) ? strtotime($date) : $date;
    
    if ($format === 'full') {
        $dayName = $days[date('w', $timestamp)];
        $day = date('j', $timestamp);
        $monthName = $months[date('n', $timestamp)];
        $year = date('Y', $timestamp);
        return "$dayName $day $monthName $year";
    }
    
    return date($format, $timestamp);
}

/**
 * Calcule le temps de lecture estimé
 */
function calculateReadingTime($text, $wordsPerMinute = 200) {
    $wordCount = str_word_count(strip_tags($text));
    $minutes = ceil($wordCount / $wordsPerMinute);
    return $minutes;
}

/**
 * Génère un extrait de texte
 */
function generateExcerpt($text, $length = 150, $suffix = '...') {
    $text = strip_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $excerpt = substr($text, 0, $length);
    $lastSpace = strrpos($excerpt, ' ');
    
    if ($lastSpace !== false) {
        $excerpt = substr($excerpt, 0, $lastSpace);
    }
    
    return $excerpt . $suffix;
}

/**
 * Formate une taille de fichier
 */
function formatFileSize($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Vérifie si une URL est valide
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Génère un mot de passe aléatoire
 */
function generateRandomPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Chiffre une chaîne
 */
function encrypt($data, $key = null) {
    $key = $key ?: ENCRYPTION_KEY;
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Déchiffre une chaîne
 */
function decrypt($data, $key = null) {
    $key = $key ?: ENCRYPTION_KEY;
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

/**
 * Log une erreur
 */
function logError($message, $context = []) {
    $logMessage = date('Y-m-d H:i:s') . ' - ' . $message;
    
    if (!empty($context)) {
        $logMessage .= ' - Context: ' . json_encode($context);
    }
    
    error_log($logMessage, 3, LOG_PATH . '/error.log');
}

/**
 * Log une information
 */
function logInfo($message, $context = []) {
    if (DEBUG_MODE) {
        $logMessage = date('Y-m-d H:i:s') . ' - INFO: ' . $message;
        
        if (!empty($context)) {
            $logMessage .= ' - Context: ' . json_encode($context);
        }
        
        error_log($logMessage, 3, LOG_PATH . '/info.log');
    }
}

/**
 * Vérifie si l'utilisateur est sur mobile
 */
function isMobile() {
    return preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT'] ?? '');
}

/**
 * Obtient l'adresse IP du client
 */
function getClientIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            $ip = trim($ips[0]);
            
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Obtient des informations sur le navigateur
 */
function getBrowserInfo() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $browsers = [
        'Chrome' => '/Chrome\/([0-9.]+)/',
        'Firefox' => '/Firefox\/([0-9.]+)/',
        'Safari' => '/Safari\/([0-9.]+)/',
        'Edge' => '/Edge\/([0-9.]+)/',
        'Opera' => '/Opera\/([0-9.]+)/',
        'Internet Explorer' => '/MSIE ([0-9.]+)/'
    ];
    
    foreach ($browsers as $browser => $pattern) {
        if (preg_match($pattern, $userAgent, $matches)) {
            return [
                'name' => $browser,
                'version' => $matches[1] ?? 'Unknown'
            ];
        }
    }
    
    return ['name' => 'Unknown', 'version' => 'Unknown'];
}

/**
 * Obtient des informations sur le système d'exploitation
 */
function getOSInfo() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $os = [
        'Windows' => '/Windows NT ([0-9.]+)/',
        'macOS' => '/Mac OS X ([0-9._]+)/',
        'Linux' => '/Linux/',
        'Android' => '/Android ([0-9.]+)/',
        'iOS' => '/OS ([0-9_]+)/'
    ];
    
    foreach ($os as $osName => $pattern) {
        if (preg_match($pattern, $userAgent, $matches)) {
            return [
                'name' => $osName,
                'version' => $matches[1] ?? 'Unknown'
            ];
        }
    }
    
    return ['name' => 'Unknown', 'version' => 'Unknown'];
}

/**
 * Nettoie et valide une entrée utilisateur
 */
function sanitizeInput($input, $type = 'string') {
    $input = trim($input);
    
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
            
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
            
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
        case 'html':
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            
        default:
            return filter_var($input, FILTER_SANITIZE_STRING);
    }
}

/**
 * Génère une pagination
 */
function generatePagination($currentPage, $totalPages, $baseUrl, $maxLinks = 5) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $pagination = '<nav class="pagination">';
    $pagination .= '<ul class="pagination-list">';
    
    // Bouton précédent
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $pagination .= "<li><a href=\"{$baseUrl}?page={$prevPage}\" class=\"pagination-link\">&laquo; Précédent</a></li>";
    }
    
    // Calcul des pages à afficher
    $start = max(1, $currentPage - floor($maxLinks / 2));
    $end = min($totalPages, $start + $maxLinks - 1);
    
    if ($end - $start + 1 < $maxLinks) {
        $start = max(1, $end - $maxLinks + 1);
    }
    
    // Pages
    for ($i = $start; $i <= $end; $i++) {
        $activeClass = $i === $currentPage ? ' active' : '';
        $pagination .= "<li><a href=\"{$baseUrl}?page={$i}\" class=\"pagination-link{$activeClass}\">{$i}</a></li>";
    }
    
    // Bouton suivant
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $pagination .= "<li><a href=\"{$baseUrl}?page={$nextPage}\" class=\"pagination-link\">Suivant &raquo;</a></li>";
    }
    
    $pagination .= '</ul>';
    $pagination .= '</nav>';
    
    return $pagination;
}

/**
 * Crée les dossiers nécessaires s'ils n'existent pas
 */
function createDirectoriesIfNotExists() {
    $directories = [
        LOG_PATH,
        CACHE_PATH,
        UPLOAD_PATH
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

/**
 * Initialise l'application
 */
function initializeApp() {
    // Créer les dossiers nécessaires
    createDirectoriesIfNotExists();
    
    // Initialiser la base de données
    try {
        $db = Database::getInstance();
        $db->createTables();
        
        if (DEBUG_MODE) {
            logInfo('Application initialisée avec succès');
        }
    } catch (Exception $e) {
        logError('Erreur lors de l\'initialisation de l\'application: ' . $e->getMessage());
        
        if (DEBUG_MODE) {
            throw $e;
        }
    }
}

// Initialisation automatique
if (!defined('SKIP_AUTO_INIT')) {
    initializeApp();
}
?>
