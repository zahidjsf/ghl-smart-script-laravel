<?php

namespace Modules\RewardAndPromotions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Location;
use Illuminate\Pagination\LengthAwarePaginator;

class LoyaltyPointsController extends Controller
{
    public function index(Request $request)
    {
        $locationId = 'bPF3PypFAd66WXJXUGeV';
        // Validate location exists
        $location = Location::where('loc_id', $locationId)->firstOrFail();

        // dd(1237);

        // Get points name from location settings
        $pointsName = $this->getPointsName($location);

        // Process search if any
        $search = $request->input('Search', '');

        // Get leaderboard data
        $leaderBoard = $this->getLeaderBoardData($location, $search);

        // Pagination
        $perPage = 40;
        $currentPage = $request->input('page', 1);
        $leaderBoardPaginated = $this->paginateLeaderBoard($leaderBoard, $perPage, $currentPage);

        return view('rewardandpromotions::loyaltypoints.index', [
            'pointsName' => $pointsName,
            'leaderBoard' => $leaderBoardPaginated,
            'locationId' => $locationId,
            'search' => $search,
            'showMessage' => '',
            'locationName' => $location->name,
            'licenseKey' => $this->getLicenseKey($location->a_id),
            'parentURL' => $this->getAgencyURL($location->a_id),
        ]);
    }

