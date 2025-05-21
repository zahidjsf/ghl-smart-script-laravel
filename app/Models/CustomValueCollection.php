<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomValueCollection extends Model
{
    use HasFactory;
    protected $table = 'customvaluecollections';
    public $timestamps = false;

     public function customValues()
    {
        return $this->hasMany(CustomValue::class, 'col_id');
    }

}
