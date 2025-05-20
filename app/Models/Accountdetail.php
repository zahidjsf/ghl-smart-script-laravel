<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accountdetail extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'accountsdetails';

    public function account()
    {
        return $this->belongsTo(Account::class, 'a_id', 'id');
    }

    // Relationship with parent Account
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id', 'id');
    }

}