    protected function getPointsName($location)
    {
        // Implement your logic to get points name from location settings
        $locSet = DB::table('locationsettings')
            ->where('loc_id', $location->loc_id)
            ->where('proj', 2)
            ->first();

        return $locSet->pointsName ?? "Points";
    }
    protected function getLeaderBoardData($location, $search)
    {
        $leaderBoard = [];
        $where = "AND event != 'redeem' AND archive != 1";

        if (!empty($search)) {
            $char = ["'", "\""];
            $search = str_replace(["(", ")", "-", "+"], "", $search);

            if (ctype_digit(str_replace([" ", "+", "-"], "", $search))) {
                $name[0] = str_replace([" ", "+", "-"], "", $search);
                $first_name = $name[0];
            } else {
                $name = explode(" ", $search);
            }

            $first_name = !empty($name[0]) ? str_replace($char, "", (string) $name[0]) : "";
            $last_name = !empty($name[1]) ? str_replace($char, "", (string) $name[1]) : str_replace($char, "", (string) $name[0]);

            $where = " AND (first_name LIKE '%" . $first_name . "%' OR last_name LIKE '%" . $last_name . "%' OR c.cid = '" . $name[0] . "' OR c.phone LIKE '%" . $name[0] . "%') AND event != 'redeem' AND archive != 1";

            $results = DB::select("
            SELECT
                r.cid,
                r.a_id,
                r.loc_id,
                c.first_name,
                c.last_name,
                c.email,
                c.phone,
                SUM(r.points) as totalPoints
            FROM LoyaltyRewards r
            LEFT JOIN LoyaltyContacts c ON r.cid = c.cid
            WHERE r.loc_id = ? " . $where . "
            GROUP BY r.cid, r.a_id, r.loc_id, c.first_name, c.last_name, c.email, c.phone
            ORDER BY totalPoints DESC
        ", [$location->loc_id]);
        } else {
            $results = DB::select("
            SELECT
                cid,
                a_id,
                loc_id,
                SUM(points) as totalPoints
            FROM LoyaltyRewards
            WHERE loc_id = ? " . $where . "
            GROUP BY cid, a_id, loc_id
            ORDER BY totalPoints DESC
        ", [$location->loc_id]);
        }

        // Process results
        foreach ($results as $i => $row) {
            if (empty($row->cid)) {
                continue;
            }

            $contact = $this->getRewardsContactDB($row->cid);
            $pbalance = $this->getPointsBalance($row->cid);

            if ($contact) {
                if (empty($contact->first_name)) {
                    $lookupContact = $this->lookupContactById($row->cid, $this->decryptAPI($location->apikey), $location->loc_id);
                    $contactData = json_decode($lookupContact, true);

                    if (!empty($contactData['contact']['firstName'])) {
                        $firstName = $contactData['contact']['firstName'] ?? "";
                        $lastName = $contactData['contact']['lastName'] ?? "";
                        $email = $contactData['contact']['emailLowerCase'] ?? "";
                        $phone = $contactData['contact']['phone'] ?? "";

                        $this->insertContactRewards($row->cid, $row->a_id, $row->loc_id, $firstName, $lastName, $email, $phone);
                    } else {
                        $firstName = "REMOVED_FROM_CRM_DELETE";
                        $lastName = "";
                        $phone = $contact->phone ?? '';
                        $email = $contact->email ?? '';
                    }
                } else {
                    $firstName = $contact->first_name;
                    $lastName = $contact->last_name;
                    $phone = $contact->phone ?? '';
                    $email = $contact->email ?? '';
                }
            } else {
                $lookupContact = $this->lookupContactById($row->cid, $this->decryptAPI($location->apikey), $location->loc_id);
                $contactData = json_decode($lookupContact, true);

                if (!empty($contactData['id']['rule'])) {
                    continue;
                }

                $firstName = $contactData['contact']['firstName'] ?? "";
                $lastName = $contactData['contact']['lastName'] ?? "";
                $email = $contactData['contact']['emailLowerCase'] ?? "";
                $phone = $contactData['contact']['phone'] ?? "";

                $this->insertContactRewards($row->cid, $row->a_id, $row->loc_id, $firstName, $lastName, $email, $phone);
            }

            $leaderBoard[] = [
                'pos' => $i + 1,
                'id' => $row->cid,
                'name' => $firstName . " " . $lastName,
                'phone' => $phone,
                'email' => $email,
                'points' => number_format($pbalance['lifePoints']),
                'available' => number_format($pbalance['curPoints']),
                'location' => $location->loc_id,
            ];
        }

        return $leaderBoard;
    }

    protected function paginateLeaderBoard($items, $perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $itemsForPage = array_slice($items, $offset, $perPage);

        return new LengthAwarePaginator(
            $itemsForPage,
            count($items),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    protected function getLicenseKey($agencyId)
    {
        // Implement your license key logic
        return "licensekey=" . DB::table('accounts')->where('id', $agencyId)->value('licensekey');
    }

    protected function getAgencyURL($agencyId)
    {
        // Implement your agency URL logic
        return DB::table('accounts')->where('id', $agencyId)->value('agency_url');
    }

    protected function decryptAPI($encryptedApiKey)
    {
        // Implement your decryption logic
        return decrypt($encryptedApiKey);
    }

    protected function getRewardsContactDB($cid)
    {
        return DB::table('LoyaltyContacts')->where('cid', $cid)->first();
    }

    protected function getPointsBalance($cid)
    {
        $lifePoints = DB::table('LoyaltyRewards')
            ->where('cid', $cid)
            ->where('event', '!=', 'redeem')
            ->sum('points');

        $curPoints = DB::table('LoyaltyRewards')
            ->where('cid', $cid)
            ->where('event', '!=', 'redeem')
            ->where('archive', '!=', 1)
            ->sum('points');

        return [
            'lifePoints' => $lifePoints,
            'curPoints' => $curPoints,
        ];
    }

    protected function lookupContactById($cid, $apiKey, $locationId)
    {
        // Implement your API call to lookup contact by ID
        // This would typically be a HTTP request to an external service
        return json_encode(['contact' => []]); // Placeholder
    }

    protected function insertContactRewards($cid, $aid, $locid, $firstName, $lastName, $email, $phone)
    {
        DB::table('LoyaltyContacts')->updateOrInsert(
            ['cid' => $cid],
            [
                'a_id' => $aid,
                'loc_id' => $locid,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
