<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = ['name', 'slug'];

    public function tagSets(): HasMany
    {
        return $this->hasMany(TagSet::class);
    }

    public function sets(): BelongsToMany
    {
        return $this->belongsToMany(Set::class, 'tag_sets');
    }
}
