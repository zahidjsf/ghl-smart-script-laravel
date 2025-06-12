<?php

namespace App\Repositories\Interfaces\AdminPanel;

interface AccountRepositoryInterface
{
    public function all();
    public function getAccountsForDatatable($currentAccount);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function generateLicense();
    public function encryptAPI($apikey);
    public function decryptAPI($apikey);
    public function updateProjectLicenses($accountId, $projectAccess);
    public function getSystemProjects();
    public function getAccountWithRelations($id);
    public function updateSSMembership($accountId, $status);
    public function getProjectLicense($accountId, $projectId);
    public function updateLicenseCount($accountId, $projectId, $count);
}
