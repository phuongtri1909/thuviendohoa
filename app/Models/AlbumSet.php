<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumSet extends Model
{
    protected $table = 'album_sets';
    protected $fillable = ['album_id', 'set_id'];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}
