<?php

namespace App\Http\Controllers;

use App\Helper\CRM;
use Illuminate\Http\Request;

class CRMConnectionController extends Controller
{
    public function crmCallback(Request $request)
    {
        \Artisan::call('optimize:clear');
        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');

        $code = $request->code ?? null;
        if ($code) {
            $user_id = auth()->user()->id;
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
}
