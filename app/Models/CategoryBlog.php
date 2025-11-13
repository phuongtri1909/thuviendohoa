<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBlog extends Model
{
    protected $table = 'category_blogs';
    protected $fillable = ['name', 'slug', 'order'];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
