<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class EmailTemplateController extends Controller
{

    public function index()
    {
        $path = 'email-templates/activation-email-template.html';
        if (!Storage::disk('local')->exists($path)) {
            Storage::disk('local')->put($path, ''); // Create the file if it doesn't exist
        }

        $contents = Storage::disk('local')->get($path);

        return view('adminpanel.emailtemplate.manage', compact('contents'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'emailtemplate' => 'required|string',
        ]);
        Storage::disk('local')->put('email-templates/activation-email-template.html', $request->input('emailtemplate'));
        return redirect()->back()->with('success', 'Template updated successfully!');
    }
}
