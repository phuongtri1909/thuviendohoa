<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class Software extends Model
{
    protected $table = 'software';
    protected $fillable = ['logo', 'logo_hover', 'logo_active','name'];

    public function sets()
    {
        return $this->hasMany(Set::class);
    }

    /**
     * Process an uploaded image and store as webp under public disk.
     * The type determines subfolder: logo | logo_hover | logo_active
     */
    public static function processAndSaveImage(UploadedFile $imageFile, string $type): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        $processed = Image::make($imageFile);
        $processed->encode('webp', 90);

        $relativePath = "software/{$yearMonth}/{$type}/{$fileName}.webp";
        Storage::disk('public')->makeDirectory("software/{$yearMonth}/{$type}");
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
