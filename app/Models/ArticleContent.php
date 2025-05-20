<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleContent extends Model
{
    use HasFactory;
    protected $connection = 'ghlsmartscripts_docs';
    protected $table = 'article_contents';
    public $timestamps = false;

    

}
