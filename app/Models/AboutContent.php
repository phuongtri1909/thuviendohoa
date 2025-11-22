<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    protected $fillable = [
        'key',
        'title',
        'content',
    ];

    /**
     * Get content by key
     */
    public static function getByKey($key)
    {
        return self::where('key', $key)->first();
    }

    /**
     * Get or create content by key
     */
    public static function getOrCreateByKey($key, $defaultTitle = null, $defaultContent = '')
    {
        $content = self::where('key', $key)->first();
        
        if (!$content) {
            $content = self::create([
                'key' => $key,
                'title' => $defaultTitle,
                'content' => $defaultContent,
            ]);
        }
        
        return $content;
    }
}
