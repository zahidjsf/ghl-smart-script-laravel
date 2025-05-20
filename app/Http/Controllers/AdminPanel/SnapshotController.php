<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Snapshot;
use App\Models\SystemProject;

class SnapshotController extends Controller
{
    public function index()
    {
        return view('adminpanel.snapshot.manage');
    }

    public function getsnapshots(Request $request)
    {
        if ($request->ajax()) {
            $query = Snapshot::with(['project']);

            return DataTables::of($query)
                ->addColumn('action', function ($snapshot) {
                    $editUrl = route('admin.snapshotedit', $snapshot->id);
                    $deleteUrl = route('admin.snapshotdelete', $snapshot->id);

                    return '
                        <a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a>
                        <a href="' . $deleteUrl . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this snapshot?\')">Delete</a>
                    ';
                })
                ->addColumn('image', function ($snapshot) {
                    if ($snapshot->server_image) {
                        return '<img src="' . asset($snapshot->server_image) . '" style="max-height: 50px; max-width: 50px;" class="img-thumbnail">';
                    } elseif ($snapshot->image) {
                        return '<img src="' . $snapshot->image . '" style="max-height: 50px; max-width: 50px;" class="img-thumbnail">';
                    } else {
                        return 'No Image';
                    }
                })
                ->addColumn('project_name', function ($snapshot) {
                    return $snapshot->project ? $snapshot->project->name : 'No Project Assigned';
                })
                ->rawColumns(['action', 'image']) // Needed to render HTML in 'action' column
                ->make(true);
        }
    }



    public function create()
    {
        $projects = SystemProject::all();
        return view('adminpanel.snapshot.add', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'documentation' => 'nullable|string',
            'image' => 'nullable|string',
            'proj_id' => 'nullable|exists:systemprojects,id',
            'server_image' => 'nullable|image|mimes:jpeg,png,jpg,gif', //|max:2048
        ]);

        $snapshot = new Snapshot();
        $snapshot->name = $validated['name'];
        $snapshot->url = $validated['url'];
        $snapshot->documentation = $validated['documentation'];
        $snapshot->image = $validated['image'] ?? null;
        $snapshot->proj_id = $validated['proj_id'];
        // $snapshot->a_id = auth()->id();

        if ($request->hasFile('server_image')) {

            $image = $request->file('server_image');
            $folder = 'uploads/snapshots';
            $filename = time() . '_' . $image->getClientOriginalName();

            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0777, true);
            }
            $path = $image->move(public_path($folder), $filename);
            $snapshot->server_image = $folder . '/' . $filename;
        }

        $snapshot->save();

        return redirect()->route('admin.snapshots')->with('success', 'Snapshot created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $snapshot = Snapshot::findOrFail($id);
        $projects = SystemProject::all();
        return view('adminpanel.snapshot.edit', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Snapshot $snapshot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'documentation' => 'nullable|string',
            'proj_id' => 'nullable|exists:systemprojects,id',
            'server_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $snapshot->name = $validated['name'];
        $snapshot->url = $validated['url'];
        $snapshot->documentation = $validated['documentation'];
        $snapshot->proj_id = $validated['proj_id'];
        if ($request->hasFile('server_image')) {
            if ($snapshot->server_image && file_exists(public_path($snapshot->server_image))) {
                unlink(public_path($snapshot->server_image));
            }
            $image = $request->file('server_image');
            $folder = 'uploads/snapshots';
            $filename = time() . '_' . $image->getClientOriginalName();
            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0777, true);
            }
            $path = $image->move(public_path($folder), $filename);
            $snapshot->server_image = $folder . '/' . $filename;
        }
        $snapshot->save();
        return redirect()->route('admin.snapshots')->with('success', 'Snapshot updated successfully.');
    }


    public function destroy($id)
    {
        $snapshot = Snapshot::findOrFail($id);
        $snapshot->delete();
        return redirect()->route('admin.snapshots')->with('success', 'Snapshot deleted successfully.');
    }
}
