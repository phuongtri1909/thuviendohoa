<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class Album extends Model
{
    protected $table = 'albums';
    protected $fillable = ['name', 'slug', 'image', 'icon'];

    public function albumSets(): HasMany
    {
        return $this->hasMany(AlbumSet::class);
    }

    public function albumTypes(): HasMany
    {
        return $this->hasMany(AlbumType::class);
    }

    public function featuredType(): HasOne
    {
        return $this->hasOne(AlbumType::class)->where('type', AlbumType::TYPE_FEATURED);
    }

    public function trendingType(): HasOne
    {
        return $this->hasOne(AlbumType::class)->where('type', AlbumType::TYPE_TRENDING);
    }

    public function isFeatured(): bool
    {
        return $this->albumTypes()->where('type', AlbumType::TYPE_FEATURED)->exists();
    }

    public function isTrending(): bool
    {
        return $this->albumTypes()->where('type', AlbumType::TYPE_TRENDING)->exists();
    }

    public function markFeatured(?int $order = 0): void
    {
        $this->albumTypes()->updateOrCreate(
            ['type' => AlbumType::TYPE_FEATURED],
            ['order' => $order ?? 0]
        );
    }

    public function unmarkFeatured(): void
    {
        $this->albumTypes()->where('type', AlbumType::TYPE_FEATURED)->delete();
    }

    public function markTrending(?int $order = 0): void
    {
        $this->albumTypes()->updateOrCreate(
            ['type' => AlbumType::TYPE_TRENDING],
            ['order' => $order ?? 0]
        );
    }

    public function unmarkTrending(): void
    {
        $this->albumTypes()->where('type', AlbumType::TYPE_TRENDING)->delete();
    }

    public function scopeFeatured($query)
    {
        return $query->whereHas('albumTypes', function ($q) {
            $q->where('type', AlbumType::TYPE_FEATURED);
        });
    }

    public function scopeTrending($query)
    {
        return $query->whereHas('albumTypes', function ($q) {
            $q->where('type', AlbumType::TYPE_TRENDING);
        });
    }

    public static function processAndSaveImage(UploadedFile $imageFile): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("albums/{$yearMonth}/original");

        $extension = strtolower($imageFile->getClientOriginalExtension());
        
        if ($extension === 'svg') {
            $relativePath = "albums/{$yearMonth}/original/{$fileName}.svg";
            Storage::disk('public')->put($relativePath, file_get_contents($imageFile->getRealPath()));
        } else {
            $processed = Image::make($imageFile);
            $processed->encode('webp', 90);
            $relativePath = "albums/{$yearMonth}/original/{$fileName}.webp";
            Storage::disk('public')->put($relativePath, $processed->stream());
        }

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
