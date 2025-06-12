<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Services\AdminPanel\SnapshotService;
use Illuminate\Http\Request;

class SnapshotController extends Controller
{
    protected $snapshotService;

    public function __construct(SnapshotService $snapshotService)
    {
        $this->snapshotService = $snapshotService;
    }

    public function index()
    {
        return view('adminpanel.snapshot.manage');
    }

    public function getsnapshots(Request $request)
    {
        if ($request->ajax()) {
            return $this->snapshotService->getSnapshotsForDatatable();
        }
    }

    public function create()
    {
        $projects = $this->snapshotService->getSystemProjects();
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
            'server_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $this->snapshotService->createSnapshot(
            $validated,
            $request->file('server_image')
        );

        return redirect()->route('admin.snapshots')->with('success', 'Snapshot created successfully.');
    }

    public function edit(string $id)
    {
        $data = $this->snapshotService->getSnapshotDataForEdit($id);
        return view('adminpanel.snapshot.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'documentation' => 'nullable|string',
            'proj_id' => 'nullable|exists:systemprojects,id',
            'server_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $this->snapshotService->updateSnapshot(
            $id,
            $validated,
            $request->file('server_image')
        );

        return redirect()->route('admin.snapshots')->with('success', 'Snapshot updated successfully.');
    }

    public function destroy($id)
    {
        $this->snapshotService->deleteSnapshot($id);
        return redirect()->route('admin.snapshots')->with('success', 'Snapshot deleted successfully.');
    }
}
