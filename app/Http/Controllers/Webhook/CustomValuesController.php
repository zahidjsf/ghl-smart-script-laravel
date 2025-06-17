<?php

namespace App\Http\Controllers\Webhook;

use App\Helper\CRM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\APIWebhook;
use App\Models\Location;
use App\Models\CollectionAssign;
use App\Models\CustomValue;
use Illuminate\Support\Facades\Log;
use stdClass;

class CustomValuesController extends Controller
{
    public function WebhookCustomValues(Request $request)
    {
        $jsonText = $request->json()->all() ?: $request->all();

        $locId = $request->input('location55', $jsonText['customData']['location'] ?? null);

        if (!$locId) {
            return response()->json(['error' => 'Location ID is required'], 400);
        }

        $location = Location::where('loc_id', $locId)->first();
        if (!$location) {
            return response()->json(['error' => 'Location not found'], 400);
        }

        $extra = new stdClass;
        $extra->proj = 7;
        $extra = json_encode($extra);

        // Store webhook data
        $apiwebhook = new APIWebhook();
        $apiwebhook->a_id = $location->a_id;
        $apiwebhook->loc_id = $location->loc_id;
        $apiwebhook->data = json_encode($jsonText);
        $apiwebhook->status = 'updateCV';
        $apiwebhook->type = $request->input('action', '');
        $apiwebhook->date = now();
        $apiwebhook->extra = $extra;
        $apiwebhook->notes = 'Custom Fields Updated';
        $apiwebhook->save();

        // Trigger CV update
        $this->processCVUpdates();

        return response()->json(['message' => 'Webhook received'], 200);
    }

    public function processCVUpdates()
    {
        $webhooks = APIWebhook::where('status', 'updateCV')->limit(10)->get();
        if ($webhooks->isEmpty()) {
            return;
        }
        foreach ($webhooks as $webhook) {
            $this->processSingleWebhook($webhook);
        }
    }

    protected function processSingleWebhook($webhook)
    {
        $hookData = json_decode($webhook->data);
        $account = DB::table('accounts')->where('id', $webhook->a_id)->first();
        $locationId = $webhook->loc_id;
        $location = Location::where('loc_id', $locationId)->first();
        if (!$location) {
            $this->markWebhookFailed($webhook, 'Location not found');
            return;
        }
        $phone = $hookData->phone ?? '';
        $type = $webhook->type;
        // Get collection assignments
        $collections = CollectionAssign::where('proj_id', 7)->where('loc_id', $location->loc_id)->get();
        if ($collections->isEmpty()) {
            $error = "No collection assigned to location";
            $this->markWebhookFailed($webhook, $error);
            $this->sendNotification($account->email, "UPDATE CV: Collection Error", $error);
            return;
        }

        // Get custom values
        $customValues = CustomValue::whereIn('col_id', $collections->pluck('col_id'))->get();

        $valuesToUpdate = [];
        foreach ($customValues as $cv) {
            if (isset($hookData->{$cv->custom_field})) {
                $valuesToUpdate[] = [
                    'name' => str_replace('"', '', $cv->name),
                    'mergeKey' => $cv->mergeKey,
                    'id' => "",
                    'value' => str_replace('"', '', $hookData->{$cv->custom_field})
                ];
            }
        }

        // Get GHL custom values
        $ghlCustomValues = $this->getGHLCustomValues($location);

        if (empty($ghlCustomValues)) {
            $error = "Could not get custom values from GHL";
            $this->markWebhookFailed($webhook, $error);
            return;
        }

        // Prepare updates
        $updates = [];
        foreach ($ghlCustomValues['customValues'] as $ghlValue) {
            foreach ($valuesToUpdate as $cv) {
                if (
                    str_replace('"', '', $ghlValue['name']) == $cv['name'] ||
                    str_replace(' ', '', $ghlValue['fieldKey']) == str_replace(' ', '', $cv['mergeKey'])
                ) {
                    if (!empty($cv['value'])) {
                        $updates[$ghlValue['id']] = [
                            'value' => is_array($cv['value']) ? json_encode($cv['value']) : $cv['value'],
                            'name' => $cv['name']
                        ];
                    }
                }
            }
        }

        if (empty($updates)) {
            $error = "No matching custom values to update";
            $this->markWebhookFailed($webhook, $error);
            $this->sendNotification($account->email, "CV Update Error", $error);
            return;
        }

        // Perform updates
        foreach ($updates as $id => $data) {
            $this->updateGHLCustomValue($location, $id, $data);
        }
        $webhook->update(['status' => 'added']);
    }

    protected function getGHLCustomValues($location)
    {
        if ($location) {
            $locId = $location->loc_id;
            $url = 'locations/' . $locId . '/customValues';
            $response = CRM::crmV2(auth()->user()->id, $url,  'get', '', [], false, $locId);
            return $response->json();
        }
        return;
    }

    protected function updateGHLCustomValue($location, $cvId, $data)
    {

        $locationId = $location->loc_id;
        $userId = auth()->user()->id;
        $url = 'locations/' . $locationId . '/customValues/' . $cvId;
        $response = CRM::crmV2($userId, $url, 'put', $data, [], false, $locationId);

        if (!$response->successful()) {
            Log::error("Failed to update GHL custom value", [
                'location_id' => $location->id,
                'custom_value_id' => $cvId,
                'response' => $response->body()
            ]);
        }

        return $response->successful();
    }

    protected function markWebhookFailed($webhook, $error)
    {
        $webhook->update([
            'status' => 'error',
            'notes' => $error
        ]);
    }

    protected function sendNotification($email, $subject, $message)
    {
        // Implement your email sending logic here
        // mail($email, $subject, $message);
    }
}
