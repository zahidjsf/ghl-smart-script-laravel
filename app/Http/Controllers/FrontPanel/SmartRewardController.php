<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemProject;
use App\Models\ProjectLicense;
use App\Models\ReviewMlLocation;
use App\Models\Location;
use App\Models\CollectionAssign;
use App\Models\ProjectOption;
use App\Models\LocationSetting;
use App\Models\AccountSetting;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Helper\CRM;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class SmartRewardController extends Controller
{

    public function index()
    {

        $authUser = LoginUser();
        $user_id = $authUser->id;

        $projdetail = SystemProject::where('id', 2)->first();

        $projectlicense = ProjectLicense::where(['a_id' =>  $user_id, 'proj_id' => 2])->first();

        if ($projectlicense) {
            $numLicenses = ($projectlicense->membership == 1) ? "Unlimited" : $projectlicense->licenses;
            $perLocMsg = ($projectlicense->perloc != 0) ? $projectlicense->perloc : "unlimited";
            $perLoc = ($projectlicense->perloc != 0) ? $projectlicense->perloc : 100000;
        } else {
            $numLicenses = 0;
            $perLocMsg = "unlimited";
            $perLoc = 100000;
        }

        $locations = Location::where(['a_id' =>  $user_id, 'proj_id' => 2])->get();
        $numLocations = $locations->count();

        $setting = AccountSetting::where('a_id', $user_id)->first();

        if ($projectlicense && $numLocations >= $projectlicense->licenses ?? 0) {
            $limit = "full";
            $creditsused = __('messages.cred_used');
            $more = __('messages.more_cred_used');
            $licenseMsg = "<strong>" . $creditsused . "</strong> " . $numLocations . " of " . $numLicenses . "<br/><span class='red'>" . $more . "</span>";
        } else {
            $limit = "active";
            $text = __('messages.lic_used');
            $licenseMsg = "<strong>" . $text . "</strong> " . $numLocations . " of " . $numLicenses;
        }

        return view('frontpanel.smartreward.index', get_defined_vars());
    }


    public function getLocations()
    {
        $authUser = LoginUser();
        $user_id = $authUser->id;

        $locations = Location::where(['a_id' => $user_id, 'proj_id' => 2]);

        return DataTables::of($locations)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<a href="' . route('frontend.smart_reward.cv_smartreward', ['id' => $row->id]) . '" class="btn btn-sm btn-primary">' . __('messages.custom_values') . '</a> ';

                $html .= '<a style="margin-right:4px" href="#" data-url="' . route('frontend.smart_reward.action_manage', ['id' => $row->id, 'action' => 'edit_details']) . '" class="btn btn-sm btn-secondary load-license-modal">' . __('messages.edit_details') . '</a>';

                $html .= '<a style="margin-right:4px" href="#" data-url="' . route('frontend.smart_reward.action_manage', ['id' => $row->id, 'action' => 'settings']) . '" class="btn btn-sm btn-success load-setting-modal">' . __('messages.settings') . '</a>';

                $html .= '<a href="' . route('frontend.smart_reward.action_manage', ['id' => $row->id, 'action' => 'manage_rewards']) . '" class="btn btn-sm btn-warning">' . __('messages.manage_rewards') . '</a> ';

                $html .= '<a style="margin-right:4px" href="#" data-url="' . route('frontend.smart_reward.action_manage', ['id' => $row->id, 'action' => 'remove']) . '" class="btn btn-sm btn-danger remove-location">' . __('messages.remove') . '</a>';

                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function actionManage($id, $actionType)
    {

        $location = Location::where('id', $id)->first();

        if ($actionType == 'edit_details') {

            $view = view('frontpanel.smartreward.editdetails', get_defined_vars())->render();
            return response()->json(['view' => $view]);
        } elseif ($actionType == 'settings') {
            $projOptions = ProjectOption::where(['proj' => 2])->get();

            $settingRec = LocationSetting::where(['loc_id' => $location->id, 'proj' => 2])->first();
            $settings = json_decode($settingRec->settings ?? '') ?? null;
            $view = view('frontpanel.smartreward.setting', get_defined_vars())->render();
            return response()->json(['view' => $view]);
        } elseif ($actionType == 'manage_rewards') {
            dd('fdsfsdfsd');
        } elseif ($actionType == 'remove') {
            $location->delete();
            CollectionAssign::where(['loc_id' => $id, 'proj_id' => 2])->delete();
            return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Action Not Found']);
        }
    }

    private function isMatchingCustomValue($cv, $value)
    {
        return $value['name'] == $cv->name ||
            str_replace(' ', '', $value['fieldKey']) == str_replace(' ', '', $cv->mergeKey);
    }


    private function getCustomValues($locId)
    {
        if ($locId) {
            $url = 'locations/' . $locId . '/customValues';
            $response = CRM::crmV2(LoginUser(true), $url,  'get', '', [], false, $locId);
            return $response;
        }
    }
    public function settingUpdate(Request $request)
    {

        $settingRec = LocationSetting::where(['id' => $request->settingId])->first();

        if (!$settingRec) {
            $settingRec = new LocationSetting();
            $settingRec->loc_id = $request->locid;
            $settingRec->proj = 2;
        }
        $dataExisted = false;
        $newDataSave = new \stdClass();



        if ($request->has('showSettings') && $request->showSettings !== null) {
            $dataExisted = true;
            $newDataSave->showSettings = $request->showSettings;
        }


        if ($request->has('showTiers') && $request->showTiers !== null) {
            $dataExisted = true;
            $newDataSave->showTiers = $request->showTiers;
        }

        if ($request->has('showPromoPoints') && $request->showPromoPoints !== null) {
            $dataExisted = true;
            $newDataSave->showPromoPoints = $request->showPromoPoints;
        }


        if ($request->has('showPromotions') && $request->showPromotions !== null) {
            $dataExisted = true;
            $newDataSave->showPromotions = $request->showPromotions;
        }

        if ($request->has('showReporting') && $request->showReporting !== null) {
            $dataExisted = true;
            $newDataSave->showReporting = $request->showReporting;
        }


        if ($request->has('showPoints') && $request->showPoints !== null) {
            $dataExisted = true;
            $newDataSave->showPoints = $request->showPoints;
        }

        if ($request->has('showRewardsEditor') && $request->showRewardsEditor !== null) {
            $dataExisted = true;
            $newDataSave->showRewards = $request->showRewardsEditor;
        }

        if ($request->has('renamePoints') && $request->renamePoints !== null) {
            $dataExisted = true;
            $newDataSave->renamePoints = $request->renamePoints;
        }

        if ($request->has('pointsValue') && $request->pointsValue !== null) {
            $dataExisted = true;
            $newDataSave->pointsValue = $request->pointsValue;
        }

        if ($request->has('language') && $request->language !== null) {
            $dataExisted = true;
            $newDataSave->language = $request->language;
        }

        if ($request->has('rewardsTemplate') && $request->rewardsTemplate !== null) {
            $dataExisted = true;
            $newDataSave->rewardsTemplate = $request->rewardsTemplate;
        }

        if ($request->has('vbg') && $request->vbg !== null) {
            $dataExisted = true;
            $newDataSave->vbg = $request->vbg;
        }

        if ($dataExisted == false) {
            $settingRec->delete();
            return redirect()->back()->with('error', 'Nothing Selected');
        }

        $newDataSave = json_encode($newDataSave);
        $settingRec->settings = $newDataSave;
        $settingRec->save();
        return redirect()->back()->with('success', 'Setting Saved Successfully');
    }


    public function locationUpdate(Request $request)
    {
        $location = Location::where('id', $request->loc_id)->first();
        $location->name = $request->name;
        if ($request->filled('password')) {
            $location->password = Hash::make($request->password);
        }
        $location->save();
        return redirect()->back()->with('success', 'Location Updated Successfully');
    }


    public function saveCSS(Request $request)
    {
        $request->validate([
            'leaderboard_css' => 'required'
        ]);

        $authUser = LoginUser();
        $user_id = $authUser->id;
        $setting = AccountSetting::where('a_id', $user_id)->first();

        if (!$setting) {
            $setting = new AccountSetting();
            $setting->a_id = $user_id;
        }
        $setting->rewards_css = $request->leaderboard_css;
        $setting->save();

        return back()->with('success', 'CSS saved successfully !');
    }


    public function addLocations()
    {
        $account = LoginUser();
        $account_id = $account->id;
        $currentLocations = Location::where('a_id', $account_id)->get();
        $agencyLocations = [];
        $allLocations = [];
        $limit = 100;
        $skip = 0;

        do {
            $urlmain = "locations/search?deleted=false&limit={$limit}&skip={$skip}";
            $locations = CRM::agencyV2($account_id, $urlmain);
            // dd($locations);
            $locations = $locations->locations ?? [];
            $allLocations = array_merge($allLocations, $locations);
            $hasMore = count($locations) === $limit;
            $skip += $limit;
        } while ($hasMore);

        foreach ($allLocations as $loc) {
            $agencyLocations[$loc->id] = $loc;
        }

        $projectId =  request('proj_id', 2);
        // $projectId =  request('project_id', 1);
        // $showLocationSelect = $account->is_agency;
        $showLocationSelect = true;

        $view = view('frontpanel.smartreward.addlocation', get_defined_vars())->render();
        return response()->json(['view' => $view]);
    }


    public function locationAdd(Request $request)
    {

        $account = LoginUser();

        $validated = $request->validate([
            'project_id' => 'required|exists:systemprojects,id',
            'sel_loc_id' => 'nullable|string',
            'loc_id' => 'nullable|string',
            'loc_name' => 'nullable|string',
            'ml' => 'nullable|in:yes,no',
            'snapshot' => 'nullable|string',
            'add_promo_loc' => 'nullable|in:yes,no',
            'add_loyalty_loc' => 'nullable|in:yes,no',
            'selectCurLoc' => 'nullable|string',
            'password' => 'nullable|string'
        ]);

        // Determine which location ID to use
        if (!empty($validated['sel_loc_id']) && $validated['sel_loc_id'] !== 'Select A Location') {
            $locationId = $validated['sel_loc_id'];
        } elseif (!empty($validated['loc_id'])) {
            $locationId = $validated['loc_id'];
        } elseif (!empty($validated['selectCurLoc']) && $validated['selectCurLoc'] !== 'Select A Location') {
            $locationId = $validated['selectCurLoc'];
        } else {
            return back()->with('error', 'You must select a location or add one to submit');
        }

        // Check if location already exists for this project
        if (Location::where('a_id', $account->id)
            ->where('proj_id', $validated['project_id'])
            ->where('loc_id', $locationId)
            ->exists()
        ) {
            return back()->with('error', 'This location has already been added');
        }
        // Get location details
        $locationDetails = $this->getLocationDetails($account, $locationId, $validated);
        // dd($locationDetails);
        // Create the location
        $location = $this->createLocationRecord($account, $validated, $locationId, $locationDetails);

        // Handle project-specific setup
        $this->handleProjectSetup($account, $validated, $location);

        return redirect()->back()
            ->with('success', 'Location added successfully');
    }


    protected function getLocationDetails($account, $locationId, $validated)
    {
        // Try to get from API if agency account
        // if ($account->is_agency) {
        try {

            $urlmain = "locations/{$locationId}";
            $response = CRM::agencyV2($account->id, $urlmain);

            // $response = json_encode($response);
            // dd($response);
            if ($response && property_exists($response, 'location')) {
                $data = $response->location;
                return [
                    'name' => $data->name ?? $validated['loc_name'] ?? 'NA-Added ' . now()->format('Y-m-d'),
                    'website' => $data->website ?? null,
                    'firstname' => $data->firstName ?? null,
                    'lastname' => $data->lastName ?? null,
                    'email' => $data->email ?? null,
                    'phone' => $data->phone ?? null,
                    'address' => $data->address ?? null,
                    'city' => $data->city ?? null,
                    'state' => $data->state ?? null,
                    'country' => $data->country ?? null,
                    'postalcode' => $data->postalCode ?? null,
                    'timezone' => $data->timezone ?? null,
                    // 'apikey' => $data->apiKey ?? Crypt::decryptString($account->apikey)
                ];
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch location details: " . $e->getMessage());
        }
        // }

        // Fallback to form data
        return [
            'name' => $validated['loc_name'] ?? 'NA-Added ' . now()->format('Y-m-d'),
            // 'apikey' => Crypt::decryptString($account->apikey)
        ];
    }


    protected function createLocationRecord($account, $validated, $locationId, $details)
    {
        $location = new Location();
        $location->a_id = $account->id;
        $location->proj_id = $validated['project_id'];
        $location->loc_id = $locationId;
        $location->name = $details['name'] ?? '';
        $location->website = $details['website'] ?? '';
        $location->firstname = $details['firstname'] ?? '';
        $location->lastname = $details['lastname'] ?? '';
        $location->email = $details['email'] ?? '';
        $location->phone = $details['phone'] ?? '';
        $location->address = $details['address'] ?? '';
        $location->city = $details['city'] ?? '';
        $location->state = $details['state'] ?? '';
        $location->country = $details['country'] ?? '';
        $location->postalcode = $details['postalcode'] ?? '';
        $location->timezone = $details['timezone'] ?? '';
        if ($details['password']) {
            $location->password = Hash::make($details['password']);
        }
        // $location->apikey = Crypt::encryptString($details['apikey']);
        $location->isML = $validated['ml'] ?? 'no';
        $location->snapshot = $validated['snapshot'] ?? null;

        $location->save();
        // Create multi-location if needed
        if (($validated['ml'] ?? 'no') === 'yes') {
            $rev = new ReviewMlLocation();
            $rev->loc_id = $location->id;
            $rev->a_id = $account->id;
            $rev->name = $location->name . '- Loc 1';
            $rev->sub_loc_id = 1;
            $rev->save();
        }
        return $location;
    }

    protected function handleProjectSetup($account, $validated, $location)
    {
        // Rewards project (ID 2)
        if ($validated['project_id'] == 2) {
            $this->setupRewardsProject($account, $validated, $location);
        }

        // Video Testimonials project (ID 16)
        // if ($validated['project_id'] == 16) {
        //     $this->setupVideoTestimonials($account, $location);
        // }
    }

    protected function setupRewardsProject($account, $validated, $location)
    {
        // Create default settings
        $locationSetting = new LocationSetting();
        $locationSetting->loc_id = $location->id;
        $locationSetting->settings = json_encode([
            'showSettings'   => "yes",
            'showRewards'    => "yes",
            'showPoints'     => "yes",
            'showPromotions' => "yes",
            'use_loyalty'    => "yes",
            'showTiers'      => "yes",
            'pointsName'     => "Points",
            'showReporting'  => "yes",
            'pointsValue'    => 1,
            'language'       => "english"
        ]);
        $locationSetting->save();

        // Add to promotions if selected
        // if (($validated['add_promo_loc'] ?? 'no') === 'yes') {
        //     $this->duplicateLocationForProject($account, $location, 15);
        // }

        // Add to loyalty if selected
        // if (($validated['add_loyalty_loc'] ?? 'no') === 'yes') {
        //     $this->duplicateLocationForProject($account, $location, 17);
        // }
    }


    protected function duplicateLocationForProject($account, $originalLocation, $projectId)
    {
        // Check if already exists
        if (!Location::where('a_id', $account->id)
            ->where('proj_id', $projectId)
            ->where('loc_id', $originalLocation->loc_id)
            ->exists()) {

            $location = new Location();
            $location->a_id        = $account->id;
            $location->proj_id     = $projectId;
            $location->loc_id      = $originalLocation->loc_id;
            $location->name        = $originalLocation->name;
            $location->website     = $originalLocation->website;
            $location->firstname   = $originalLocation->firstname;
            $location->lastname    = $originalLocation->lastname;
            $location->email       = $originalLocation->email;
            $location->phone       = $originalLocation->phone;
            $location->address     = $originalLocation->address;
            $location->city        = $originalLocation->city;
            $location->state       = $originalLocation->state;
            $location->country     = $originalLocation->country;
            $location->postalcode  = $originalLocation->postalcode;
            $location->timezone    = $originalLocation->timezone;
            $location->apikey      = $originalLocation->apikey;
            $location->isML       = $originalLocation->is_ml;
            $location->snapshot    = $originalLocation->snapshot;
            $location->save();
        }
    }
}
