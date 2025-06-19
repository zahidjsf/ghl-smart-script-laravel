<?php

namespace App\Services\FrontPanel;

use App\Models\CustomValue;
use App\Repositories\Interfaces\FrontPanel\CustomValueRepositoryInterface;
use Yajra\DataTables\Facades\DataTables;

class CustomValueService
{
    protected $customValueRepository;

    public function __construct(CustomValueRepositoryInterface $customValueRepository)
    {
        $this->customValueRepository = $customValueRepository;
    }

    public function getCollectionsForDatatable($userId)
    {
        $collections = $this->customValueRepository->getCollectionsForDatatable($userId);

        return DataTables::of($collections)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<a href="' . route('frontend.smart_reward.editcollection', ['id' => $row->id]) . '" class="btn btn-sm btn-primary">Edit Collection</a> ';
                $html .= '<a style="margin-right:4px" href="#" data-url="' . route('frontend.smart_reward.copycollection', ['id' => $row->id]) . '" class="btn btn-sm btn-success duplicate-collection">Copy</a>';
                if ($row->locked !== 'yes') {
                    $html .= '<a style="margin-right:4px" href="#" data-url="' . route('frontend.smart_reward.removecollection', ['id' => $row->id]) . '" class="btn btn-sm btn-danger remove-collection">Remove</a>';
                }
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getCollection($id, $userId)
    {
        return $this->customValueRepository->findCollection($id, $userId);
    }

    public function getAgencyLocations($userId)
    {
        return $this->customValueRepository->getAgencyLocations($userId);
    }

    public function createCollection(array $data)
    {
        $parts = explode('|', $data['locations']);
        $orig_loc_id = $parts[0] ?? '';
        $secondValue = $parts[1] ?? '';
        $cf_loc = $data['cf_loc'] == 0 ? $secondValue : $data['cf_loc'];

        $collectionData = [
            'a_id' => $data['a_id'],
            'orig_loc_id' => $orig_loc_id,
            'cf_loc_id' => $cf_loc,
            'name' => $data['collection_name'],
            'description' => $data['collection_description'],
        ];

        $collection = $this->customValueRepository->createCollection($collectionData);

        foreach ($data['cv'] as $index => $cvData) {
            if (isset($cvData['select'])) {
                $this->customValueRepository->createCustomValue([
                    'a_id' => $data['a_id'],
                    'col_id' => $collection->id,
                    'name' => $cvData['name'] ?? '',
                    'fieldKey' => $cvData['fieldKey'] ?? '',
                    'fieldType' => $cvData['fieldType'] ?? '',
                    'tooltip' => $cvData['tooltip'] ?? '',
                    'readonly' => $cvData['readonly'] ?? false,
                    'wysiwyg' => $cvData['wysiwyg'] ?? false,
                    'customField' => $cvData['customField'] ?? '',
                    'resource' => $cvData['resource'] ?? '',
                    'sort_order' => $cvData['sort_order'] ?? 0,
                    'defaultv' => $cvData['defaultv'] ?? null,
                ]);
            }
        }

        $this->customValueRepository->assignCollectionToLocation([
            'loc_id' => $collection->orig_loc_id,
            'col_id' => $collection->id,
            'a_id' => $collection->a_id,
        ]);

        return $collection;
    }

    public function updateCollection($id, array $data)
    {
        $parts = explode('|', $data['locations']);
        $orig_loc_id = $parts[0] ?? '';
        $secondValue = $parts[1] ?? '';
        $cf_loc = $data['cf_loc'] == 0 ? $secondValue : $data['cf_loc'];

        $collectionData = [
            'a_id' => $data['a_id'] ?? LoginUser(true),
            'orig_loc_id' => $orig_loc_id,
            'cf_loc_id' => $cf_loc,
            'name' => $data['collection_name'],
            'description' => $data['collection_description'],
        ];

        $collection = $this->customValueRepository->updateCollection($id, $collectionData);

        foreach ($data['cv'] as $index => $cvData) {
            if (isset($cvData['select'])) {
                $cvData['col_id'] = $collection->id;
                $cvData['a_id'] = $data['a_id'] ?? LoginUser(true);
                $this->customValueRepository->updateCustomValue($cvData['cv_id'] ?? null, $cvData);
            }else{
                $delCV = CustomValue::where('id', $cvData['cv_id'])->first();
                $delCV->delete();
            }
        }

        return $collection;
    }

    public function duplicateCollection($originalId, array $data)
    {
        return $this->customValueRepository->duplicateCollection($originalId, [
            'name' => $data['name'],
            'description' => $data['col_desc'] ?? null,
        ]);
    }

    public function deleteCollection($id)
    {
        return $this->customValueRepository->deleteCollection($id);
    }

    public function getCustomValuesAndFields($userId, $locationId, $cfLocationId)
    {
        $customValues = $this->customValueRepository->getCustomValuesFromCRM($userId, $locationId);
        $customFields = $this->customValueRepository->getCustomFieldsFromCRM($userId, $cfLocationId);

        return [
            'customValues' => $customValues,
            'customFields' => $customFields,
        ];
    }

    public function getCollectionWithCustomValues($collectionId, $userId)
    {
        $collection = $this->customValueRepository->findCollection($collectionId, $userId);
        $customValues = $collection->customValues()->get()->toArray();

        return [
            'collection' => $collection,
            'customValues' => $customValues,
        ];
    }
}
