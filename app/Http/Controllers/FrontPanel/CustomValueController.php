<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use App\Services\FrontPanel\CustomValueService;
use Illuminate\Http\Request;

class CustomValueController extends Controller
{
    protected $customValueService;
    public function __construct(CustomValueService $customValueService)
    {
        $this->customValueService = $customValueService;
    }
    public function cvUpdater()
    {
        return view('frontpanel.cvupdater.index');
    }

    public function getCollections()
    {
        $userId = auth()->user()->id;
        return $this->customValueService->getCollectionsForDatatable($userId);
    }

    public function addcollection()
    {
        $userId = auth()->user()->id;
        $agencyLocations = $this->customValueService->getAgencyLocations($userId);
        return view('frontpanel.cvupdater.add-collection', get_defined_vars());
    }

    public function createCollection(Request $request)
    {
        $request->validate([
            'collection_name' => 'required',
            'collection_description' => 'required',
            'locations' => 'required'
        ]);
        if (empty($request->cv)) {
            return back()->with('error', 'No Custom Value is selected');
        }
        try {
            $data = $request->all();
            $data['a_id'] = auth()->user()->id;
            $this->customValueService->createCollection($data);

            return back()->with('success', "Collection Created & Custom Values Added");
        } catch (\Exception $e) {
            return back()->with('error', urlencode($e->getMessage()));
        }
    }
    public function getCustomValue(Request $request, $location_id)
    {
        $userId = $request->user_id;
        $cfLocationId = $request->cf_location_id;
        $data = $this->customValueService->getCustomValuesAndFields($userId, $location_id, $cfLocationId);
        return view('frontpanel.cvupdater.get-cv-table-data', [
            'cv' => $data['customValues'],
            'cf' => $data['customFields']
        ])->render();
    }

    public function editCollection($id)
    {
        $userId = auth()->user()->id;
        $collection = $this->customValueService->getCollection($id, $userId);
        $agencyLocations = $this->customValueService->getAgencyLocations($userId);
        return view('frontpanel.cvupdater.edit-collection', get_defined_vars());
    }

    public function updateCollectionCustomValues(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'cf_location_id' => 'required',
            'collection_id' => 'required'
        ]);

        try {
            $userId = auth()->user()->id;
            $locationId = $request->location_id;
            $cfLocationId = $request->cf_location_id;
            $collectionId = $request->collection_id;

            $customValuesData = $this->customValueService->getCustomValuesAndFields($userId, $locationId, $cfLocationId);
            $collectionData = $this->customValueService->getCollectionWithCustomValues($collectionId, $userId);

            return response()->json([
                'success' => true,
                'html' => view('frontpanel.cvupdater.update-cv-table-data', [
                    'cv' => [
                        'customValues' => $customValuesData['customValues'],
                        'db' => $collectionData['customValues']
                    ],
                    'cf' => $customValuesData['customFields']
                ])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load custom values: ' . $e->getMessage()
            ]);
        }
    }

    public function updateCollection(Request $request, $id)
    {
        $request->validate([
            'collection_name' => 'required',
            'collection_description' => 'required',
            'locations' => 'required'
        ]);

        if (empty($request->cv)) {
            return back()->with('error', 'No Custom Value is selected');
        }
        try {
            $data = $request->all();
            $data['a_id'] = auth()->user()->id;
            $this->customValueService->updateCollection($id, $data);

            return back()->with('success', "Collection Updated Successfully");
        } catch (\Exception $e) {
            return back()->with('error', urlencode($e->getMessage()));
        }
    }

    public function copyCollection($id)
    {
        $collection = $this->customValueService->getCollection($id, auth()->user()->id);
        $view = view('frontpanel.cvupdater.copy-collection', get_defined_vars())->render();
        return response()->json(['view' => $view]);
    }

    public function duplicateCollection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'col' => 'required|exists:customvaluecollections,id',
        ]);
        try {
            $this->customValueService->duplicateCollection($request->col, $request->all());
            return redirect()->back()->with('success', 'Collection duplicated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to duplicate collection: ' . $e->getMessage());
        }
    }

    public function removeCollection($collectionId)
    {
        try {
            $this->customValueService->deleteCollection($collectionId);
            return response()->json(['status' => 'success', 'message' => 'Collection removed successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
