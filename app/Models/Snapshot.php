<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Snapshot extends Model
{
    use HasFactory;

    protected $table = 'snapshots';
    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(SystemProject::class, 'proj_id');
    }


}
