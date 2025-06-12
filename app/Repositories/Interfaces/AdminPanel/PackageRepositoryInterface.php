<?php

namespace App\Repositories\Interfaces\AdminPanel;

interface PackageRepositoryInterface
{
    public function all();
    public function getPackagesForDatatable($userId, $role);
    public function find($id);
    public function findWithDetails($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getSystemProjects();
    public function createPackageDetail($packageId, array $detailData);
    public function updatePackageDetail($detailId, array $detailData);
    public function deletePackageDetail($detailId);
    public function deleteAllPackageDetails($packageId);
    public function removeProjectFromPackage($packageId, $detailId);
}
