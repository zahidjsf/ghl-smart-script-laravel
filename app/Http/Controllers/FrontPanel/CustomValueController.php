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
        return view('frontpanel.cvupdateer.index');
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
        return view('frontpanel.cvupdateer.add-collection', get_defined_vars());
    }

    public function createCollection(Request $request)
    {
        dd($request->all());
        return;
    }
    public function getCustomValue(Request $request, $location_id)
    {
        $user_id = $request->user_id;
        $cf_location_id = $request->cf_location_id;
        $url = 'locations/'.$location_id.'/customValues';
        $cv = CRM::crmV2($user_id, $url,  'get', '', [], false,$location_id);
        $url1 = 'locations/'.$cf_location_id.'/customFields';
        $cf = CRM::crmV2($user_id, $url1,  'get', '', [], false,$cf_location_id);
        return view('frontpanel.cvupdateer.get-cv-table-data', [
            'cv' => $cv->customValues,
            'cf' => $cf->customFields
        ])->render();
    }

    public function editCollection($id)
    {
        dd($id);
    }

    public function copyCollection($id)
    {
        $collection = CustomValueCollection::find($id);
        $view = view('frontpanel.cvupdateer.copy-collection', get_defined_vars())->render();
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
            $collectionAssign->proj_id = 2;
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
