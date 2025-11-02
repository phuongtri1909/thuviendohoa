<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class DesktopContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'logo',
        'title',
        'description',
        'features',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'features' => 'array',
    ];

    const KEY_DESKTOP = 'desktop';

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public static function processAndSaveImage(UploadedFile $imageFile, string $type = 'logo'): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $extension = $imageFile->getClientOriginalExtension();
        
        Storage::disk('public')->makeDirectory("desktop-content/{$yearMonth}/{$type}");

        if (strtolower($extension) === 'svg') {
            $fileName = "{$timestamp}_{$randomString}.svg";
            $relativePath = "desktop-content/{$yearMonth}/{$type}/{$fileName}";
            Storage::disk('public')->put($relativePath, file_get_contents($imageFile->getRealPath()));
            return $relativePath;
        }

        $fileName = "{$timestamp}_{$randomString}";
        $processed = Image::make($imageFile);
        $processed->encode('webp', 90);

        $relativePath = "desktop-content/{$yearMonth}/{$type}/{$fileName}.webp";
        Storage::disk('public')->put($relativePath, $processed->stream());

        return $relativePath;
    }

    public static function deleteImage(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        
        if (str_starts_with($relativePath, 'desktop-content/')) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}





