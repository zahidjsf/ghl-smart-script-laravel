<?php

namespace App\Repositories\Interfaces\FrontPanel;

interface CVSmartRewardRepositoryInterface
{
    public function getLocationById($id);
    public function getGhlCustomValues($userId, $locationId);
    public function getCollectionIds($locationId);
    public function getCustomValueDefinitions(array $collectionIds);
    public function updateGhlCustomValue($userId, $locationId, $cvId, array $data);
}
