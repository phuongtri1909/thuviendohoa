<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Banner extends Model
{
    protected $fillable = ['image', 'key_page', 'order', 'status'];

    const PAGE_HOME = 'home';
    const PAGE_SEARCH = 'search';

    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;

    public static function processAndSaveImage(UploadedFile $imageFile): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("banners/{$yearMonth}");

        $processed = Image::make($imageFile);
        $processed->encode('webp', 90);

        $relativePath = "banners/{$yearMonth}/{$fileName}.webp";
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

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image);
    }

    public function scopeForPage($query, string $page)
    {
        return $query->where('key_page', $page)->where('status', self::STATUS_ACTIVE)->orderBy('order');
    }
}