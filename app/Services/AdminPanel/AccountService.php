<?php

namespace App\Services\AdminPanel;

use App\Repositories\Interfaces\AdminPanel\AccountRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AccountService
{
    protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getRepository()
{
    return $this->accountRepository;
}

    public function getAccountsForDatatable()
    {
        $currentAccount = LoginUser();
        $query = $this->accountRepository->getAccountsForDatatable($currentAccount);

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

    public function createAccount($requestData)
    {
        $requestData['license'] = $requestData['license'] ?? $this->accountRepository->generateLicense();
        return $this->accountRepository->create($requestData);
    }

    public function updateAccount($id, $requestData)
    {
        return $this->accountRepository->update($id, $requestData);
    }

    public function deleteAccount($id)
    {
        return $this->accountRepository->delete($id);
    }

    public function getAccountDataForEdit($id)
    {
        $account = $this->accountRepository->getAccountWithRelations($id);
        $systemAccess = [];

        if ($account->systemAccess) {
            $systemAccess = explode(',', $account->systemAccess->projids);
        }

        return [
            'account' => $account,
            'systemProjects' => $this->accountRepository->getSystemProjects(),
            'systemAccess' => $systemAccess,
            'projLicense' => $account->projects,
            'isBundleMember' => $account->isBundleMember ? 'checked' : '',
            'isSuspended' => $account->suspend ? 'checked' : '',
            'apiKey' => $this->accountRepository->decryptAPI($account->apikey)
        ];
    }

    public function handleLicenseOperation($projId, $accID)
    {
        $projLicense = $this->accountRepository->getProjectLicense($accID, $projId);
        $systemProject = $this->accountRepository->getSystemProjects()->find($projId);
        $numLicenses = $projLicense ? $projLicense->numLicenses : 0;

        return view('adminpanel.account.project_licenses', [
            'projLicense' => $projLicense,
            'systemProject' => $systemProject,
            'numLicenses' => $numLicenses,
            'accID' => $accID
        ])->render();
    }

    public function updateLicense($request)
    {
        return $this->accountRepository->updateLicenseCount(
            $request->acc_id,
            $request->proj_id,
            $request->licenses
        );
    }
}
