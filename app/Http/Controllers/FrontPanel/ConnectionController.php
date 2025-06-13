<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\CRM;
use App\Models\CRMToken;

class ConnectionController extends Controller
{
    public function locationDisplay()
    {
        $userId = auth()->user()->id;
        $alreadyConnected = CRMToken::where(['a_id' => $userId, 'type' => 'location'])->pluck('locationId')->toArray();
        return view('frontpanel.locationconnection.location_display', [
            'alreadyConnected' => $alreadyConnected
        ]);
    }
    
    public function fetchLocations(Request $request)
    {
        $userId = auth()->user()->id;
        $skip = $request->input('skip', 0);
        $limit = 100;
        $urlmain = "locations/search?deleted=false&limit={$limit}&skip={$skip}";
        $locations = CRM::agencyV2($userId, $urlmain);
        $locations = $locations->locations ?? [];
        return response()->json([
            'locations' => $locations,
            'hasMore' => count($locations) === $limit + $skip,
            'nextSkip' => $skip + $limit  // Send back the next skip value
        ]);
    }

    public function finalConnect(Request $request)
    {
        $validated = $request->validate([
            'connectLocations' => 'required|array'
        ]);
        try {
            $userId = auth()->user()->id;
            if ($request->start == 0) {
                $alreadyConnected = CRMToken::where(['a_id' => $userId, 'type' => 'location'])->pluck('locationId')->toArray();
                $toDisconnect = array_diff($alreadyConnected, $request->checkedLocations);
                foreach ($toDisconnect as $delLoc) {
                    $del = CRMToken::where(['a_id' => $userId, 'locationId' => $delLoc])->first();
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
