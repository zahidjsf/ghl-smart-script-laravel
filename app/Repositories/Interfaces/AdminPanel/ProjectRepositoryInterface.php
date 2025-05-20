<?php


namespace App\Repositories\Interfaces\AdminPanel;

interface ProjectRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByAccount($accountId);
}