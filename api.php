<?php
/**
 * API REST - Point d'entrée principal
 * Routes: /api/{resource}/{action}
 */

require_once __DIR__ . '/app/bootstrap.php';

// Obtenir la route
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('/api.php', '', $_SERVER['SCRIPT_NAME']);
$route = str_replace($basePath . '/api', '', $requestUri);

// Parser la route
$parts = array_filter(explode('/', $route));
array_values($parts);

if (empty($parts)) {
    return \Response::success(['message' => 'API Portfolio']);
}

$resource = $parts[0] ?? null;
$id = $parts[1] ?? null;
$action = $parts[2] ?? null;
$method = strtolower($_SERVER['REQUEST_METHOD']);

// Routes
try {
    // Health check
    if ($resource === 'health') {
        return \Response::success(['status' => 'ok']);
    }

    // Authentication
    if ($resource === 'auth') {
        $auth = new \App\Controllers\AuthController();
        
        if ($method === 'post' && $action === 'login') {
            return $auth->login();
        }
        if ($method === 'post' && $action === 'register') {
            return $auth->register();
        }
        if ($method === 'get' && $id === 'profile') {
            return $auth->profile();
        }
        if ($method === 'put' && $id === 'profile') {
            return $auth->updateProfile();
        }
    }

    // Projects
    if ($resource === 'projects') {
        $project = new \App\Controllers\ProjectController();
        
        if ($method === 'get' && !$id) {
            return $project->getAll();
        }
        if ($method === 'get' && is_numeric($id)) {
            return $project->getById($id);
        }
        if ($method === 'post') {
            return $project->create();
        }
        if ($method === 'put' && is_numeric($id)) {
            return $project->update($id);
        }
        if ($method === 'delete' && is_numeric($id)) {
            return $project->delete($id);
        }
        if ($method === 'post' && $action === 'like') {
            return $project->like($id);
        }
    }

    // Contacts
    if ($resource === 'contacts') {
        $contact = new \App\Controllers\ContactController();
        
        if ($method === 'post') {
            return $contact->create();
        }
        if ($method === 'get' && !$id) {
            return $contact->getAll();
        }
        if ($method === 'get' && is_numeric($id)) {
            return $contact->getById($id);
        }
        if ($method === 'put' && is_numeric($id) && $action === 'status') {
            return $contact->updateStatus($id);
        }
    }

    // Route non trouvée
    return \Response::notFound('Endpoint not found');

} catch (\Exception $e) {
    return \Response::error('Internal error: ' . $e->getMessage(), 500);
}
