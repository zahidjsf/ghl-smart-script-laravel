<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemProject;
use App\Models\ApiWebhook;
use App\Models\Account;
use App\Helper\CRM;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class FrontController extends Controller
{
    public function dashboard()
    {

        $authUser = LoginUser();
        // dd($authUser);
        $projects = SystemProject::get();
        $articles = getScriptsEntries("2", "date", "desc", "3");
        $connecturl = CRM::directConnect();

        return view('frontpanel.dashboard', get_defined_vars());
    }

    public function agencyUpdate(Request $req)
    {

        $accID = $req->id;
        $acc = Account::where('id', $accID)->first();
        $acc->agency_name = $req->agency_name ?? null;
        $acc->account_type = $req->accountType ?? null;
        $acc->agency_url = $req->agency_app_url ?? null;
        $acc->save();
        return back()->with('success', 'Information Updated Successfully !');
    }


    public function profileData()
    {
        $authUser = LoginUser();
        // dd($authUser);
        return view('frontpanel.profile.detail', get_defined_vars());
    }

    public function profileEdit($id)
    {
        $account = Account::where('id', $id)->first();
        return view('frontpanel.profile.edit', get_defined_vars());
    }

    public function update_profile(Request $request, $id)
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
        ]);

        $account = Account::where('id', $id)->first();
        $account->fName = $request->fname;
        $account->lName = $request->lname;
        $account->username = $request->username;

        $account->agency_name = $request->agency_name;
        $account->agency_url = $request->agency_url;

        if ($request->has('password') && $request->password !== null) {
            $account->password =  Hash::make($request->password);
        }

        $account->email = $request->email;
        // $account->licensekey = $request->license;
        $account->save();

        return redirect()->route('frontend.profile-detail')->with('success', 'Account updated successfully !');
    }


    public function apiHistory()
    {
        return view('frontpanel.apihistory.details');
    }

    public function getApiHistory(Request $request)
    {
        $model = ApiWebhook::query();
        return DataTables::of($model)->toJson();
    }
}
