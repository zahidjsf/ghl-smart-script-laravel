<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Services\AdminPanel\AccountService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    protected $accountService;
    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }
    public function index()
    {
        return view('adminpanel.account.manage');
    }

    public function getAccounts(Request $request)
    {
        if ($request->ajax()) {
            return $this->accountService->getAccountsForDatatable();
        }
    }

    public function create()
    {
        $systemProjects = $this->accountService->getRepository()->getSystemProjects();
        $roles = ['Member', 'Admin', 'SubAdmin'];
        $license = $this->accountService->getRepository()->generateLicense();

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

        $this->accountService->createAccount($request->all());

        return redirect()->route('admin.accounts')->with('success', 'Account created successfully.');
    }

    public function edit($id)
    {
        $data = $this->accountService->getAccountDataForEdit($id);
        $roles = ['Member', 'Admin', 'SubAdmin'];
        return view('adminpanel.account.edit', array_merge($data, ['roles' => $roles]));
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
            'license' => 'required|string',
            'apikey' => 'required|string',
            'activation_code' => 'nullable|string',
            'rememberme' => 'nullable|string'
        ]);

        $this->accountService->updateAccount($id, $request->all());

        return redirect()->route('admin.accounts')->with('success', 'Account updated successfully !');
    }

    public function destroy($id)
    {
        $this->accountService->deleteAccount($id);
        return redirect()->route('admin.accounts')->with('success', 'Account deleted successfully !');
    }

    public function licenseOperation($projId, $accID, $type)
    {
        $view = $this->accountService->handleLicenseOperation($projId, $accID, $type);
        return response()->json(['view' => $view]);
    }

    public function licenseUpdate(Request $request)
    {
        $this->accountService->updateLicense($request);
        return response()->json(['message' => 'License updated']);
    }
}
