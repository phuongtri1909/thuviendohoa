<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class FooterSetting extends Model
{
    protected $fillable = [
        'facebook_url',
        'support_hotline',
        'support_email',
        'support_fanpage',
        'support_fanpage_url',
        'partners'
    ];

    protected $casts = [
        'partners' => 'array',
    ];

    /**
     * Process and save partner image
     */
    public static function processAndSavePartnerImage(UploadedFile $imageFile): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = \Illuminate\Support\Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("footer/partners/{$yearMonth}");

        $processed = Image::make($imageFile);
        $processed->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $processed->encode('webp', 90);

        $relativePath = "footer/partners/{$yearMonth}/{$fileName}.webp";
        Storage::disk('public')->put($relativePath, $processed->stream());

        return $relativePath;
    }

    /**
     * Delete partner image
     */
    public static function deletePartnerImage(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        Storage::disk('public')->delete($relativePath);
    }
}
