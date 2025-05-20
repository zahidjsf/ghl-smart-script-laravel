<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPackageDetail extends Model
{
    use HasFactory;

    protected $table = 'membershippackagedetails';
    public $timestamps = false;
    
    public function project()
    {
        return $this->belongsTo(SystemProject::class, 'project_id');
    }
    
    public function package()
    {
        return $this->belongsTo(MembershipPackage::class, 'mp_id');
    }
}
