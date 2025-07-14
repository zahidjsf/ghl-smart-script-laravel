<?php

namespace Modules\RewardAndPromotions\Http\Controllers;

use App\Helper\CRM;
use App\Models\LoyaltyContactsReferred;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $locationId = 'ZZHap8IaodEICUFX5ua2';
        // Validate location ID
        if (empty($locationId) || $locationId == "{{location.id}}") {
            return response("Please provide a valid location ID", 400);
        }

        // Get location data
        $location = DB::table('locations')->where('loc_id', $locationId)->first();
        if (!$location) {
            return response("Location Not Found", 404);
        }

        // Get points name from location settings
        $locSet = getLocationSettings($locationId);
        $pointsName = $locSet['pointsName'] ?? "Points";

        // Process search if any
        $search = $request->input('Search', '');
        $searchSelect = $request->input('type', 'referrer') == "referree" ? "referree" : "referrer";
        $where = "";

        if (!empty($search)) {
            $name = explode(" ", $search);
            $char = array("'", "\"");
            $first_name = (!empty($name[0])) ? str_replace($char, "", (string) $name[0]) : "";
            $last_name = (!empty($name[1])) ? str_replace($char, "", (string) $name[1]) : str_replace($char, "", (string) $name[0]);

            if ($searchSelect == "referree") {
                $where = " AND (firstName LIKE '%" . $first_name . "%' OR lastName LIKE '%" . $last_name . "%')";
            } else {
                $where = " AND (refName LIKE '%" . $first_name . "%' OR refName LIKE '%" . $last_name . "%')";
            }
        }

        // Fixed SQL query - only select what we need for grouping
        $leaderboardData = DB::select("
            SELECT referrer, MAX(refName) as refName, MAX(refPhone) as refPhone, count(IF(type=3,1,NULL)) as invites, count(IF(type=1,1,NULL)) as leads, count(IF(type=2,1,NULL)) as referrals FROM loyaltyContactsReferred WHERE loc_id = ? AND status = 1 " . $where . " GROUP BY referrer ORDER BY referrals DESC", [$locationId]);

        // Process leaderboard data
        $leaderBoard = [];
        $parentURL = getAgencyURL($location->a_id);

        foreach ($leaderboardData as $index => $data) {
            $contact = null;
            $referrer = true;

            if (!empty(trim($data->refName))) {
                $name = $data->refName;
            } else {
                if (!empty($data->referrer)) {
                    $contact = CRM::crmV2($location->a_id, 'contacts'.$data->referrer, 'get', '', false,$locationId);
                    // $contact = $this->lookupContactById($data->referrer, $apiKey, $locationId);
                    $contact = json_decode($contact, true);

                    $firstName = $contact['contact']['firstName'] ?? '';
                    $lastName = $contact['contact']['lastName'] ?? '';
                    $email = $contact['contact']['emailLowerCase'] ?? '';
                    $phone = $contact['contact']['phone'] ?? '';
                    $name = $firstName . " " . $lastName;

                    $this->updateContactReferred($data->referrer, $name, $email, $phone);
                } else {
                    $name = "No Referrer";
                    $referrer = false;
                }
            }

            $leaderBoard[] = [
                'pos' => $index + 1,
                'id' => $data->referrer,
                'name' => $name,
                'referrals' => $data->referrals,
                'invites' => $data->invites,
                'leads' => $data->leads,
                'phone' => $data->refPhone,
                'location' => $locationId,
            ];
        }

        return view('rewardandpromotions::leadboard.leaderboard',
        [
            'leaderBoard' => $leaderBoard,
            'locationId' => $locationId,
            'search' => $search,
            'searchSelect' => $searchSelect,
            'parentURL' => $parentURL,
            'locationName' => $location->name,
            'agencyId' => $location->a_id,
            'pointsName' => $pointsName,
        ]);
    }

    private function updateContactReferred($referrerId, $name, $email, $phone)
    {
        $contact = LoyaltyContactsReferred::where('referrer', $referrerId)->first();
        if($contact){
             $contact->refName = $name;
             $contact->refEmail = $email;
             $contact->refPhone = $phone;
             $contact->save();
        }
    }
}
