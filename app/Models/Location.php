<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Location extends Authenticatable
{
    use HasFactory;
    protected $hidden = ['password'];
    protected $table = 'locations';
    public $timestamps = false;
    const defaultProjectId = 2;
    public function customValueCollections()
    {
        return $this->belongsToMany(CustomValueCollection::class, 'collection_assign', 'loc_id', 'col_id')
            ->where('proj_id', self::defaultProjectId);
    }
}
