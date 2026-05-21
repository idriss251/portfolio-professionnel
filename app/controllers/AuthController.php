<?php
/**
 * AuthController - Gestion authentification et profil
 */

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function login()
    {
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data || !isset($data['email'], $data['password'])) {
                return \Response::badRequest('Email et mot de passe requis');
            }

            $user = $this->user->findByEmail($data['email']);
            
            if (!$user || !$this->user->verifyPassword($data['password'], $user['password'])) {
                return \Response::unauthorized('Identifiants invalides');
            }

            // Générer token JWT
            $token = \JwtHelper::generate([
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ]);

            return \Response::success([
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role']
                ]
            ], 'Connexion réussie');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 400);
        }
    }

    public function register()
    {
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data || !isset($data['email'], $data['password'])) {
                return \Response::badRequest('Email et mot de passe requis');
            }

            $userId = $this->user->create($data);

            $newUser = $this->user->findById($userId);
            $token = \JwtHelper::generate([
                'id' => $newUser['id'],
                'email' => $newUser['email'],
                'role' => $newUser['role']
            ]);

            return \Response::success([
                'token' => $token,
                'user' => $newUser
            ], 'Inscription réussie', 201);

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 400);
        }
    }

    public function profile()
    {
        try {
            $token = \JwtHelper::getFromHeader();
            
            if (!$token) {
                return \Response::unauthorized('Token manquant');
            }

            $payload = \JwtHelper::verify($token);
            
            if (!$payload) {
                return \Response::unauthorized('Token invalide');
            }

            $user = $this->user->findById($payload['id']);
            
            if (!$user) {
                return \Response::notFound('Utilisateur non trouvé');
            }

            return \Response::success($user, 'Profil récupéré');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 500);
        }
    }

    public function updateProfile()
    {
        try {
            $token = \JwtHelper::getFromHeader();
            $payload = \JwtHelper::verify($token);
            
            if (!$payload) {
                return \Response::unauthorized('Token invalide');
            }

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $this->user->updateProfile($payload['id'], $data);
            $user = $this->user->findById($payload['id']);

            return \Response::success($user, 'Profil mis à jour');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 400);
        }
    }
}
