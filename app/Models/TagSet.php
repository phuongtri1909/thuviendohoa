<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagSet extends Model
{
    protected $table = 'tag_sets';
    protected $fillable = ['tag_id', 'set_id'];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}
