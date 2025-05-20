<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SSMembership extends Model
{
    use HasFactory;
    protected $table = 'ss_membership';
    public $timestamps = false;
}
