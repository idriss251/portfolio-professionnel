<?php
/**
 * ProjectController - Gestion projets du portfolio
 */

namespace App\Controllers;

use App\Models\Project;

class ProjectController
{
    private $project;

    public function __construct()
    {
        $this->project = new Project();
    }

    public function getAll()
    {
        try {
            $filters = [];
            
            // Filtrer par statut (par défaut published pour les users)
            if (!$this->isAdmin()) {
                $filters['status'] = 'published';
            } elseif (isset($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }

            if (isset($_GET['category'])) {
                $filters['category'] = $_GET['category'];
            }

            if (isset($_GET['featured'])) {
                $filters['featured'] = $_GET['featured'] == '1';
            }

            $projects = $this->project->getAll($filters);

            return \Response::success($projects, 'Projets récupérés');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 500);
        }
    }

    public function getById($id)
    {
        try {
            $project = $this->project->getById($id);

            if (!$project) {
                return \Response::notFound('Projet non trouvé');
            }

            if ($project['status'] !== 'published' && !$this->isAdmin()) {
                return \Response::forbidden();
            }

            return \Response::success($project, 'Projet récupéré');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {
            if (!$this->isAdmin()) {
                return \Response::forbidden('Accès admin requis');
            }

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $projectId = $this->project->create($data);
            $project = $this->project->getById($projectId);

            return \Response::success($project, 'Projet créé', 201);

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 400);
        }
    }

    public function update($id)
    {
        try {
            if (!$this->isAdmin()) {
                return \Response::forbidden('Accès admin requis');
            }

            $project = $this->project->getById($id);
            if (!$project) {
                return \Response::notFound();
            }

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $this->project->update($id, $data);
            $updated = $this->project->getById($id);

            return \Response::success($updated, 'Projet mis à jour');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 400);
        }
    }

    public function delete($id)
    {
        try {
            if (!$this->isAdmin()) {
                return \Response::forbidden('Accès admin requis');
            }

            $project = $this->project->getById($id);
            if (!$project) {
                return \Response::notFound();
            }

            $this->project->delete($id);

            return \Response::success([], 'Projet supprimé');

        } catch (\Exception $e) {
            return \Response::error($e->getMessage(), 400);
        }
    }

    public function like($id)
    {
        try {
            $project = $this->project->getById($id);
            if (!$project) {
                return \Response::notFound();
            }

            $this->project->addLike($id);
            $updated = $this->project->getById($id);

            return \Response::success($updated, 'Like ajouté');

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
