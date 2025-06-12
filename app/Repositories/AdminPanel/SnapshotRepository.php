<?php

namespace App\Repositories\AdminPanel;

use App\Models\Snapshot;
use App\Models\SystemProject;
use App\Repositories\Interfaces\AdminPanel\SnapshotRepositoryInterface;
use Illuminate\Support\Facades\File;

class SnapshotRepository implements SnapshotRepositoryInterface
{
    public function all()
    {
        return Snapshot::all();
    }

    public function getSnapshotsForDatatable()
    {
        return Snapshot::with(['project']);
    }

    public function find($id)
    {
        return Snapshot::findOrFail($id);
    }

    public function create(array $data)
    {
        $snapshot = new Snapshot();
        $snapshot->name = $data['name'];
        $snapshot->url = $data['url'];
        $snapshot->documentation = $data['documentation'] ?? null;
        $snapshot->image = $data['image'] ?? null;
        $snapshot->proj_id = $data['proj_id'] ?? null;
        $snapshot->server_image = $data['server_image'] ?? null;
        $snapshot->date = now()->toDateString();
        $snapshot->save();

        return $snapshot;
    }

    public function update($id, array $data)
    {
        $snapshot = $this->find($id);
        $snapshot->name = $data['name'];
        $snapshot->url = $data['url'];
        $snapshot->documentation = $data['documentation'] ?? null;
        $snapshot->proj_id = $data['proj_id'] ?? null;

        if (isset($data['server_image'])) {
            if ($snapshot->server_image) {
                $this->deleteImage($snapshot->server_image);
            }
            $snapshot->server_image = $data['server_image'];
        }

        $snapshot->save();
        return $snapshot;
    }

    public function delete($id)
    {
        $snapshot = $this->find($id);
        if ($snapshot->server_image) {
            $this->deleteImage($snapshot->server_image);
        }
        return $snapshot->delete();
    }

    public function getSystemProjects()
    {
        return SystemProject::all();
    }

    public function uploadImage($file)
    {
        $folder = 'uploads/snapshots';
        $filename = time() . '_' . $file->getClientOriginalName();

        if (!file_exists(public_path($folder))) {
            File::makeDirectory(public_path($folder), 0777, true);
        }

        $path = $file->move(public_path($folder), $filename);
        return $folder . '/' . $filename;
    }

    public function deleteImage($imagePath)
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            File::delete(public_path($imagePath));
        }
    }
}
