<?php

namespace App\Repositories\FrontPanel;

use App\Models\Location;
use App\Models\CustomValue;
use App\Models\CollectionAssign;
use App\Helper\CRM;
use App\Repositories\Interfaces\FrontPanel\CVSmartRewardRepositoryInterface;

class CVSmartRewardRepository implements CVSmartRewardRepositoryInterface
{
    public function getLocationById($id)
    {
        return Location::find($id);
    }

    public function getGhlCustomValues($userId, $locationId)
    {
        $url = 'locations/' . $locationId . '/customValues';
        return CRM::crmV2($userId, $url, 'get', '', [], false, $locationId);
    }

    public function getCollectionIds($locationId)
    {
        return CollectionAssign::where('loc_id', $locationId)
            ->pluck('col_id')
            ->toArray();
    }

    public function getCustomValueDefinitions(array $collectionIds)
    {
        return CustomValue::whereIn('col_id', $collectionIds)
            ->orderBy('cv_order')
            ->get()
            ->toArray();
    }

    public function updateGhlCustomValue($userId, $locationId, $cvId, array $data)
    {
        try {
            $url = 'locations/' . $locationId . '/customValues/' . $cvId;
            return CRM::crmV2($userId, $url, 'put', $data, [], false, $locationId);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
