<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPackage extends Model
{
    use HasFactory;
    protected $table = 'membershippackages';
    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(MembershipPackageDetail::class, 'mp_id');
    }
    
    public function projects()
    {
        return $this->belongsTo(SystemProject::class, 'project');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'a_id');
    }


}
