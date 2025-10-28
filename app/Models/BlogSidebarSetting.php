<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSidebarSetting extends Model
{
    protected $fillable = [
        'section_title',
        'category_id',
        'extra_link_title',
        'extra_link_url',
        'banner_images',
    ];

    protected $casts = [
        'banner_images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryBlog::class, 'category_id');
    }

    public function getSectionSubtitleAttribute()
    {
        return $this->category ? $this->category->name : 'CÂU CHUYỆN ĐỒ HỌA';
    }

    public function getFeaturedBlogs($limit = 3)
    {
        if (!$this->category_id) {
            return Blog::orderBy('created_at', 'desc')->take($limit)->get();
        }

        return Blog::where('category_id', $this->category_id)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public static function getInstance()
    {
        $setting = self::first();
        
        if (!$setting) {
            $setting = self::create([
                'section_title' => 'CẬP NHẬT XU HƯỚNG THIẾT KẾ',
                'category_id' => null,
            ]);
        }
        
        return $setting;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            self::query()->delete();
        });

        static::updating(function ($model) {
            self::where('id', '!=', $model->id)->delete();
        });
    }
}
