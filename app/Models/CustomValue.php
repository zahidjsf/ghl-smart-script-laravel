<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomValue extends Model
{
    use HasFactory;
    protected $table = 'customvalues';
    public $timestamps = false;

    public function collection()
    {
        return $this->belongsTo(CustomValueCollection::class, 'col_id');
    }


}
