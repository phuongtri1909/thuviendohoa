<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorSet extends Model
{
    protected $table = 'color_sets';
    protected $fillable = ['color_id', 'set_id'];

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}
