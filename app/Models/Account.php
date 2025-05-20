<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Account extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;
    public $timestamps = false;
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relationship with AccountDetail
    public function detail()
    {
        return $this->hasOne(AccountDetail::class, 'a_id', 'id');
    }

    // Relationship for sub-accounts (if parent)
    public function subAccounts()
    {
        return $this->hasMany(AccountDetail::class, 'parent_id', 'id');
    }


    public function systemAccess()
    {
        return $this->hasOne(SystemAccess::class, 'a_id');
    }

    public function projects()
    {
        return $this->belongsToMany(SystemProject::class, 'projLicenses', 'a_id', 'proj_id')
                    ->withPivot('numLicenses', 'isUnlimited', 'perLocation', 'video_minutes', 'status');
    }

    public function isBundleMember()
    {
        return $this->hasOne(SSMembership::class, 'a_id');
    }

    public function accountDetails()
    {
        return $this->hasOne(AccountDetail::class, 'a_id');
    }

}
