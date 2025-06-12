<?php

namespace App\Repositories\FrontPanel;

use App\Models\CollectionAssign;
use App\Models\CustomValue;
use App\Models\CustomValueCollection;
use App\Models\Location;
use App\Repositories\Interfaces\FrontPanel\CustomValueRepositoryInterface;
use App\Helper\CRM;
use Illuminate\Support\Facades\DB;

class CustomValueRepository implements CustomValueRepositoryInterface
{
    public function getCollectionsForDatatable($userId)
    {
        return CustomValueCollection::where(['a_id' => $userId]);
    }

    public function findCollection($id, $userId)
    {
        return CustomValueCollection::where('a_id', $userId)->findOrFail($id);
    }

    public function createCollection(array $data)
    {
        return DB::transaction(function () use ($data) {
            $collection = new CustomValueCollection();
            $collection->a_id = $data['a_id'];
            $collection->orig_loc_id = $data['orig_loc_id'];
            $collection->cf_loc_id = $data['cf_loc_id'];
            $collection->name = $data['name'];
            $collection->description = $data['description'];
            $collection->save();

            return $collection;
        });
    }

    public function updateCollection($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $collection = CustomValueCollection::where('a_id', $data['a_id'])->findOrFail($id);
            $collection->orig_loc_id = $data['orig_loc_id'];
            $collection->cf_loc_id = $data['cf_loc_id'];
            $collection->name = $data['name'];
            $collection->description = $data['description'];
            $collection->save();

            return $collection;
        });
    }

    public function deleteCollection($id)
    {
        return DB::transaction(function () use ($id) {
            $collection = CustomValueCollection::findOrFail($id);
            if ($collection->locked === 'yes') {
                throw new \Exception('This collection is locked and cannot be removed.');
            }

            $this->deleteCustomValuesByCollection($collection->id);
            $this->removeCollectionAssignment($collection->id);

            return $collection->delete();
        });
    }

    public function duplicateCollection($originalId, array $newData)
    {
        return DB::transaction(function () use ($originalId, $newData) {
            $originalCollection = CustomValueCollection::findOrFail($originalId);
            $newCollection = new CustomValueCollection();

            $newCollection->a_id = $originalCollection->a_id;
            $newCollection->orig_loc_id = $originalCollection->orig_loc_id;
            $newCollection->cf_loc_id = $originalCollection->cf_loc_id;
            $newCollection->name = $newData['name'];
            $newCollection->description = $newData['description'] ?? $originalCollection->description;
            $newCollection->save();

            $originalValues = CustomValue::where('col_id', $originalCollection->id)->get();
            foreach ($originalValues as $value) {
                $newValue = new CustomValue();
                $newValue->a_id = $value->a_id;
                $newValue->col_id = $newCollection->id;
                $newValue->name = $value->name;
                $newValue->mergeKey = $value->mergeKey;
                $newValue->fieldType = $value->fieldType;
                $newValue->tooltip = $value->tooltip;
                $newValue->cvaction = $value->cvaction;
                $newValue->cvattribute = $value->cvattribute;
                $newValue->custom_field = $value->custom_field;
                $newValue->resources = $value->resources;
                $newValue->cv_order = $value->cv_order;
                $newValue->defaultv = $value->defaultv;
                $newValue->save();
            }

            $collectionAssign = new CollectionAssign();
            $collectionAssign->loc_id = $originalCollection->orig_loc_id;
            $collectionAssign->col_id = $newCollection->id;
            $collectionAssign->a_id = $originalCollection->a_id;
            $collectionAssign->proj_id = 7;
            $collectionAssign->save();

            return $newCollection;
        });
    }

    public function getAgencyLocations($userId)
    {
        return Location::where('a_id', $userId)->get();
    }

    public function getCustomValuesFromCRM($userId, $locationId)
    {
        $url = 'locations/' . $locationId . '/customValues';
        $response = CRM::crmV2($userId, $url, 'get', '', [], false, $locationId);
        return $response->customValues ?? [];
    }

    public function getCustomFieldsFromCRM($userId, $cfLocationId)
    {
        $url = 'locations/' . $cfLocationId . '/customFields';
        $response = CRM::crmV2($userId, $url, 'get', '', [], false, $cfLocationId);
        return $response->customFields ?? [];
    }

    public function createCustomValue(array $data)
    {
        $customValue = new CustomValue();
        $customValue->a_id = $data['a_id'];
        $customValue->col_id = $data['col_id'];
        $customValue->name = str_replace('"', '', $data['name'] ?? '');
        $customValue->mergeKey = $data['fieldKey'] ?? '';
        $customValue->fieldType = $data['fieldType'] ?? '';
        $customValue->tooltip = $data['tooltip'] ?? '';
        $customValue->cvaction = ($data['readonly'] ?? false) ? 'readonly' : '';
        $customValue->cvattribute = ($data['wysiwyg'] ?? false) ? 'wysiwyg' : '';
        $customValue->custom_field = $data['customField'] ?? '';
        $customValue->resources = $data['resource'] ?? '';
        $customValue->cv_order = $data['sort_order'] ?? 0;
        $customValue->defaultv = $data['defaultv'] ?? null;
        $customValue->save();

        return $customValue;
    }

    public function updateCustomValue($id, array $data)
    {
        $customValue = CustomValue::where('col_id', $data['col_id'])->find($id);
        if (!$customValue) {
            $customValue = new CustomValue();
            $customValue->a_id = $data['a_id'];
            $customValue->col_id = $data['col_id'];
        }

        $customValue->name = str_replace('"', '', $data['name'] ?? '');
        $customValue->mergeKey = $data['fieldKey'] ?? '';
        $customValue->fieldType = $data['fieldType'] ?? '';
        $customValue->tooltip = $data['tooltip'] ?? '';
        $customValue->cvaction = ($data['readonly'] ?? false) ? 'readonly' : '';
        $customValue->cvattribute = ($data['wysiwyg'] ?? false) ? 'wysiwyg' : '';
        $customValue->custom_field = $data['customField'] ?? '';
        $customValue->resources = $data['resource'] ?? '';
        $customValue->cv_order = $data['sort_order'] ?? 0;
        $customValue->defaultv = $data['defaultv'] ?? null;
        $customValue->save();

        return $customValue;
    }

    public function deleteCustomValuesByCollection($collectionId)
    {
        return CustomValue::where('col_id', $collectionId)->delete();
    }

    public function assignCollectionToLocation(array $data)
    {
        $collectionAssign = new CollectionAssign();
        $collectionAssign->loc_id = $data['loc_id'];
        $collectionAssign->col_id = $data['col_id'];
        $collectionAssign->a_id = $data['a_id'];
        $collectionAssign->proj_id = 7;
        $collectionAssign->save();

        return $collectionAssign;
    }

    public function removeCollectionAssignment($collectionId)
    {
        return CollectionAssign::where('col_id', $collectionId)->delete();
    }
}
