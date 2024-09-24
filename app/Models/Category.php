<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['id','name','type'];

    // A category might have multiple articles (one-to-many relationship)
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}

