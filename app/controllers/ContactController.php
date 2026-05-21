<?php
/**
 * ContactController - Gestion messages de contact
 */

namespace App\Controllers;

use App\Models\Contact;

class ContactController
{
    private $contact;

    public function __construct()
    {
        $this->contact = new Contact();
    }

    public function create()
    {
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                return \Response::badRequest('Données invalides');
            }

            $contactId = $this->contact->create($data);
            $newContact = $this->contact->getById($contactId);

            // TODO: Envoyer email de notification

            return \Response::success($newContact, 'Message envoyé avec succès', 201);

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 400);
        }
    }

    public function getAll()
    {
        try {
            if (!$this->isAdmin()) {
                return \Response::forbidden('Accès admin requis');
            }

            $filters = [];
            if (isset($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }

            $contacts = $this->contact->getAll($filters);

            return \Response::success($contacts, 'Messages récupérés');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 500);
        }
    }

    public function getById($id)
    {
        try {
            if (!$this->isAdmin()) {
                return \Response::forbidden('Accès admin requis');
            }

            $contact = $this->contact->getById($id);

            if (!$contact) {
                return \Response::notFound('Message non trouvé');
            }

            return \Response::success($contact, 'Message récupéré');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 500);
        }
    }

    public function updateStatus($id)
    {
        try {
            if (!$this->isAdmin()) {
                return \Response::forbidden('Accès admin requis');
            }

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!isset($data['status'])) {
                return \Response::badRequest('Statut requis');
            }

            $this->contact->updateStatus($id, $data['status']);
            $updated = $this->contact->getById($id);

            return \Response::success($updated, 'Statut mis à jour');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 400);
        }
    }

    private function isAdmin()
    {
        $token = \JwtHelper::getFromHeader();
        if (!$token) return false;

        $payload = \JwtHelper::verify($token);
        return $payload && $payload['role'] === 'admin';
    }
}
