<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class Blog extends Model
{
    protected $table = 'blogs';
    protected $fillable = ['title', 'subtitle', 'slug', 'content', 'image','image_left','user_id', 'category_id','views','create_by','is_featured'];
    
    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryBlog::class, 'category_id');
    }

    public function tags()
    {
        return $this->hasMany(BlogTag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function processAndSaveImage(UploadedFile $imageFile, string $type = 'main'): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("blogs/{$yearMonth}/{$type}");

        $processed = Image::make($imageFile);
        $processed->encode('webp', 85);

        $relativePath = "blogs/{$yearMonth}/{$type}/{$fileName}.webp";
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
