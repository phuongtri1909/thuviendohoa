<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySet extends Model
{
    protected $table = 'category_sets';
    protected $fillable = ['category_id', 'set_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}
