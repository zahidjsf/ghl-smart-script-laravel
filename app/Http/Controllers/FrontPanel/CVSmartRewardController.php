<?php

namespace App\Http\Controllers\FrontPanel;

use App\Helper\CRM;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\CustomValue;
use App\Models\CollectionAssign;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class CVSmartRewardController extends Controller
{
    public function index($id)
    {
        $col = 32;
        // Get custom values from GHL
        $user_id = auth()->user()->id;
        $location = Location::find($id);

        $locationId = $location->loc_id;
        $url = 'locations/' . $locationId . '/customValues';
        $customValues = CRM::crmV2($user_id, $url,  'get', '', [], false, $locationId);
        // $customValues = $this->getGhlCustomValues($location);

        if (!$customValues) {
            return redirect()->back()->with(
                'error',
                "Could not retrieve Custom Values from {$location->name}. Make sure your API key is valid."
            );
        }

        $collectionIds = $this->getCollectionIds($col, $location);
        $customValueDefinitions = CustomValue::whereIn('col_id', $collectionIds)->orderBy('cv_order')->get();
        $inputs = $this->prepareInputs($customValues->customValues, $customValueDefinitions);
    
        return view('frontpanel.smartreward.customvalue', [
            'location' => $location,
            'inputs' => $inputs
        ]);

    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'locid' => 'required|exists:locations,id',
        ]);

        // Handle file uploads
        foreach ($request->allFiles() as $fieldName => $file) {
            $this->handleFileUpload($file, $fieldName, $location);
        }

        // Handle text/boolean updates
        foreach ($request->except(['_token', 'submit', 'locid', 'r']) as $key => $value) {
            if (strpos($key, 'ALT-') === false && $key !== 'addLicensekey' && $key !== 'addLocationId') {
                $this->updateCustomValue($key, $value, $location);
            }
        }

        return redirect($request->input('r', route('custom-values.index')))
            ->with('success', 'Custom Values Updated Successfully');
    }

    protected function getGhlCustomValues($location)
    {
        try {
            $apiKey = Crypt::decryptString($location->apikey);

            if ($location->loc_id) {
                // OAuth flow
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getOauthToken($location->loc_id),
                    'Version' => '2021-07-28',
                ])->get("https://services.leadconnectorhq.com/locations/{$location->loc_id}/customValues");
            } else {
                // API key flow
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])->get('https://rest.gohighlevel.com/v1/custom-values/');
            }

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    protected function getCollectionIds($col, $location)
    {
        // if ($request->has('c')) {
        //     return [$request->input('c')];
        // }

        $collectionsIds = CollectionAssign::where('loc_id', $location->id)
            ->pluck('col_id');
        return $collectionsIds;
    }

    protected function prepareInputs($ghlCustomValues, $customValueDefinitions)
    {
        $inputs = [];
        $i = 0;

        foreach ($customValueDefinitions as $cv) {
            foreach ($ghlCustomValues as $ghlValue) {
                if ($this->shouldIncludeValue($ghlValue, $cv)) {
                    $inputs[$i] = $this->buildInputArray($ghlValue, $cv, $i);
                    $i++;
                }
            }
        }

        return $inputs;
    }

    protected function shouldIncludeValue($ghlValue, $cv)
    {
        return $ghlValue->name === $cv['name'] || str_replace(' ', '', $ghlValue->fieldKey) === str_replace(' ', '', $cv['mergeKey']);
    }

    protected function buildInputArray($ghlValue, $cv, $index)
    {
        $val = $ghlValue->value ?? '';
        $default = ($cv['defaultv'] == "0") ? "" : $cv['defaultv'];

        $input = [
            'id' => $ghlValue->id,
            'i' => $index,
            'val' => ($val == "/" || $val == "." || $val == "") ? $default : $val,
            'fieldtype' => $cv['fieldType'],
            'tooltip' => $cv['tooltip'],
            'resources' => $cv['resources'] ? "<br/>Resources:<br/>" . $cv['resources'] : "",
            'name' => $cv['name'],
            'cvid' => $cv['id'],
            'readonly' => $cv['action'] == "readonly" ? "readonly" : "",
            'summernote' => $cv['cvattribute'] == 'wysiwyg' ? "summernote" . $index : "",
            'showImage' => in_array($cv['fieldType'], ["logo", "revLogo", "image"]) ? 'yes' : 'no',
        ];

        if ($cv['fieldType'] == 'revLogo') {
            $input['revOptions'] = $this->getReviewSiteOptions();
        }

        if ($cv['fieldType'] == 'boolean') {
            $input['checked'] = (strtolower((string)($ghlValue->value ?? '')) == "yes" ||
                strtolower((string)($ghlValue->value ?? '')) == "true") ? "checked" : "";
        }

        return $input;
    }

    protected function getReviewSiteOptions()
    {
        $reviewSites = [
            ['name' => 'Google', 'imgURL' => 'google.png'],
            ['name' => 'Yelp', 'imgURL' => 'yelp.png'],
        ];

        $options = '';
        foreach ($reviewSites as $site) {
            $options .= "<option value='{$site['imgURL']}'>{$site['name']}</option>";
        }

        return $options;
    }

    protected function handleFileUpload($file, $fieldName, $location)
    {
        if ($file->isValid()) {
            $fieldParts = explode("||", $fieldName);
            $key = $fieldParts[0];
            $cvName = $fieldParts[1] ?? '';

            // Upload to S3
            $path = $file->store('smart_images', 's3');
            $url = Storage::disk('s3')->url($path);

            // Update in GHL
            $this->updateGhlCustomValue($location, $key, ['value' => $url], $cvName);
        }
    }

    protected function updateCustomValue($key, $value, $location)
    {
        $fieldParts = explode("||", $key);
        $key = $fieldParts[0];
        $name = $fieldParts[1] ?? '';

        $value = str_replace(['"', '\/'], ['', '/'], trim($value));

        if ($value === "NA") {
            $value = "";
        }

        $this->updateGhlCustomValue($location, $key, ['value' => $value], $name);
    }

    protected function updateGhlCustomValue($location, $key, $data, $name = null)
    {
        try {
            $apiKey = Crypt::decryptString($location->apikey);

            if ($location->loc_id) {
                // OAuth flow
                $payload = array_merge(['name' => str_replace("_", " ", $name)], $data);

                Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getOauthToken($location->loc_id),
                    'Content-Type' => 'application/json',
                    'Version' => '2021-07-28',
                ])->put("https://services.leadconnectorhq.com/locations/{$location->loc_id}/customValues/{$key}", $payload);
            } else {
                // API key flow
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->put("https://rest.gohighlevel.com/v1/custom-values/{$key}", $data);
            }
        } catch (\Exception $e) {
            report($e);
        }
    }

    protected function getOauthToken($locationId)
    {
        // Simple implementation - you might want to cache this
        return cache()->remember("ghl_oauth_{$locationId}", now()->addHours(1), function () use ($locationId) {
            // Query your database or make an API call to get the token
            return '';
        });
    }
}
