<?php
use App\Models\Setting;
use App\Models\Article;
use App\Models\ArticleContent;
use Illuminate\Support\Facades\Auth;

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
