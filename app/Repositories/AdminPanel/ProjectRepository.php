<?php
namespace App\Repositories\AdminPanel;

use App\Models\SystemProject;
use App\Repositories\Interfaces\AdminPanel\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{
    protected $model;

    public function __construct(SystemProject $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        $project = new $this->model;
        $project->name = $data['name'];
        $project->description = $data['description'] ?? null;
        $project->cv_collections = $data['cv_collections'] ?? null;
        $project->inMembership = $data['inMembership'] ?? 'no';
        $project->a_id = $data['a_id'];
        $project->save();
        return $project;
    }

    public function update($id, array $data)
    {
        $project = $this->find($id);
        $project->name = $data['name'];
        $project->description = $data['description'] ?? null;
        $project->cv_collections = $data['cv_collections'] ?? null;
        $project->inMembership = $data['inMembership'] ?? 'no';
        $project->save();
        return $project;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function getByAccount($accountId)
    {
        return $this->model->where('a_id', $accountId)->get();
    }
}