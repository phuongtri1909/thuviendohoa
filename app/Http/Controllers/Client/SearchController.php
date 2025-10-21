<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\BannerHelper;
use App\Models\Set;
use App\Models\Category;
use App\Models\Album;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $bannerData = BannerHelper::getBannersDataForView('search');
        
        $query = $request->get('q');
        $categorySlug = $request->get('category');
        $albumSlug = $request->get('album');
        $tagSlug = $request->get('tag');
        $tags = $request->get('tags', []);
        $colors = $request->get('colors', []);
        $software = $request->get('software', []);
        
        // Build query for sets with optimized eager loading
        $setsQuery = Set::with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path')->take(1); // Only get first photo
                },
                'categories.category:id,name,slug',
                'albums.album:id,name,slug',
                'colors.color:id,value,name',
                'tags.tag:id,name,slug',
                'software.software:id,name'
            ])
            ->where('status', Set::STATUS_ACTIVE);
        
        // Apply search query
        if ($query) {
            $setsQuery->where(function(Builder $q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('keywords', 'like', "%{$query}%");
            });
        }
        
        // Apply category filter
        if ($categorySlug) {
            $setsQuery->whereHas('categories.category', function(Builder $q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        // Apply album filter
        if ($albumSlug) {
            $setsQuery->whereHas('albums.album', function(Builder $q) use ($albumSlug) {
                $q->where('slug', $albumSlug);
            });
        }
        
        // Apply tag filter
        if ($tagSlug) {
            $setsQuery->whereHas('tags.tag', function(Builder $q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }
        
        // Apply tags filter (multiple tags)
        if (!empty($tags)) {
            $setsQuery->whereHas('tags.tag', function(Builder $q) use ($tags) {
                $q->whereIn('slug', $tags);
            });
        }
        
        // Apply color filters
        if (!empty($colors)) {
            $setsQuery->whereHas('colors.color', function(Builder $q) use ($colors) {
                $q->whereIn('value', $colors);
            });
        }
        
        // Apply software filters
        if (!empty($software)) {
            $setsQuery->whereHas('software.software', function(Builder $q) use ($software) {
                $q->whereIn('id', $software);
            });
        }
        
        $sets = $setsQuery->orderBy('created_at', 'desc')->paginate(12);
        
        // Get selected category and album for display
        $selectedCategory = $categorySlug ? Category::where('slug', $categorySlug)->first() : null;
        $selectedAlbum = $albumSlug ? Album::where('slug', $albumSlug)->first() : null;
        
        // Get all categories for filter dropdown
        $categories = Category::orderBy('order', 'asc')->get();
        
        // Get all albums for filter dropdown
        $albums = Album::orderBy('created_at', 'desc')->get();
        
        // Get all colors for filter
        $allColors = \App\Models\Color::orderBy('name', 'asc')->get();
        
        // Get all software for filter
        $allSoftware = \App\Models\Software::orderBy('name', 'asc')->get();
        
        // Get related tags from filtered sets (optimized query)
        $relatedTags = \App\Models\Tag::whereHas('sets', function(Builder $q) use ($query, $categorySlug, $albumSlug, $tagSlug, $tags, $colors, $software) {
            $q->where('status', Set::STATUS_ACTIVE);
            
            // Apply same filters as sets query
            if ($query) {
                $q->where(function(Builder $subQ) use ($query) {
                    $subQ->where('name', 'like', "%{$query}%")
                         ->orWhere('description', 'like', "%{$query}%")
                         ->orWhere('keywords', 'like', "%{$query}%");
                });
            }
            
            if ($categorySlug) {
                $q->whereHas('categories.category', function(Builder $subQ) use ($categorySlug) {
                    $subQ->where('slug', $categorySlug);
                });
            }
            
            if ($albumSlug) {
                $q->whereHas('albums.album', function(Builder $subQ) use ($albumSlug) {
                    $subQ->where('slug', $albumSlug);
                });
            }
            
            if ($tagSlug) {
                $q->whereHas('tags.tag', function(Builder $subQ) use ($tagSlug) {
                    $subQ->where('slug', $tagSlug);
                });
            }
            
            if (!empty($tags)) {
                $q->whereHas('tags.tag', function(Builder $subQ) use ($tags) {
                    $subQ->whereIn('slug', $tags);
                });
            }
            
            if (!empty($colors)) {
                $q->whereHas('colors.color', function(Builder $subQ) use ($colors) {
                    $subQ->whereIn('value', $colors);
                });
            }
            
            if (!empty($software)) {
                $q->whereHas('software.software', function(Builder $subQ) use ($software) {
                    $subQ->whereIn('id', $software);
                });
            }
        })->orderBy('name', 'asc')->get(['id', 'name', 'slug']);
        
        return view('client.pages.search', array_merge($bannerData, compact(
            'query', 
            'sets', 
            'selectedCategory', 
            'selectedAlbum', 
            'categories', 
            'albums',
            'categorySlug',
            'albumSlug',
            'colors',
            'software',
            'allColors',
            'allSoftware',
            'relatedTags'
        )));
    }

    public function filter(Request $request)
    {
        $query = $request->get('q');
        $categorySlug = $request->get('category');
        $albumSlug = $request->get('album');
        $tagSlug = $request->get('tag');
        $tags = $request->get('tags', []);
        $colors = $request->get('colors', []);
        $software = $request->get('software', []);
        
        // Build query for sets with optimized eager loading
        $setsQuery = Set::with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path')->take(1); // Only get first photo
                },
                'categories.category:id,name,slug',
                'albums.album:id,name,slug',
                'colors.color:id,value,name',
                'tags.tag:id,name,slug',
                'software.software:id,name'
            ])
            ->where('status', Set::STATUS_ACTIVE);
        
        // Apply search query
        if ($query) {
            $setsQuery->where(function(Builder $q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('keywords', 'like', "%{$query}%");
            });
        }
        
        // Apply category filter
        if ($categorySlug) {
            $setsQuery->whereHas('categories.category', function(Builder $q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        // Apply album filter
        if ($albumSlug) {
            $setsQuery->whereHas('albums.album', function(Builder $q) use ($albumSlug) {
                $q->where('slug', $albumSlug);
            });
        }
        
        // Apply tag filter
        if ($tagSlug) {
            $setsQuery->whereHas('tags.tag', function(Builder $q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }
        
        // Apply tags filter (multiple tags)
        if (!empty($tags)) {
            $setsQuery->whereHas('tags.tag', function(Builder $q) use ($tags) {
                $q->whereIn('slug', $tags);
            });
        }
        
        // Apply color filters
        if (!empty($colors)) {
            $setsQuery->whereHas('colors.color', function(Builder $q) use ($colors) {
                $q->whereIn('value', $colors);
            });
        }
        
        // Apply software filters
        if (!empty($software)) {
            $setsQuery->whereHas('software.software', function(Builder $q) use ($software) {
                $q->whereIn('id', $software);
            });
        }
        
        $sets = $setsQuery->orderBy('created_at', 'desc')->paginate(12);
        
        return response()->json([
            'success' => true,
            'html' => view('components.client.search-results-ajax', compact('sets'))->render(),
            'count' => $sets->total()
        ]);
    }
}