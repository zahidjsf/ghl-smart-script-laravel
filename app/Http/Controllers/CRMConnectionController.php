<?php

namespace App\Http\Controllers;

use App\Helper\CRM;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CRMConnectionController extends Controller
{
    public function crmCallback(Request $request)
    {
        \Artisan::call('optimize:clear');
        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');

        $code = $request->code ?? null;
        if ($code) {
            $user_id = LoginUser(true);
            $code = CRM::crm_token($code, '');
            $code = json_decode($code);
            $user_type = $code->userType ?? null;
            $main = route('frontend.dashboard');
            if ($user_type) {
                $token = $user->token ?? null;
                list($connected, $con) = CRM::go_and_get_token($code, '', $user_id, $token);
                if ($connected) {
                    // $this->authService->getCompany(auth()->user());
                    return redirect($main)->with('success', 'Connected Successfully');
                }
                return redirect($main)->with('error', json_encode($code));
            }
            return response()->json(['message' => 'Not allowed to connect']);
        }
    }

    protected const VIEW = 'autoauth';

    public function authChecking(Request $req)
    {
        $location = $req->query('location');
        $token = $req->query('token');

        dd($location);

        if ($req->ajax()) {
            if ($req->has('location') && $req->has('token')) {
                $location = $req->location;
                $with     = 'token';
                $user     = Account::with($with)->where('location_id', $req->location)->first();
                $isNew    = false;
                if (! $user) {
                    $user              = new Account();
                    $user->name        = 'Location User';
                    $user->email       = $location . '@autoauth.net';
                    $user->password    = bcrypt('autoauth_' . $location);
                    $user->location_id = $location;
                    $user->ghl_api_key = '-';
                    $isNew             = true;
                    $user->save();
                }
                $user->ghl_api_key = $req->token;
                if (!$isNew) {
                    $user->save();
                }

                $res                = new \stdClass;
                $res->user_id       = $user->id;
                $res->location_id   = $user->location_id ?? null;
                $res->is_crm        = false;
                $res->token         = $user->ghl_api_key;
                $token              = $user->{$with} ?? null;
                $res->crm_connected = false;

                if ($token) {
                    // request()->code = $token;
                    list($tokenx, $token) = CRM::go_and_get_token($token->refresh_token, 'refresh', $user->id, $token);
                    $res->crm_connected   = $tokenx && $token;
                }
                if (! $res->crm_connected) {
                    $res->crm_connected = CRM::ConnectOauth($req->location, $res->token, false, $user->id);
                }
                if ($res->crm_connected) {
                    if (Auth::check()) {
                        Auth::logout();
                        sleep(1);
                        //return response()->json(['logout user']);
                    }
                    Auth::login($user);
                }

                $res->is_crm   = $res->crm_connected;
                $res->token_id = encrypt($res->user_id);
                $res->route    = route('location.home');

                return response()->json($res);
            }
        }
        return response()->json(['status' => 'invalid request']);
    }

    public function connect()
    {
        return view(self::VIEW . '.connect');
    }

    public function authError()
    {
        return view(self::VIEW . '.error');
    }

}
