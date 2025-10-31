<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class Set extends Model
{
    protected $table = 'sets';
    protected $fillable = ['name', 'slug','type', 'description', 'image', 'drive_url', 'status', 'keywords','formats','size','price','is_featured'];

    protected $casts = [
        'keywords' => 'array',
        'formats' => 'array',
    ];

    const TYPE_FREE = 'free';
    const TYPE_PREMIUM = 'premium';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

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

    public function category()
    {
        return $this->hasOne(CategorySet::class)->with('category');
    }

    public function albums()
    {
        return $this->hasMany(AlbumSet::class);
    }

    public function albumSets()
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
        return $this->hasMany(SoftwareSet::class);
    }

    public function purchases()
    {
        return $this->hasMany(PurchaseSet::class);
    }

    public function isPurchasedBy($userId)
    {
        return $this->purchases()->where('user_id', $userId)->exists();
    }

    public function isFree()
    {
        return $this->type === self::TYPE_FREE;
    }

    public function isPremium()
    {
        return $this->type === self::TYPE_PREMIUM;
    }

    public static function processAndSaveImage(UploadedFile $imageFile): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("sets/{$yearMonth}/original");

        $processed = Image::make($imageFile);
        $processed->encode('webp', 90);

        $relativePath = "sets/{$yearMonth}/original/{$fileName}.webp";
        Storage::disk('public')->put($relativePath, $processed->stream());

        return $relativePath;
    }

    public static function deleteImage(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        Storage::disk('public')->delete($relativePath);
    }
}
