<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagBlog extends Model
{
    protected $table = 'tag_blogs';
    protected $fillable = ['name', 'slug'];

    public function blogTags()
    {
        return $this->hasMany(BlogTag::class, 'tag_id');
    }

    public function blogs()
    {
        return $this->hasManyThrough(Blog::class, BlogTag::class, 'tag_id', 'id', 'id', 'blog_id');
    }
}
