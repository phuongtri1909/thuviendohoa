<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class ContentImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'image',
        'url',
        'button_text',
        'button_position_x',
        'button_position_y',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'button_position_x' => 'string',
        'button_position_y' => 'string',
    ];

    // Keys constants
    const KEY_CONTENT1 = 'content1';
    const KEY_CONTENT2 = 'content2';

    /**
     * Scope để lấy content image theo key
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope để lấy content images đang active
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Process and save image with compression
     */
    public static function processAndSaveImage(UploadedFile $imageFile): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("content-images/{$yearMonth}");

        $processed = Image::make($imageFile);
        $processed->encode('webp', 90);

        $relativePath = "content-images/{$yearMonth}/{$fileName}.webp";
        Storage::disk('public')->put($relativePath, $processed->stream());

        return $relativePath;
    }

    /**
     * Delete image from storage
     */
    public static function deleteImage(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        
        if (str_starts_with($relativePath, 'content-images/')) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}

