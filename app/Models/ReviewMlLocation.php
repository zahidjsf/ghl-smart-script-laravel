<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewMlLocation extends Model
{
    use HasFactory;
    protected $table = 'reviewML_Locations';
    public $timestamps = false;
}
