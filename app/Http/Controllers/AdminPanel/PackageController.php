<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipPackage;
use App\Models\MembershipPackageDetail;
use App\Models\SystemProject;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class PackageController extends Controller
{
    public function index()
    {
        return view('adminpanel.package.manage');
    }


    public function getpackages(Request $request)
    {
        if ($request->ajax()) {
            $query = MembershipPackage::with(['projects']);

            if (Auth::user()->role === 'SubAdmin') {
                $query->where('a_id', Auth::id());
            }

            return DataTables::eloquent($query)
                ->addColumn('action', function ($package) {
                    return '<a href="' . route('admin.packageedit', $package->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        <a href="' . route('admin.packagedelete', $package->id) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this package?\')">Delete</a>';
                })
                ->addColumn('projects_list', function ($package) {
                    $details = $package->details;
                    $projNames = [];
                    foreach ($details as $det) {
                        $proj = SystemProject::find($det->project);
                        if ($proj) {
                            $projNames[] = '<li>' . e($proj->name) . '</li>';
                        }
                    }
                    return '<ul>' . implode('', $projNames) . '</ul>';
                })
                ->rawColumns(['action', 'projects_list'])
                ->toJson();
        }
    }


    public function create()
    {
        $projects = SystemProject::all();
        return view('adminpanel.package.add', compact('projects'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'projects' => 'required|array',
        ]);

        $package = new MembershipPackage();
        $package->a_id = Auth::id();
        $package->name = $request->name;
        $package->Description = $request->description;
        $package->save();

        foreach ($request->projects as $project) {
            $detail = new MembershipPackageDetail();
            $detail->mp_id = $package->id;
            $detail->project = $project['id'];
            $detail->credits = $project['credits'];
            $detail->perLocation = $project['per_location'];
            $detail->unlimited = $project['unlimited'] ?? false;
            $detail->cumulative = $project['cumulative'] ?? false;
            $detail->save();
        }

        return redirect()->route('admin.packages')
            ->with('success', 'Package created successfully.');
    }

    public function edit($id)
    {
        $package = MembershipPackage::with('details.project')->findOrFail($id);
        $projects = SystemProject::all();
        return view('adminpanel.package.edit', compact('package', 'projects'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'existing_projects' => 'sometimes|array',
            // 'existing_projects.*.id' => 'sometimes|exists:membership_package_details,id',
            // 'existing_projects.*.credits' => 'sometimes|integer',
            // 'existing_projects.*.per_location' => 'sometimes|integer',
            'new_projects' => 'sometimes|array',
            // 'new_projects.*.id' => 'sometimes|exists:system_projects,id',
            // 'new_projects.*.credits' => 'sometimes|integer',
            // 'new_projects.*.per_location' => 'sometimes|integer',
        ]);

        $package = MembershipPackage::findOrFail($id);
        $package->name = $request->name;
        $package->description = $request->description;
        $package->save();

        // Update existing projects
        if ($request->has('existing_projects')) {
            foreach ($request->existing_projects as $project) {
                $detail = MembershipPackageDetail::find($project['id']);
                if ($detail) {
                    $detail->credits = $project['credits'];
                    $detail->perLocation = $project['per_location'];
                    $detail->unlimited = $project['unlimited'] ?? false;
                    $detail->cumulative = $project['cumulative'] ?? false;
                    $detail->save();
                }
            }
        }

        // Add new projects
        if ($request->has('new_projects')) {
            foreach ($request->new_projects as $project) {
                $detail = new MembershipPackageDetail();
                $detail->mp_id = $package->id;
                $detail->project = $project['id'];
                $detail->credits = $project['credits'];
                $detail->perLocation = $project['per_location'];
                $detail->unlimited = $project['unlimited'] ?? false;
                $detail->cumulative = $project['cumulative'] ?? false;
                $detail->save();
            }
        }

        return redirect()->route('admin.packages')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $package = MembershipPackage::findOrFail($id);

        $detail = MembershipPackageDetail::where('mp_id', $id)->get();
        foreach ($detail as $key => $det) {
            $det->delete();
        }
        $package->delete();
        return redirect()->route('admin.packages')
            ->with('success', 'Package deleted successfully.');
    }

    public function removeProject($packageId, $detailId)
    {
        $detail = MembershipPackageDetail::where('mp_id', $packageId)
            ->where('id', $detailId)
            ->firstOrFail();

        $detail->delete();

        return back()->with('success', 'Project removed from package successfully.');
    }
}
