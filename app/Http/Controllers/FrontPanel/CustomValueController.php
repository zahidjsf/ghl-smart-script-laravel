<?php

namespace App\Http\Controllers\FrontPanel;

use App\Helper\CRM;
use App\Http\Controllers\Controller;
use App\Models\CollectionAssign;
use App\Models\CustomValue;
use App\Models\CustomValueCollection;
use App\Models\Location;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomValueController extends Controller
{
    public function cvUpdater()
    {
        return view('frontpanel.cvupdater.index');
    }

    public function getCollections()
    {
        $authUser = auth()->user();
        $user_id = $authUser->id;

        $locations = CustomValueCollection::where(['a_id' => $user_id]);
        return DataTables::of($locations)
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

    public function addcollection()
    {
        $authUser = auth()->user();
        $a_id = $authUser->id;
        $agencyLocations = Location::where('a_id', $a_id)->get();
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
            DB::beginTransaction();
            $parts = explode('|', $request->locations);
            $orig_loc_id = $parts[0] ?? '';
            $secondValue = $parts[1] ?? '';
            $cf_loc = $request->cf_loc;
            if ($cf_loc == 0) {
                $cf_loc = $secondValue;
            }
            $collection = new CustomValueCollection();
            $collection->a_id = auth()->user()->id;
            $collection->orig_loc_id = $orig_loc_id;
            $collection->cf_loc_id = $cf_loc;
            $collection->name = $request->collection_name;
            $collection->description = $request->collection_description;
            $collection->save();

            foreach ($request->cv as $index => $cvData) {
                if (isset($cvData['select'])) {
                    $customValue = new CustomValue();
                    $customValue->a_id = auth()->user()->id;
                    $customValue->col_id = $collection->id;
                    $customValue->name = str_replace('"', '', $cvData['name'] ?? '');
                    $customValue->mergeKey = $cvData['fieldKey'] ?? '';
                    $customValue->fieldType = $cvData['fieldType'] ?? '';
                    $customValue->tooltip = $cvData['tooltip'] ?? '';
                    $customValue->cvaction = ($cvData['readonly'] ?? false) ? 'readonly' : '';
                    $customValue->cvattribute = ($cvData['wysiwyg'] ?? false) ? 'wysiwyg' : '';
                    $customValue->custom_field = $cvData['customField'] ?? '';
                    $customValue->resources = $cvData['resource'] ?? '';
                    $customValue->cv_order = $cvData['sort_order'] ?? 0;
                    $customValue->defaultv = $cvData['defaultv'] ?? null;
                    $customValue->save();
                }
            }

            $collectionAssign = new CollectionAssign();
            $collectionAssign->loc_id = $collection->orig_loc_id;
            $collectionAssign->col_id = $collection->id;
            $collectionAssign->a_id = $collection->a_id;
            $collectionAssign->proj_id = 7;
            $collectionAssign->save();
            DB::commit();
            $msg = "Collection Created & Custom Values Added";
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', urlencode($e->getMessage()));
        }
    }
    public function getCustomValue(Request $request, $location_id)
    {
        $user_id = $request->user_id;
        $cf_location_id = $request->cf_location_id;
        $url = 'locations/' . $location_id . '/customValues';
        $cv = CRM::crmV2($user_id, $url,  'get', '', [], false, $location_id);
        $url1 = 'locations/' . $cf_location_id . '/customFields';
        $cf = CRM::crmV2($user_id, $url1,  'get', '', [], false, $cf_location_id);
        return view('frontpanel.cvupdater.get-cv-table-data', [
            'cv' => $cv->customValues,
            'cf' => $cf->customFields
        ])->render();
    }

    public function editCollection($id)
    {
        $authUser = auth()->user();
        $a_id = $authUser->id;
        $collection = CustomValueCollection::where('a_id', $a_id)->findOrFail($id);
        $agencyLocations = Location::where('a_id', $a_id)->get();
        return view('frontpanel.cvupdater.edit-collection', get_defined_vars());
    }

    public function updateCollectionCustomValues(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'cf_location_id' => 'required',
            'collection_id' => 'required'
        ]);

        $user_id = auth()->user()->id;
        $location_id = $request->location_id;
        $cf_location_id = $request->cf_location_id;
        $collection_id = $request->collection_id;

        try {
            // Get all custom values from the selected location
            $url = 'locations/' . $location_id . '/customValues';
            $cvResponse = CRM::crmV2($user_id, $url, 'get', '', [], false, $location_id);
            $allCustomValues = $cvResponse->customValues ?? [];

            // Get custom fields from the target location
            $url = 'locations/' . $cf_location_id . '/customFields';
            $cfResponse = CRM::crmV2($user_id, $url, 'get', '', [], false, $cf_location_id);
            $customFields = $cfResponse->customFields ?? [];

            // Get existing custom values for this collection
            $collection = CustomValueCollection::with('customValues')->find($collection_id);
            $dbCustomValues = $collection ? $collection->customValues->toArray() : [];

            return response()->json([
                'success' => true,
                'html' => view('frontpanel.cvupdater.update-cv-table-data', [
                    'cv' => [
                        'customValues' => $allCustomValues,
                        'db' => $dbCustomValues
                    ],
                    'cf' => $customFields
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
            DB::beginTransaction();

            $parts = explode('|', $request->locations);
            $orig_loc_id = $parts[0] ?? '';
            $secondValue = $parts[1] ?? '';
            $cf_loc = $request->cf_loc;
            if ($cf_loc == 0) {
                $cf_loc = $secondValue;
            }
            // Update the collection
            $collection = CustomValueCollection::where('a_id', auth()->user()->id)->findOrFail($id);
            $collection->orig_loc_id = $orig_loc_id;
            $collection->cf_loc_id = $cf_loc;
            $collection->name = $request->collection_name;
            $collection->description = $request->collection_description;
            $collection->save();

            // Process each submitted custom value
            foreach ($request->cv as $index => $cvData) {
                if (isset($cvData['select'])) {
                    // Check if this is an existing custom value (has cv_id)
                    if (!empty($cvData['cv_id'])) {
                        $customValue = CustomValue::where('col_id', $collection->id)
                            ->find($cvData['cv_id']);
                    }

                    // If not found or new custom value
                    if (empty($customValue)) {
                        $customValue = new CustomValue();
                        $customValue->a_id = auth()->user()->id;
                        $customValue->col_id = $collection->id;
                    }

                    $customValue->name = str_replace('"', '', $cvData['name'] ?? '');
                    $customValue->mergeKey = $cvData['fieldKey'] ?? '';
                    $customValue->fieldType = $cvData['fieldType'] ?? '';
                    $customValue->tooltip = $cvData['tooltip'] ?? '';
                    $customValue->cvaction = ($cvData['readonly'] ?? false) ? 'readonly' : '';
                    $customValue->cvattribute = ($cvData['wysiwyg'] ?? false) ? 'wysiwyg' : '';
                    $customValue->custom_field = $cvData['customField'] ?? '';
                    $customValue->resources = $cvData['resource'] ?? '';
                    $customValue->cv_order = $cvData['sort_order'] ?? 0;
                    $customValue->defaultv = $cvData['defaultv'] ?? null;
                    $customValue->save();
                }
            }

            DB::commit();
            $msg = "Collection Updated Successfully";
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', urlencode($e->getMessage()));
        }
    }

    public function copyCollection($id)
    {
        $collection = CustomValueCollection::find($id);
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

            DB::beginTransaction();
            $originalCollection = CustomValueCollection::findOrFail($request->col);
            $newCollection = $originalCollection->replicate();
            $newCollection->name = $request->name;
            $newCollection->description = $request->col_desc ?? $originalCollection->description;
            $newCollection->save();
            $originalValues = CustomValue::where('col_id', $originalCollection->id)->get();
            foreach ($originalValues as $value) {
                $newValue = $value->replicate();
                $newValue->col_id = $newCollection->id;
                $newValue->save();
            }

            $collectionAssign = new CollectionAssign();
            $collectionAssign->loc_id = $originalCollection->orig_loc_id;
            $collectionAssign->col_id = $newCollection->id;
            $collectionAssign->a_id = $originalCollection->a_id;
            $collectionAssign->proj_id = 7;
            $collectionAssign->save();

            DB::commit();
            return redirect()->back()->with('success', 'Collection duplicated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to duplicate collection: ' . $e->getMessage());
        }
    }

    public function removeCollection($collectionId)
    {
        try {
            DB::beginTransaction();
            $collection = CustomValueCollection::findOrFail($collectionId);
            if ($collection->locked === 'yes') {
                return response()->json(['status' => 'error', 'message' => 'This collection is locked and cannot be removed.']);
            }
            CustomValue::where('col_id', $collectionId)->delete();
            CollectionAssign::where('col_id', $collectionId)->delete();
            DB::table('collection_assign')->where('col_id', $collectionId)->delete();
            $collection->delete();
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Collection removed successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to remove collection: ' . $e->getMessage());
        }
    }
}
