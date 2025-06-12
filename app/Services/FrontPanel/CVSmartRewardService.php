<?php

namespace App\Services\FrontPanel;

use App\Repositories\Interfaces\FrontPanel\CVSmartRewardRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CVSmartRewardService
{
    protected $repository;

    public function __construct(CVSmartRewardRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getLocationData($id, $userId)
    {
        $location = $this->repository->getLocationById($id);
        if (!$location) {
            return null;
        }

        $customValues = $this->repository->getGhlCustomValues($userId, $location->loc_id);
        if (!$customValues) {
            return null;
        }

        $collectionIds = $this->repository->getCollectionIds($location->id);
        $customValueDefinitions = $this->repository->getCustomValueDefinitions($collectionIds);

        $inputs = $this->prepareInputs($customValues->customValues, $customValueDefinitions);

        return [
            'location' => $location,
            'inputs' => $inputs
        ];
    }

    public function updateCustomValues($requestData, $userId)
    {
        $location = $this->repository->getLocationById($requestData['locid']);
        if (!$location) {
            return false;
        }

        // Handle file uploads (currently commented out in original)
        // foreach ($requestData->allFiles() as $fieldName => $file) {
        //     $this->handleFileUpload($file, $fieldName, $location);
        // }

        // Handle text/boolean updates
        foreach ($requestData as $key => $value) {
            if ($this->shouldProcessField($key)) {
                $this->processFieldUpdate($key, $value, $location, $userId);
            }
        }

        return true;
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
        return $ghlValue->name === $cv['name'] ||
               str_replace(' ', '', $ghlValue->fieldKey) === str_replace(' ', '', $cv['mergeKey']);
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

            $path = $file->store('smart_images', 's3');
            $url = Storage::disk('s3')->url($path);

            $this->processFieldUpdate($key, $url, $location, auth()->user()->id, $cvName);
        }
    }

    protected function shouldProcessField($key)
    {
        return strpos($key, '_token') === false &&
               strpos($key, 'submit') === false &&
               strpos($key, 'locid') === false &&
               strpos($key, 'r') === false &&
               strpos($key, 'ALT-') === false &&
               $key !== 'addLicensekey' &&
               $key !== 'addLocationId';
    }

    protected function processFieldUpdate($key, $value, $location, $userId, $name = null)
    {
        $fieldParts = explode("||", $key);
        $key = $fieldParts[0];
        $name = $name ?? ($fieldParts[1] ?? '');

        $value = str_replace(['"', '\/'], ['', '/'], trim($value));
        if ($value === "NA") {
            $value = "";
        }

        $parts = explode('-', $key);
        $cvId = (count($parts) > 1) ? $parts[1] : $key;

        $data = ['value' => $value];
        if ($name) {
            $data['name'] = str_replace("_", " ", $name);
        }

        $this->repository->updateGhlCustomValue($userId, $location->loc_id, $cvId, $data);
    }
}
