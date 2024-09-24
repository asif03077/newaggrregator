<?php
// app/Models/Article.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id', 'author_id', 'category_id', 'author', 'title', 'description', 'url', 'urlToImage', 'published_at', 'type', 'category'
    ];

    // Define the inverse relationship with Source
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
