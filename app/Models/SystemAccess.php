<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemAccess extends Model
{
    use HasFactory;
    protected $table = 'systemaccess';
    public $timestamps = false;
}
