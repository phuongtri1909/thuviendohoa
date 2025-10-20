<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumType extends Model
{
    protected $table = 'album_types';
    protected $fillable = ['album_id', 'type','order'];
    protected $casts = [
        'order' => 'integer',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    const TYPE_FEATURED = 'featured';
    const TYPE_TRENDING = 'trending';

    public function scopeFeatured($query)
    {
        return $query->where('type', self::TYPE_FEATURED);
    }

    public function scopeTrending($query)
    {
        return $query->where('type', self::TYPE_TRENDING);
    }
}
