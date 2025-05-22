<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use App\Models\CustomValueCollection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

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
                $html .= '<a href="' . route('frontend.smart_reward.copycollection', ['id' => $row->id]) . '" class="btn btn-sm btn-success">Copy</a> ';

                if ($row->locked == 'yes') {
                    $html .= '<a href="' . route('frontend.smart_reward.removecollection', ['id' => $row->id]) . '" class="btn btn-sm btn-danger">Remove</a> ';
                }

                return $html;
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function addcollection()
    {

        $locationId = $request->input('id');
        $cfLocationId = $request->input('cf_loc');

        $location = Location::findOrFail($locationId);
        $oauth = $this->checkOauthLocation($location->loc_id);

        $cv = $this->ghlService->getCustomValues(
            decrypt($location->apikey),
            $location->loc_id
        );

        $cvDB = $collectionId ? CustomValueCollection::find($collectionId)->values : [];

        if ($cfLocationId == $location->loc_id || empty($cfLocationId)) {
            $cf = $this->ghlService->getCustomFields(
                decrypt($location->apikey),
                $location->loc_id
            );
        } else {
            $newLoc = Location::where('loc_id', $cfLocationId)->first();
            $cf = $this->ghlService->getCustomFields(
                decrypt($newLoc->apikey),
                $newLoc->loc_id
            );
        }

        if (isset($cv['error'])) {
            return response()->json(['error' => $cv['error']], 400);
        }


        return view('frontpanel.cvupdateer.add-collection');
    }

    public function editCollection($id)
    {
        dd($id);
    }

    public function copyCollection($id) {}

    public function removeCollection($id) {}
}
