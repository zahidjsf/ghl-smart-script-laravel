<?php

namespace App\Repositories\AdminPanel;

use App\Models\Account;
use App\Models\Accountdetail;
use App\Models\SystemAccess;
use App\Models\SystemProject;
use App\Models\ProjectLicense;
use App\Models\SSMembership;
use App\Repositories\Interfaces\AdminPanel\AccountRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AccountRepository implements AccountRepositoryInterface
{
    public function all()
    {
        return Account::all();
    }

    public function getAccountsForDatatable($currentAccount)
    {
        $query = Account::with('detail');

        if ($currentAccount->role == Account::SUBADMIN) {
            $query->whereHas('detail', function ($q) use ($currentAccount) {
                $q->where('parent_id', $currentAccount->id);
            });
        }

        return $query;
    }

    public function find($id)
    {
        return Account::findOrFail($id);
    }

    public function create(array $data)
    {
        $account = new Account();
        $account->fName = $data['fname'];
        $account->lName = $data['lname'];
        $account->username = $data['username'];
        $account->agency_name = $data['agency_name'] ?? null;
        $account->account_type = $data['account_type'] ?? null;
        $account->agency_url = $data['agency_url'] ?? null;
        $account->password = Hash::make($data['password']);
        $account->email = $data['email'];
        $account->licensekey = $data['license'];
        $account->apikey = $this->encryptAPI($data['apikey']);
        $account->activation_code = $data['activation_code'] ?? Str::random(32);
        $account->rememberme = $data['rememberme'] ?? Str::random(32);
        $account->role = $data['role'];
        $account->suspend = $data['suspend'] ?? 0;
        $account->save();

        // Add account details
        $accountDetail = new AccountDetail();
        $accountDetail->a_id = $account->id;
        $accountDetail->parent_id = Auth::id();
        $accountDetail->save();

        // Handle system access
        if (!empty($data['projectAccess'])) {
            $systemAccess = new SystemAccess();
            $systemAccess->a_id = $account->id;
            $systemAccess->projids = implode(',', $data['projectAccess']);
            $systemAccess->save();

            $this->updateProjectLicenses($account->id, $data['projectAccess']);
        }

        // Handle SS membership
        $ssMembership = new SSMembership();
        $ssMembership->a_id = $account->id;
        $ssMembership->type = $data['SSBM'] ?? 0;
        $ssMembership->save();

        return $account;
    }

    public function update($id, array $data)
    {
        $account = $this->find($id);
        $account->fName = $data['fname'];
        $account->lName = $data['lname'];
        $account->username = $data['username'];
        $account->agency_name = $data['agency_name'] ?? null;
        $account->account_type = $data['account_type'] ?? null;
        $account->agency_url = $data['agency_url'] ?? null;

        if (!empty($data['password'])) {
            $account->password = Hash::make($data['password']);
        }

        $account->email = $data['email'];
        $account->licensekey = $data['license'];
        $account->apikey = $this->encryptAPI($data['apikey']);
        $account->activation_code = $data['activation_code'] ?? Str::random(32);
        $account->rememberme = $data['rememberme'] ?? Str::random(32);
        $account->role = $data['role'];
        $account->suspend = $data['suspend'] ?? 0;
        $account->save();

        // Update account details
        $accountDetail = AccountDetail::where('a_id', $id)->firstOrNew();
        $accountDetail->a_id = $account->id;
        $accountDetail->parent_id = Auth::id();
        $accountDetail->save();

        // Handle system access
        if (!empty($data['projectAccess'])) {
            $systemAccess = SystemAccess::where('a_id', $id)->firstOrNew();
            $systemAccess->a_id = $account->id;
            $systemAccess->projids = implode(',', $data['projectAccess']);
            $systemAccess->save();

            $this->updateProjectLicenses($account->id, $data['projectAccess']);
        }

        // Handle SS membership
        $this->updateSSMembership($account->id, $data['SSBM'] ?? 0);

        return $account;
    }

    public function delete($id)
    {
        $account = $this->find($id);

        SystemAccess::where('a_id', $id)->delete();
        SSMembership::where('a_id', $id)->delete();
        Accountdetail::where('a_id', $id)->delete();
        ProjectLicense::where('a_id', $id)->delete();

        return $account->delete();
    }

    public function generateLicense()
    {
        return strtoupper(bin2hex(random_bytes(10)));
    }

    public function encryptAPI($apikey)
    {
        $ciphering = "AES-128-CTR";
        $encryption_iv = '1231231231234456';
        $options = 0;
        $encryption_key = "GHLSmartReviews";

        return openssl_encrypt($apikey, $ciphering, $encryption_key, $options, $encryption_iv);
    }

    public function decryptAPI($apikey)
    {
        $ciphering = "AES-128-CTR";
        $decryption_iv = '1231231231234456';
        $options = 0;
        $decryption_key = "GHLSmartReviews";
        $tag = "";

        return openssl_decrypt(
            (string)$apikey,
            $ciphering,
            $decryption_key,
            $options,
            $decryption_iv,
            $tag
        );
    }

    public function updateProjectLicenses($accountId, $projectAccess)
    {
        $alreadyProjLicenses = ProjectLicense::where('a_id', $accountId)->pluck('proj_id')->toArray();
        $newProjLicenses = $projectAccess;
        $toBeDeleted = array_diff($alreadyProjLicenses, $newProjLicenses);

        foreach ($toBeDeleted as $proj) {
            ProjectLicense::where(['a_id' => $accountId, 'proj_id' => $proj])->delete();
        }

        foreach ($newProjLicenses as $proj) {
            $prolicense = new  ProjectLicense();
            $prolicense->a_id = $accountId;
            $prolicense->proj_id = $proj;
        }
    }

    public function getSystemProjects()
    {
        return SystemProject::all();
    }

    public function getAccountWithRelations($id)
    {
        return Account::with(['systemAccess', 'isBundleMember', 'projects'])->findOrFail($id);
    }

    public function updateSSMembership($accountId, $status)
    {
        $ssMembership = SSMembership::where('a_id', $accountId)->firstOrNew();
        $ssMembership->a_id = $accountId;
        $ssMembership->type = $status ? 1 : 0;
        $ssMembership->save();
    }

    public function getProjectLicense($accountId, $projectId)
    {
        return ProjectLicense::where(['a_id' => $accountId, 'proj_id' => $projectId])->first();
    }

    public function updateLicenseCount($accountId, $projectId, $count)
    {
        $projLicense = ProjectLicense::where(['a_id' => $accountId, 'proj_id' => $projectId])->firstOrNew();
        $projLicense->a_id = $accountId;
        $projLicense->proj_id = $projectId;
        $projLicense->numLicenses = $count;
        $projLicense->save();

        return $projLicense;
    }
}
