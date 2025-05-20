<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Accountdetail;
use App\Models\SystemAccess;
use App\Models\SystemProject;
use App\Models\ProjectLicense;
use App\Models\SSMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        return view('adminpanel.account.manage');
    }

    public function getAccounts(Request $request)
    {
        if ($request->ajax()) {
            $currentAccount = Auth::user();

            $query = Account::with('detail');

            if ($currentAccount->role == 'SubAdmin') {
                $query->whereHas('detail', function ($q) use ($currentAccount) {
                    $q->where('parent_id', $currentAccount->id);
                });
            }
            return DataTables::of($query)
                ->addColumn('name', function ($account) {
                    return $account->fName . ' ' . $account->lName;
                })
                ->addColumn('license_short', function ($account) {
                    return $account->licensekey ? substr($account->licensekey, 0, 20) . '...' : '';
                })
                ->addColumn('parent_id', function ($account) {
                    return $account->detail->parent_id ?? '';
                })
                ->addColumn('actions', function ($account) {
                    $html = ' <a href="' . route('admin.accountedit', $account->id) . '" class="btn btn-xs btn-primary">Edit</a> ';
                    $html .= ' <a href="../home.php?licensekey=' . $account->licensekey . '" class="btn btn-xs btn-primary" target="_incognito">Manage D4Y</a>';
                    $html .= ' <a href="' . $account->agency_url . '" class="btn btn-xs btn-primary" target="_blank">GHL Login</a>';
                    $html .= ' <a href="' . route('admin.accountdelete', $account->id) . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this account?\')">Delete</a> ';
                    return $html;
                })

                ->editColumn('suspend', function ($account) {
                    return $account->suspend ? '<span class="badge bg-danger">Suspended</span>' : '<span class="badge bg-success">Active</span>';
                })
                ->rawColumns(['actions', 'suspend'])
                ->make(true);
        }
    }

    public function create()
    {
        $systemProjects = SystemProject::all();
        $roles = ['Member', 'Admin', 'SubAdmin'];
        $license = $this->generateLicense();

        return view('adminpanel.account.add', get_defined_vars());
    }

    public function store(Request $request)
    {

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:accounts',
            'password' => 'required|string|min:8',
            'email' => 'required|email|unique:accounts',
            'role' => 'required|in:Member,Admin,SubAdmin',
            'projectAccess' => 'nullable|array',
            'projectAccess.*' => 'exists:systemProjects,id',
            'license' => 'required|string',
            'apikey' => 'required|string',
            'activation_code' => 'nullable|string',
            'rememberme' => 'nullable|string'
        ]);

        // Create account
        $account = new Account();
        $account->fName = $request->fname;
        $account->lName = $request->lname;
        $account->username = $request->username;

        $account->agency_name = $request->agency_name;
        $account->account_type = $request->account_type;
        $account->agency_url = $request->agency_url;

        $account->password = Hash::make($request->password);
        $account->email = $request->email;
        $account->licensekey = $request->license;
        $account->apikey = $this->encryptAPI($request->apikey);
        $account->activation_code = $request->activation_code ?? Str::random(32);
        $account->rememberme = $request->rememberme ?? Str::random(32);
        $account->role = $request->role;
        $account->suspend = $request->has('suspend') ? 1 : 0;
        $account->save();

        // Add account details
        $accountDetail = new AccountDetail();
        $accountDetail->a_id = $account->id;
        $accountDetail->parent_id = Auth::id();
        $accountDetail->save();

        // Handle system access
        if ($request->projectAccess) {
            $systemAccess = new SystemAccess();
            $systemAccess->a_id = $account->id;
            $systemAccess->projids = implode(',', $request->projectAccess);
            $systemAccess->save();

            // Add project licenses

            foreach ($request->projectAccess as $key => $value) {
                $projectLicense = new ProjectLicense();
                $projectLicense->a_id = $account->id;
                $projectLicense->proj_id = $value;
                $projectLicense->save();
            }
        }

        // Handle SS membership
        $ssMembership = new SSMembership();
        $ssMembership->a_id = $account->id;
        $ssMembership->type = $request->has('SSBM') ? 1 : 0;
        $ssMembership->save();

        return redirect()->route('admin.accounts')->with('success', 'Account created successfully.');
    }

    protected function encryptAPI($apikey)
    {
        $ciphering = "AES-128-CTR";
        $encryption_iv = '1231231231234456';
        $options = 0;
        $encryption_key = "GHLSmartReviews";

        return openssl_encrypt($apikey, $ciphering, $encryption_key, $options, $encryption_iv);
    }


    function decryptAPI($apikey)
    {

        // Store the cipher method
        $ciphering = "AES-128-CTR";
        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1231231231234456';
        $options = 0;
        // Store the decryption key
        $decryption_key = "GHLSmartReviews";
        $tag = "";

        // Use openssl_decrypt() function to decrypt the data
        $decryption = openssl_decrypt(
            (string)$apikey,
            $ciphering,
            $decryption_key,
            $options,
            $decryption_iv,
            $tag
        );

        return $decryption;
    }

    protected function generateLicense()
    {
        // Implement your license generation logic here
        return strtoupper(bin2hex(random_bytes(10)));
    }

    public function edit($id)
    {
        $account = Account::with(['systemAccess', 'isBundleMember', 'projects'])->findOrFail($id);
        $roles = ['Member', 'Admin', 'SubAdmin'];
        $systemProjects = SystemProject::all();

        $systemAccess = [];
        if ($account->systemAccess) {
            $systemAccess = explode(',', $account->systemAccess->projids);
        }

        $projLicense = ProjectLicense::where('a_id', $id)->get();

        $isBundleMember = $account->isBundleMember ? 'checked' : '';
        $isSuspended = $account->suspend ? 'checked' : '';
        $apiKey = $this->decryptAPI($account->apikey);

        return view('adminpanel.account.edit', get_defined_vars());
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('accounts')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('accounts')->ignore($id),
            ],
            'role' => 'required|in:Member,Admin,SubAdmin',
            'projectAccess' => 'nullable|array',
            // 'projectAccess.*' => 'exists:systemProjects,id',
            'license' => 'required|string',
            'apikey' => 'required|string',
            'activation_code' => 'nullable|string',
            'rememberme' => 'nullable|string'
        ]);

        $account = Account::where('id', $id)->first();
        $account->fName = $request->fname;
        $account->lName = $request->lname;
        $account->username = $request->username;

        $account->agency_name = $request->agency_name;
        $account->account_type = $request->account_type;
        $account->agency_url = $request->agency_url;

        if ($request->has('password') && $request->password !== null) {
            $account->password =  Hash::make($request->password);
        }

        $account->email = $request->email;
        $account->licensekey = $request->license;
        $account->apikey = $this->encryptAPI($request->apikey); // need to change
        $account->activation_code = $request->activation_code ?? Str::random(32);
        $account->rememberme = $request->rememberme ?? Str::random(32);
        $account->role = $request->role;
        $account->suspend = $request->has('suspend') ? 1 : 0;
        $account->save();

        // Add account details
        // $accountDetail = AccountDetail::where('a_id', $id)->first();
        // $accountDetail->a_id = $account->id;
        // $accountDetail->parent_id = Auth::id();
        // $accountDetail->save();

        // Handle system access
        if ($request->projectAccess) {

            $systemAccess = SystemAccess::where('a_id', $id)->first();
            $systemAccess->a_id = $account->id;
            $systemAccess->projids = implode(',', $request->projectAccess);
            $systemAccess->save();
            // Add project licenses
            $alreadyProjLicenses = ProjectLicense::where('a_id', $id)->pluck('proj_id')->toArray();
            $newProjLicenses = $request->projectAccess;
            $toBeDeleted = array_diff($alreadyProjLicenses, $newProjLicenses);
            foreach ($toBeDeleted as $key => $proj) {
                $proj =  ProjectLicense::where(['a_id' => $id, 'proj_id' => $proj])->first();
                $proj->delete();
            }
            foreach ($newProjLicenses as $key => $proj) {
                $projectLicense = new ProjectLicense();
                $projectLicense->a_id = $id;
                $projectLicense->proj_id = $proj;
                $projectLicense->save();
            }
        }

        // Handle SS membership
        $ssMembership = SSMembership::where('a_id', $id)->first();
        if (!$ssMembership) {
            $ssMembership = new SSMembership();
        }
        $ssMembership->a_id = $id;
        $ssMembership->type = $request->has('SSBM') ? 1 : 0;
        $ssMembership->save();

        return redirect()->route('admin.accounts')->with('success', 'Account updated successfully !');
    }

    public function delete($id)
    {
        $account = Account::findOrFail($id);

        $systemAccess = SystemAccess::where('a_id', $id)->first();
        $systemAccess->delete();

        $ssMembership = SSMembership::where('a_id', $id)->first();
        $ssMembership->delete();
        $Accountdetail = Accountdetail::where('a_id', $id)->first();
        $Accountdetail->delete();

        $proj =  ProjectLicense::where(['a_id' => $id])->get();
        foreach ($proj as $key => $pro) {
            $pro->delete();
        }
        $account->delete();
        return redirect()->route('admin.accounts')->with('success', 'Account deleted successfully !');
    }

    public function licenseOperation($projId, $accID, $type)
    {
        $projLicense = ProjectLicense::where(['a_id' => $accID, 'proj_id' => $projId])->first();
        $systemProject = SystemProject::findOrFail($projId);
        $numLicenses = $projLicense ? $projLicense->numLicenses : 0;

        $view =  view('adminpanel.account.project_licenses', get_defined_vars())->render();

        return response()->json(['view' => $view]);
    }

    public function licenseUpdate(Request $request)
    {
        $projLicense = ProjectLicense::where(['a_id' => $request->acc_id, 'proj_id' => $request->proj_id])->first();
        if ($projLicense) {
            $projLicense->numLicenses = $request->licenses;
            $projLicense->save();
        } else {
            $projLicense = new ProjectLicense();
            $projLicense->a_id = $request->acc_id;
            $projLicense->proj_id = $request->proj_id;
            $projLicense->numLicenses = $request->licenses;
            $projLicense->save();
        }

        return response()->json(['message' => 'License updated']);
    }
}
