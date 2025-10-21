<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name', 'slug', 'image', 'order'];

    public function categorySets()
    {
        return $this->hasMany(CategorySet::class);
    }

    public function sets()
    {
        return $this->belongsToMany(Set::class, 'category_sets');
    }

    public static function processAndSaveImage(UploadedFile $imageFile): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("categories/{$yearMonth}/original");

        $processed = Image::make($imageFile);
        $processed->encode('webp', 90);

        $relativePath = "categories/{$yearMonth}/original/{$fileName}.webp";
        Storage::disk('public')->put($relativePath, $processed->stream());

        return $relativePath;
    }

    /**
     * Delete a previously saved image path from public storage.
     */
    public static function deleteImage(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        Storage::disk('public')->delete($relativePath);
    }
}
