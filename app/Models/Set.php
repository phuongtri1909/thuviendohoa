<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $table = 'sets';
    protected $fillable = ['name', 'slug','type', 'description', 'image', 'status', 'keywords','formats','size','price'];


    const TYPE_FREE = 'free';
    const TYPE_PREMIUM = 'premium';

    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    
    public function colors()
    {
        return $this->hasMany(ColorSet::class);
    }

    public function categories()
    {
        return $this->hasMany(CategorySet::class);
    }

    public function albums()
    {
        return $this->hasMany(AlbumSet::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function tags()
    {
        return $this->hasMany(TagSet::class);
    }

    public function software()
    {
        return $this->hasMany(Software::class);
    }
}
