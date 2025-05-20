<?php

namespace App\Services\AdminPanel;

use App\Repositories\Interfaces\AdminPanel\ProjectRepositoryInterface;

class ProjectService
{
    protected $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getAllProjectsForAccount($account)
    {
        if ($account->role === 'SubAdmin') {
            return $this->projectRepository->getByAccount($account->id);
        }
        
        return $this->projectRepository->all();
    }

    public function createProject(array $data)
    {
        return $this->projectRepository->create($data);
    }

    public function updateProject($id, array $data)
    {
        return $this->projectRepository->update($id, $data);
    }

    public function deleteProject($id)
    {
        return $this->projectRepository->delete($id);
    }

    public function findProject($id)
    {
        return $this->projectRepository->find($id);
    }
}