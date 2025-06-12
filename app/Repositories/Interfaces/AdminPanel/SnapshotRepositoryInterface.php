<?php

namespace App\Repositories\Interfaces\AdminPanel;

interface SnapshotRepositoryInterface
{
    public function all();
    public function getSnapshotsForDatatable();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getSystemProjects();
    public function uploadImage($file);
    public function deleteImage($imagePath);
}
