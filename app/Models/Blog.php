<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Laravel\Scout\Searchable;
use App\Helpers\VietnameseHelper;

class Blog extends Model
{
    use Searchable;
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

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $title = $this->title ?? '';
        $subtitle = $this->subtitle ?? '';
        $content = strip_tags($this->content ?? '');

        // Thêm version không dấu để search tốt hơn
        $titleNoAccent = VietnameseHelper::removeVietnameseAccents($title);
        $subtitleNoAccent = VietnameseHelper::removeVietnameseAccents($subtitle);
        $contentNoAccent = VietnameseHelper::removeVietnameseAccents($content);

        return [
            'id' => $this->id,
            'title' => $title,
            'title_no_accent' => $titleNoAccent, // Thêm field không dấu
            'subtitle' => $subtitle,
            'subtitle_no_accent' => $subtitleNoAccent, // Thêm field không dấu
            'slug' => $this->slug,
            'content' => $content,
            'content_no_accent' => $contentNoAccent, // Thêm field không dấu
            'category_id' => $this->category_id,
            'is_featured' => $this->is_featured,
            'views' => $this->views ?? 0,
            'created_at' => $this->created_at?->timestamp,
        ];
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey(): mixed
    {
        return $this->id;
    }

    /**
     * Get the key name used to index the model.
     *
     * @return mixed
     */
    public function getScoutKeyName(): mixed
    {
        return 'id';
    }
}
