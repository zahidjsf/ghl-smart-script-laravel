<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Services\AdminPanel\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    protected $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    public function index()
    {
        return view('adminpanel.package.manage');
    }

    public function getpackages(Request $request)
    {
        if ($request->ajax()) {
            return $this->packageService->getPackagesForDatatable(
                Auth::id(),
                Auth::user()->role
            );
        }
    }

    public function create()
    {
        $projects = $this->packageService->getSystemProjects();
        return view('adminpanel.package.add', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'projects' => 'required|array',
        ]);

        $this->packageService->createPackage(Auth::id(), $request->all());

        return redirect()->route('admin.packages')
            ->with('success', 'Package created successfully.');
    }

    public function edit($id)
    {
        $data = $this->packageService->getPackageDataForEdit($id);
        return view('adminpanel.package.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'existing_projects' => 'sometimes|array',
            'new_projects' => 'sometimes|array',
        ]);

        $this->packageService->updatePackage($id, $request->all());

        return redirect()->route('admin.packages')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $this->packageService->deletePackage($id);
        return redirect()->route('admin.packages')
            ->with('success', 'Package deleted successfully.');
    }

    public function removeProject($packageId, $detailId)
    {
        $this->packageService->removeProjectFromPackage($packageId, $detailId);
        return back()->with('success', 'Project removed from package successfully.');
    }
}
