<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use App\Services\FrontPanel\CVSmartRewardService;
use Illuminate\Http\Request;

class CVSmartRewardController extends Controller
{
    protected $service;

    public function __construct(CVSmartRewardService $service)
    {
        $this->service = $service;
    }

    public function index($id)
    {
        $userId = LoginUser(true);
        $data = $this->service->getLocationData($id, $userId);

        if (!$data) {
            return redirect()->back()->with(
                'error',
                "Could not retrieve Custom Values. Make sure your API key is valid."
            );
        }

        return view('frontpanel.smartreward.customvalue', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'locid' => 'required|exists:locations,id',
        ]);
        $success = $this->service->updateCustomValues($request->except(['_token', 'submit', 'r']), LoginUser(true));
        if (!$success) {
            return redirect()->back()->with('error', 'Failed to update custom values');
        }
        return redirect()->back()->with('success', 'Custom Values Updated Successfully');
    }
}
