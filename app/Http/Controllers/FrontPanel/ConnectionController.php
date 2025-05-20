<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\CRM;
use App\Models\CRMToken;

class ConnectionController extends Controller
{
    public function locationDisplay(){

        $userId = auth()->user()->id;
        // $users = User::where('role', 0)->where('is_active', 1)->where('separate_location', 0)->pluck('location', 'id')->toArray();
        // $users = User::where('role', 0)->where('is_active', 1)->whereNotNull('location')->get();
        $crmlocationID = [];

        $allLocations = [];
        $limit = 100;
        $skip = 0;

        do {
            $urlmain = "locations/search?deleted=false&limit={$limit}&skip={$skip}";
            $locations = CRM::agencyV2($userId, $urlmain);
            // dd($locations);
            $locations = $locations->locations ?? [];

            $allLocations = array_merge($allLocations, $locations);
            $hasMore = count($locations) === $limit;
            $skip += $limit;
        } while ($hasMore);

        foreach ($allLocations as $loc) {
            $crmlocationID[$loc->name] = $loc->id;
        }

        $alreadyConnected = CRMToken::where(['a_id'=> $userId, 'type' => 'location'])->whereIn('locationId', $crmlocationID)->pluck('locationId')->toArray();
        return view('frontpanel.locationconnection.location_display', get_defined_vars());
    }

    public function finalConnect(Request $request)
    {
        $validated = $request->validate([
            'connectLocations' => 'required|array'
        ]);
        try {
            $userId = auth()->user()->id;

            if($request->start == 0){
                $alreadyConnected = CRMToken::where(['a_id'=> $userId, 'type' => 'location'])->pluck('locationId')->toArray();
                $toDisconnect = array_diff($alreadyConnected, $request->checkedLocations);
                foreach ($toDisconnect as $delLoc) {
                  $del = CRMToken::where(['a_id'=> $userId, 'locationId' => $delLoc])->first();
                  $del->delete();
                }
            }
            $results = [];
            foreach ($validated['connectLocations'] as $locationId) {
                $resp = CRM::getLocationAccessTokenFirstTimeByCompany($userId, $locationId);
                // $results[$locationId] = true;
            }
            return response()->json([
                'success' => true,
                'message' => 'Batch processing complete',
                'results' => $results,
                'processed_count' => count($validated['connectLocations'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch processing failed',
                'processed_count' => count($results),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }






}
