<?php

namespace App\Repositories\Interfaces\FrontPanel;

interface CustomValueRepositoryInterface
{
    public function getCollectionsForDatatable($userId);
    public function findCollection($id, $userId);
    public function createCollection(array $data);
    public function updateCollection($id, array $data);
    public function deleteCollection($id);
    public function duplicateCollection($originalId, array $newData);
    public function getAgencyLocations($userId);
    public function getCustomValuesFromCRM($userId, $locationId);
    public function getCustomFieldsFromCRM($userId, $cfLocationId);
    public function createCustomValue(array $data);
    public function updateCustomValue($id, array $data);
    public function deleteCustomValuesByCollection($collectionId);
    public function assignCollectionToLocation(array $data);
    public function removeCollectionAssignment($collectionId);
}
