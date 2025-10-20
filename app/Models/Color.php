<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    protected $table = 'colors';
    protected $fillable = ['value', 'name'];

    public function colorSets(): HasMany
    {
        return $this->hasMany(ColorSet::class);
    }

    public function sets(): BelongsToMany
    {
        return $this->belongsToMany(Set::class, 'color_sets');
    }
}
