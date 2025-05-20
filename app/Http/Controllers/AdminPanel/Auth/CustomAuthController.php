<?php

namespace App\Http\Controllers\AdminPanel\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomAuthController extends Controller
{

    public function loginForm()
    {

        if (auth()->check()) {
            return redirect()->route('admin.accounts');
        }
        return view('adminpanel.auth.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $user = Account::where('email', $email)->first();

        if ($user) {

            if (Hash::check($password, $user->password)) {
                Auth::login($user);

                if ($user->role === 'Admin' || $user->role === 'SubAdmin') {
                    return redirect()->route('admin.accounts');
                } elseif ($user->role === 'Member') {
                    return redirect()->route('frontend.dashboard');
                } else {
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Unauthorized role.',
                    ]);
                }
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput();
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
