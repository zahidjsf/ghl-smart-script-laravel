<?php
namespace App\Jobs;

use App\Helper\CRM;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateGHLCustomValueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $location;
    protected $cvId;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($location, $cvId, $data)
    {
        $this->location = $location;
        $this->cvId = $cvId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $locationId = $this->location->loc_id;
        $userId = $this->location->a_id;
        $url = 'locations/' . $locationId . '/customValues/' . $this->cvId;

        $response = CRM::crmV2($userId, $url, 'put', $this->data, [], false, $locationId);

        if (!$response || !property_exists($response, 'customValue')) {
            Log::error("Failed to update GHL custom value", [
                'location_id' => $this->location->id ?? null,
                'custom_value_id' => $this->cvId,
                'response' => method_exists($response, 'body') ? $response->body() : $response
            ]);
        }
    }
}
