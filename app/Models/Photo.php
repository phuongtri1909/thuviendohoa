<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class Photo extends Model
{
    protected $table = 'photos';
    protected $fillable = ['set_id', 'path','size'];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function getSizeAttribute($value)
    {
        return ($this->attributes['size'] ?? $value) . ' MB';
    }

    public static function processAndSavePhoto(UploadedFile $imageFile): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("photos/{$yearMonth}/original");

        $processed = Image::make($imageFile);

        try {
            $watermarkPath = public_path('images/logo/logo-site.webp');
            if (file_exists($watermarkPath)) {
                $positions = [
                    ['bottom-right', 20, 20],
                    ['top-left', 50, 50],
                    ['top-right', 50, 50],
                    ['bottom-left', 50, 50],
                    ['center', 0, 0]
                ];
                
                foreach ($positions as $position) {
                    $watermark = Image::make($watermarkPath);
                    
                    $targetWidth = max(60, (int) round($processed->width() * 0.2));
                    $watermark->resize($targetWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    
                    $watermark->opacity(20);
                    
                    $processed->insert($watermark, $position[0], $position[1], $position[2]);
                }
            }
        } catch (\Throwable $e) {
        }

        $processed->encode('webp', 90);

        $relativePath = "photos/{$yearMonth}/original/{$fileName}.webp";
        Storage::disk('public')->put($relativePath, $processed->stream());

        return $relativePath;
    }

    public static function deletePhoto(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        Storage::disk('public')->delete($relativePath);
    }
}
