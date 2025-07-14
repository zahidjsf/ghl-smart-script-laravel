<?php

use App\Models\Account;
use App\Models\Setting;
use App\Models\Article;
use Illuminate\Support\Str;
use App\Models\Location;
use App\Models\LocationSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if(!function_exists('onlyWhatsApp')){
    function onlyWhatsApp(){
        dd('done');
    }
}

if (!function_exists('save_my_settings')) {
    function save_my_settings($key, $value)
    {
        $obj = Setting::where('key', $key)->first();
        if (!$obj) {
            $obj = new Setting();
            $obj->key = $key;
        }
        $obj->value = $value;
        $obj->save();
        cache_put($key,$value);
    }
}

if (!function_exists('get_default_settings')) {
    function get_default_settings($key, $default = '')
    {
        $setting = cache_get($key);
        if(!empty($setting)){
            return $setting;
        }
        $setting = Setting::where('key', $key)->pluck('value', 'key')->toArray();
        return $setting[$key] ?? $default;
    }
}

if (!function_exists('cache_get')) {
    function cache_get($key, $default = '')
    {
        return  cache()->get($key) ?? $default;
    }
}
if (!function_exists('cache_put')) {
    function cache_put($key, $value = '')
    {
        return  cache()->put($key,$value);
    }
}


function getScriptsEntries($docsProj = "1", $order = "id", $direction = "asc", $limit = "nolim")
{
    // Validate and sanitize inputs
    $projID = (int)$docsProj;
    $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

    // Base query
    $query = Article::with('content')
        ->where('status', 1)
        ->where('projects__id', $projID);

    // Apply ordering
    if ($order === 'date') {
        $query->orderBy('created_at', $direction);
    } elseif ($order === 'list_order') {
        $query->orderBy('list_order', $direction);
    } else {
        // Default order by id if not specified
        $query->orderBy('id', $direction);
    }

    // Apply limit if needed
    if ($limit !== 'nolim') {
        $limitValue = (int)$limit;
        $query->limit($limitValue);
    }

    // Execute and return results
    return $query->get()->toArray();
}


function setting($key, $id = 1)
{
    $setting = Setting::pluck('value', 'key');
    if ($key == 'all') {
        return $setting;
    }
    return $setting[$key] ?? null;
}

function LoginUser($onlyId = false){
    $user = Auth::user();
    if(!$user){
        return null;
    }
    if($onlyId){
        return $user->id;
    }
    return $user;
}

//Need to remove this
function decryptAPI($apikey){

		// Store the cipher method
		$ciphering = "AES-128-CTR";
		// Non-NULL Initialization Vector for decryption
		$decryption_iv = '1231231231234456';
		$options = 0;
		// Store the decryption key
		$decryption_key = "GHLSmartReviews";
		$tag = "";

		// Use openssl_decrypt() function to decrypt the data
		$decryption=openssl_decrypt ((string)$apikey, $ciphering,
				$decryption_key, $options, $decryption_iv,$tag);

		return $decryption;

}

function getLocationSettings($loc, $proj = "")
{
    if (strlen($loc) > 10) {
        $loc = getLocIdFromGHLLocationId($loc, $proj);
    }

    $lid = DB::connection()->getPdo()->quote($loc);

    $setting = LocationSetting::where('loc_id', $lid)
                ->where('proj', $proj)
                ->first();

    return $setting ? json_decode($setting->settings, true) : false;
}


function getLocIdFromGHLLocationId($locationId, $proj_id = "")
{
    $query = Location::where('loc_id', $locationId);

    if (!empty($proj_id)) {
        $query->where('proj_id', $proj_id);
    }

    $location = $query->first();

    return $location ? $location->id : false;
}

function getAgencyURL($aid = "")
{
    $account = getAccountByID(DB::connection()->getPdo()->quote($aid));

    if (empty($account) || !isset($account['agency_url'])) {
        return null;
    }

    $parentURL = Str::lower(trim($account['agency_url']));

    // Remove http:// or https:// if present
    $parentURL = preg_replace('#^https?://#', '', $parentURL);

    // Ensure URL starts with https://
    $parentURL = 'https://' . $parentURL;

    // Parse URL to get clean format
    $parsed = parse_url($parentURL);

    $scheme = $parsed['scheme'] ?? 'https';
    $host = $parsed['host'] ?? str_replace($scheme.'://', '', $parentURL);

    return rtrim("{$scheme}://{$host}", '/');
}

function getAccountByID($aID)
{
    return Account::find($aID)?->toArray() ?? false;
}
