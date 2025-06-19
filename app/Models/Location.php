<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    public $timestamps = false;
    const defaultProjectId = 2;
    public function customValueCollections()
    {
        return $this->belongsToMany(CustomValueCollection::class, 'collection_assign', 'loc_id', 'col_id')
            ->where('proj_id', self::defaultProjectId);
    }
}
