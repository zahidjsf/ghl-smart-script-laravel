<?php

namespace Modules\RewardAndPromotions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\RewardAndPromotions\Entities\Location as EntitiesLocation;

class LoginController extends Controller
{
    public function login()
    {
        return view('rewardandpromotions::auth.login');
    }

    public function loginpost(Request $request)
    {
        $location = EntitiesLocation::where('email', $request->email)->where('proj_id', 2)->first();

        if ($location && Hash::check($request->password, $location->password)) {
            $loc = Auth::guard('location')->login($location);

            return redirect()->intended('/reward-promotions/referrals');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::guard('location')->logout();
        // Optionally invalidate and regenerate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return view('rewardandpromotions::auth.login');
    }
}
