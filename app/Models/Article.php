<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $connection = 'ghlsmartscripts_docs';

    protected $table = 'articles';
    public $timestamps = false;
    public function content()
    {
        return $this->hasOne(ArticleContent::class, '_id', '_id');
    }
}
