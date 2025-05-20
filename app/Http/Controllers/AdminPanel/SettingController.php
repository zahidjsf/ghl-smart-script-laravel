<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(){
        return view('adminpanel.settings.form');
    }


    public function update(Request $request)
    {
        // dd($request->all());
        foreach ($request->except('_token') as $key => $value) {
            // if ($request->hasFile($key)) {
            //     $value = uploadFile($value, 'Upload/Settings', $key . time());
            // }
            save_my_settings($key, $value);
        }

        return redirect()->back()->with('success', 'Saved');
    }
}
