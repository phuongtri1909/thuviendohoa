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

class Set extends Model
{
    use Searchable;
    protected $table = 'sets';
    protected $fillable = ['name', 'slug','type', 'description', 'image', 'drive_url', 'status', 'keywords','formats','size','price','is_featured','order','can_use_free_downloads','download_method'];

    const TYPE_FREE = 'free';
    const TYPE_PREMIUM = 'premium';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const DOWNLOAD_METHOD_BOTH = 'both';
    const DOWNLOAD_METHOD_COINS_ONLY = 'coins_only';
    const DOWNLOAD_METHOD_FREE_ONLY = 'free_only';

    public function getKeywordsAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        $decoded = json_decode($value, true);
        return $decoded !== null && json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    public function setKeywordsAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['keywords'] = null;
        } elseif (is_array($value)) {
            $this->attributes['keywords'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
                $this->attributes['keywords'] = json_encode($decoded, JSON_UNESCAPED_UNICODE);
            } else {
                $array = array_filter(array_map('trim', explode(',', $value)));
                $this->attributes['keywords'] = !empty($array) ? json_encode(array_values($array), JSON_UNESCAPED_UNICODE) : null;
            }
        } else {
            $this->attributes['keywords'] = null;
        }
    }

    public function getFormatsAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        $decoded = json_decode($value, true);
        return $decoded !== null && json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    public function setFormatsAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['formats'] = null;
        } elseif (is_array($value)) {
            $this->attributes['formats'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
                $this->attributes['formats'] = json_encode($decoded, JSON_UNESCAPED_UNICODE);
            } else {
                $array = array_filter(array_map('trim', explode(',', $value)));
                $this->attributes['formats'] = !empty($array) ? json_encode(array_values($array), JSON_UNESCAPED_UNICODE) : null;
            }
        } else {
            $this->attributes['formats'] = null;
        }
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    
    public function colors()
    {
        return $this->hasMany(ColorSet::class);
    }

    public function categories()
    {
        return $this->hasMany(CategorySet::class);
    }

    public function category()
    {
        return $this->hasOne(CategorySet::class)->with('category');
    }

    public function albums()
    {
        return $this->hasMany(AlbumSet::class);
    }

    public function albumSets()
    {
        return $this->hasMany(AlbumSet::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function tags()
    {
        return $this->hasMany(TagSet::class);
    }

    public function software()
    {
        return $this->hasMany(SoftwareSet::class);
    }

    public function purchases()
    {
        return $this->hasMany(PurchaseSet::class);
    }

    public function isPurchasedBy($userId)
    {
        return $this->purchases()->where('user_id', $userId)->exists();
    }

    public function isFree()
    {
        return $this->type === self::TYPE_FREE;
    }

    public function isPremium()
    {
        return $this->type === self::TYPE_PREMIUM;
    }

    public function canUseFreeDownloads()
    {
        if (!$this->isPremium()) {
            return false;
        }
        $method = $this->download_method ?? self::DOWNLOAD_METHOD_COINS_ONLY;
        return $method === self::DOWNLOAD_METHOD_BOTH || $method === self::DOWNLOAD_METHOD_FREE_ONLY;
    }

    public function canUseCoins()
    {
        if (!$this->isPremium()) {
            return false;
        }
        $method = $this->download_method ?? self::DOWNLOAD_METHOD_COINS_ONLY;
        return $method === self::DOWNLOAD_METHOD_BOTH || $method === self::DOWNLOAD_METHOD_COINS_ONLY;
    }

    public function requiresCoinPurchase()
    {
        if (!$this->isPremium()) {
            return false;
        }
        $method = $this->download_method ?? self::DOWNLOAD_METHOD_COINS_ONLY;
        return $method === self::DOWNLOAD_METHOD_COINS_ONLY;
    }

    public function requiresFreeDownload()
    {
        if (!$this->isPremium()) {
            return false;
        }
        $method = $this->download_method ?? self::DOWNLOAD_METHOD_COINS_ONLY;
        return $method === self::DOWNLOAD_METHOD_FREE_ONLY;
    }

    public static function processAndSaveImage(UploadedFile $imageFile): string
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("sets/{$yearMonth}/original");

        $processed = Image::make($imageFile);
        $processed->encode('webp', 90);

        $relativePath = "sets/{$yearMonth}/original/{$fileName}.webp";
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
        $categories = $this->categories()->with('category')->get()->pluck('category.id')->toArray();
        $albums = $this->albums()->with('album')->get()->pluck('album.id')->toArray();
        $tags = $this->tags()->with('tag')->get()->pluck('tag.name')->toArray();
        $colors = $this->colors()->with('color')->get()->pluck('color.value')->toArray();
        $softwareIds = $this->software()->with('software')->get()->pluck('software.id')->toArray();

        $name = $this->name ?? '';
        $description = $this->description ?? '';
        $keywords = is_array($this->keywords) ? implode(' ', $this->keywords) : ($this->keywords ?? '');

        // Thêm version không dấu để search tốt hơn
        $nameNoAccent = VietnameseHelper::removeVietnameseAccents($name);
        $descriptionNoAccent = VietnameseHelper::removeVietnameseAccents($description);
        $keywordsNoAccent = VietnameseHelper::removeVietnameseAccents($keywords);

        return [
            'id' => $this->id,
            'name' => $name,
            'name_no_accent' => $nameNoAccent, // Thêm field không dấu
            'slug' => $this->slug,
            'description' => $description,
            'description_no_accent' => $descriptionNoAccent, // Thêm field không dấu
            'keywords' => $keywords,
            'keywords_no_accent' => $keywordsNoAccent, // Thêm field không dấu
            'type' => $this->type,
            'status' => $this->status,
            'price' => $this->price ?? 0,
            'category_id' => $categories,
            'album_id' => $albums,
            'tags' => $tags,
            'colors' => $colors,
            'software_id' => $softwareIds,
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

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
