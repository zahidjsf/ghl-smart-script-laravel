<?php

namespace App\Repositories\AdminPanel;

use App\Models\MembershipPackage;
use App\Models\MembershipPackageDetail;
use App\Models\SystemProject;
use App\Repositories\Interfaces\AdminPanel\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{
    public function all()
    {
        return MembershipPackage::all();
    }

    public function getPackagesForDatatable($userId, $role)
    {
         $query = MembershipPackage::with(['details']);

        if ($role === 'SubAdmin') {
            $query->where('a_id', $userId);
        }

        return $query;
    }

    public function find($id)
    {
        return MembershipPackage::findOrFail($id);
    }

    public function findWithDetails($id)
    {
        return MembershipPackage::with('details.project')->findOrFail($id);
    }

    public function create(array $data)
    {
        $package = new MembershipPackage();
        $package->a_id = $data['user_id'];
        $package->name = $data['name'];
        $package->Description = $data['description'];
        $package->save();

        return $package;
    }

    public function update($id, array $data)
    {
        $package = $this->find($id);
        $package->name = $data['name'];
        $package->Description = $data['description'];
        $package->save();

        return $package;
    }

    public function delete($id)
    {
        $package = $this->find($id);
        $this->deleteAllPackageDetails($id);
        return $package->delete();
    }

    public function getSystemProjects()
    {
        return SystemProject::all();
    }

    public function createPackageDetail($packageId, array $detailData)
    {
        $detail = new MembershipPackageDetail();
        $detail->mp_id = $packageId;
        $detail->project = $detailData['project_id'];
        $detail->credits = $detailData['credits'];
        $detail->perLocation = $detailData['per_location'];
        $detail->unlimited = $detailData['unlimited'] ?? false;
        $detail->cumulative = $detailData['cumulative'] ?? false;
        $detail->save();

        return $detail;
    }

    public function updatePackageDetail($detailId, array $detailData)
    {
        $detail = MembershipPackageDetail::findOrFail($detailId);
        $detail->credits = $detailData['credits'];
        $detail->perLocation = $detailData['per_location'];
        $detail->unlimited = $detailData['unlimited'] ?? false;
        $detail->cumulative = $detailData['cumulative'] ?? false;
        $detail->save();

        return $detail;
    }

    public function deletePackageDetail($detailId)
    {
        $detail = MembershipPackageDetail::findOrFail($detailId);
        return $detail->delete();
    }

    public function deleteAllPackageDetails($packageId)
    {
        return MembershipPackageDetail::where('mp_id', $packageId)->delete();
    }

    public function removeProjectFromPackage($packageId, $detailId)
    {
        $detail = MembershipPackageDetail::where('mp_id', $packageId)
            ->where('id', $detailId)
            ->firstOrFail();

        return $detail->delete();
    }
}
