<?php

namespace App\Http\Controllers;

use App\Helper\CRM;
use App\Models\Account;
use App\Models\CrmToken;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CRMConnectionController extends Controller
{
    public function crmCallback(Request $request)
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

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

    private function evp_bytes_to_key($password, $salt)
    {
        $key = '';
        $iv = '';
        $derived_bytes = '';
        $previous = '';

        // Concatenate MD5 results until we generate enough key material (32 bytes key + 16 bytes IV = 48 bytes)
        while (strlen($derived_bytes) < 48) {
            $previous = md5($previous . $password . $salt, true);
            $derived_bytes .= $previous;
        }

        // Split the derived bytes into the key (first 32 bytes) and IV (next 16 bytes)
        $key = substr($derived_bytes, 0, 32);
        $iv = substr($derived_bytes, 32, 16);

        return [
            $key,
            $iv
        ];
    }
    public function decryptSSO(Request $request)
    {
        try {
            $ssoKey = env('SSO_KEY', null);
            if (!$ssoKey) {
                return response()->json(['status' => false, 'message' => 'SSO key is not configured.']);
            }
            $ciphertext = base64_decode($request->ssoToken);

            if (substr($ciphertext, 0, 8) !== "Salted__") {
                return response()->json(['status' => false]);
            }
            $salt = substr($ciphertext, 8, 8);
            $ciphertext = substr($ciphertext, 16);
            list($key, $iv) = self::evp_bytes_to_key($ssoKey, $salt);
            $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

            if ($decrypted === false) {
                return response()->json(['status' => false]);
            } else {

                $decrypted_data = json_decode($decrypted, true);
                \Log::info($decrypted_data);

                $comapnyId = $decrypted_data['companyId'];

                $userId = CrmToken::where('companyId', $comapnyId)->value('a_id');
                if ($userId) {
                    $user = Account::where('id', $userId)->first();
                    if ($user) {
                        Auth::login($user);
                    }
                    if (Auth::check()) {
                        return response()->json(['status' => true, 'user' => Auth::user()]);
                    }
                    return response()->json(['status' => false, 'message' => 'Auth session initialization failed.']);
                }
            }
        } catch (Exception $e) {
            Log::error('SSO Decryption Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'An error occurred while processing your request.']);
        }
    }


}
