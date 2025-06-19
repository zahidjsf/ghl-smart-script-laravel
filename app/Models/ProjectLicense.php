<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProjectLicense extends Model
{
    use HasFactory;
    protected $table = 'projlicenses';
    public $timestamps = false;
    public function scopeForAccount(Builder $query, $accountId)
    {
        return $query->where('a_id', $accountId);
    }
}
