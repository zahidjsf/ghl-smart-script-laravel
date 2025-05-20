<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemProject extends Model
{
    use HasFactory;
    protected $table = 'systemprojects';

    public $timestamps = false;
}
