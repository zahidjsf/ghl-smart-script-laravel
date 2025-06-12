<?php

namespace App\Services\AdminPanel;

use App\Repositories\Interfaces\AdminPanel\PackageRepositoryInterface;
use Yajra\DataTables\Facades\DataTables;

class PackageService
{
    protected $packageRepository;

    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    public function getPackagesForDatatable($userId, $role)
    {
        $query = $this->packageRepository->getPackagesForDatatable($userId, $role);

        return DataTables::eloquent($query)
            ->addColumn('action', function ($package) {
                return '<a href="' . route('admin.packageedit', $package->id) . '" class="btn btn-primary btn-sm">Edit</a>
                <a href="' . route('admin.packagedelete', $package->id) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this package?\')">Delete</a>';
            })
            ->addColumn('projects_list', function ($package) {
                $projNames = [];
                foreach ($package->details as $det) {
                    // Make sure we have the project relation loaded and it's not just an ID
                    if ($det->relationLoaded('project') && $det->project) {
                        $projNames[] = '<li>' . e($det->project->name) . '</li>';
                    }
                }
                return '<ul>' . implode('', $projNames) . '</ul>';
            })
            ->rawColumns(['action', 'projects_list'])
            ->toJson();
    }

    public function createPackage($userId, array $data)
    {
        $packageData = [
            'user_id' => $userId,
            'name' => $data['name'],
            'description' => $data['description']
        ];

        $package = $this->packageRepository->create($packageData);

        foreach ($data['projects'] as $project) {
            $this->packageRepository->createPackageDetail($package->id, [
                'project_id' => $project['id'],
                'credits' => $project['credits'],
                'per_location' => $project['per_location'],
                'unlimited' => $project['unlimited'] ?? false,
                'cumulative' => $project['cumulative'] ?? false
            ]);
        }

        return $package;
    }

    public function updatePackage($id, array $data)
    {
        $package = $this->packageRepository->update($id, [
            'name' => $data['name'],
            'description' => $data['description']
        ]);

        // Update existing projects
        if (!empty($data['existing_projects'])) {
            foreach ($data['existing_projects'] as $project) {
                $this->packageRepository->updatePackageDetail($project['id'], [
                    'credits' => $project['credits'],
                    'per_location' => $project['per_location'],
                    'unlimited' => $project['unlimited'] ?? false,
                    'cumulative' => $project['cumulative'] ?? false
                ]);
            }
        }

        // Add new projects
        if (!empty($data['new_projects'])) {
            foreach ($data['new_projects'] as $project) {
                $this->packageRepository->createPackageDetail($package->id, [
                    'project_id' => $project['id'],
                    'credits' => $project['credits'],
                    'per_location' => $project['per_location'],
                    'unlimited' => $project['unlimited'] ?? false,
                    'cumulative' => $project['cumulative'] ?? false
                ]);
            }
        }

        return $package;
    }

    public function deletePackage($id)
    {
        return $this->packageRepository->delete($id);
    }

    public function getPackageDataForEdit($id)
    {
        return [
            'package' => $this->packageRepository->findWithDetails($id),
            'projects' => $this->packageRepository->getSystemProjects()
        ];
    }

    public function removeProjectFromPackage($packageId, $detailId)
    {
        return $this->packageRepository->removeProjectFromPackage($packageId, $detailId);
    }

    public function getSystemProjects()
    {
        return $this->packageRepository->getSystemProjects();
    }
}
