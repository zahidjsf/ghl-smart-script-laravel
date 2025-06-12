<?php

namespace App\Services\AdminPanel;

use App\Repositories\Interfaces\AdminPanel\SnapshotRepositoryInterface;
use Yajra\DataTables\Facades\DataTables;

class SnapshotService
{
    protected $snapshotRepository;

    public function __construct(SnapshotRepositoryInterface $snapshotRepository)
    {
        $this->snapshotRepository = $snapshotRepository;
    }

    public function getSnapshotsForDatatable()
    {
        $query = $this->snapshotRepository->getSnapshotsForDatatable();

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
                }
                return 'No Image';
            })
            ->addColumn('project_name', function ($snapshot) {
                return $snapshot->project ? $snapshot->project->name : 'No Project Assigned';
            })
            ->rawColumns(['action', 'image'])
            ->make(true);
    }

    public function createSnapshot(array $data, $imageFile = null)
    {
        if ($imageFile) {
            $data['server_image'] = $this->snapshotRepository->uploadImage($imageFile);
        }

        return $this->snapshotRepository->create($data);
    }

    public function updateSnapshot($id, array $data, $imageFile = null)
    {
        if ($imageFile) {
            $data['server_image'] = $this->snapshotRepository->uploadImage($imageFile);
        }

        return $this->snapshotRepository->update($id, $data);
    }

    public function deleteSnapshot($id)
    {
        return $this->snapshotRepository->delete($id);
    }

    public function getSnapshotDataForEdit($id)
    {
        return [
            'snapshot' => $this->snapshotRepository->find($id),
            'projects' => $this->snapshotRepository->getSystemProjects()
        ];
    }

    public function getSystemProjects()
    {
        return $this->snapshotRepository->getSystemProjects();
    }
}
