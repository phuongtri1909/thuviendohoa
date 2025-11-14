<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Set;
use App\Models\Blog;
use App\Helpers\VietnameseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AutocompleteController extends Controller
{
    /**
     * Autocomplete suggestions cho search
     */
    public function suggest(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min((int) $request->get('limit', 10), 20);
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'suggestions' => []
            ]);
        }

        // Cache key
        $cacheKey = 'autocomplete_' . md5($query . '_' . $limit);
        
        return Cache::remember($cacheKey, 300, function () use ($query, $limit) {
            $suggestions = [];
            
            // Normalize query
            $normalizedQuery = VietnameseHelper::normalizeQuery($query);
            
            // Search Sets
            $sets = Set::search($query)
                ->take($limit)
                ->get()
                ->map(function ($set) {
                    return [
                        'type' => 'set',
                        'id' => $set->id,
                        'title' => $set->name,
                        'slug' => $set->slug,
                        'image' => $set->image ? asset('storage/' . $set->image) : null,
                    ];
                });
            
            // Search Blogs
            $blogs = Blog::search($query)
                ->take($limit)
                ->get()
                ->map(function ($blog) {
                    return [
                        'type' => 'blog',
                        'id' => $blog->id,
                        'title' => $blog->title,
                        'slug' => $blog->slug,
                        'image' => $blog->image ? asset('storage/' . $blog->image) : null,
                    ];
                });
            
            // Combine và limit
            $allResults = $sets->merge($blogs)->take($limit);
            
            return response()->json([
                'success' => true,
                'suggestions' => $allResults->values()->all(),
                'count' => $allResults->count()
            ]);
        });
    }

    /**
     * Popular search terms
     */
    public function popular(Request $request)
    {
        $limit = min((int) $request->get('limit', 10), 20);
        
        // Có thể lấy từ database hoặc cache
        $popularTerms = Cache::remember('popular_search_terms', 3600, function () use ($limit) {
            // Lấy từ Sets và Blogs phổ biến
            $popularSets = Set::where('status', Set::STATUS_ACTIVE)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->pluck('name')
                ->map(function ($name) {
                    $words = explode(' ', $name);
                    return $words[0] ?? $name;
                })
                ->unique()
                ->take($limit)
                ->values()
                ->all();
            
            return $popularSets;
        });
        
        return response()->json([
            'success' => true,
            'terms' => array_slice($popularTerms, 0, $limit)
        ]);
    }
}

