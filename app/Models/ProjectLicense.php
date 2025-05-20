<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLicense extends Model
{
    use HasFactory;
    protected $table = 'projlicenses';
    public $timestamps = false;
}
