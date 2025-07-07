<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPanel\ProjectStoreRequest;
use App\Http\Requests\AdminPanel\ProjectUpdateRequest;
use App\Services\AdminPanel\ProjectService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        return view('adminpanel.project.manage');
    }

    public function getProjects()
    {
        $account = LoginUser();
        $projects = $this->projectService->getAllProjectsForAccount($account);

        return DataTables::of($projects)
            ->addColumn('actions', function ($project) {
                $html = ' <a href="' . route('admin.projectedit', $project->id) . '" class="btn btn-xs btn-primary">Edit</a> ';
                $html .= ' <a href="' . route('admin.projectdelete', $project->id) . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this project?\')">Delete</a> ';
                return $html;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        return view('adminpanel.project.add');
    }

    public function edit($id)
    {
        $project = $this->projectService->findProject($id);
        return view('adminpanel.project.edit', compact('project'));
    }
    public function store(ProjectStoreRequest $request)
    {
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'cv_collections' => $request->input('cvcol'),
            'inMembership' => $request->has('inMembership') ? 'yes' : 'no',
            'a_id' => auth()->id()
        ];

        $this->projectService->createProject($data);

        return redirect()->route('admin.projects')->with('success', 'Project added successfully');
    }

    public function update(ProjectUpdateRequest $request, $id)
    {
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'cv_collections' => $request->input('cvcol'),
            'inMembership' => $request->has('inMembership') ? 'yes' : 'no'
        ];

        $this->projectService->updateProject($id, $data);

        return redirect()->route('admin.projects')->with('success', 'Project updated successfully');
    }


    public function destroy($id)
    {
        $this->projectService->deleteProject($id);
        return redirect()->back()->with('success', 'Project deleted Successfully!');
    }
}
