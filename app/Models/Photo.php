<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';
    protected $fillable = ['set_id', 'path','size'];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function getSizeAttribute($value)
    {
        return $this->attributes['size'] . ' MB';
    }
}
