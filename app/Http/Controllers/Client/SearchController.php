<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\BannerHelper;
use App\Models\Set;
use App\Models\Category;
use App\Models\Album;
use App\Models\Bookmark;
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
        
        $setsQuery = Set::select('id', 'name', 'image', 'created_at')
            ->with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path')->take(1);
                }
            ])
            ->where('status', Set::STATUS_ACTIVE);
        
        if ($query) {
            $setsQuery->where(function(Builder $q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('keywords', 'like', "%{$query}%");
            });
        }
        
        if ($categorySlug) {
            $setsQuery->whereHas('categories.category', function(Builder $q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        if ($albumSlug) {
            $setsQuery->whereHas('albums.album', function(Builder $q) use ($albumSlug) {
                $q->where('slug', $albumSlug);
            });
        }
        
        if ($tagSlug) {
            $setsQuery->whereHas('tags.tag', function(Builder $q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }
        
        if (!empty($tags)) {
            $setsQuery->whereHas('tags.tag', function(Builder $q) use ($tags) {
                $q->whereIn('slug', $tags);
            });
        }
        
        if (!empty($colors)) {
            $setsQuery->whereHas('colors.color', function(Builder $q) use ($colors) {
                $q->whereIn('value', $colors);
            });
        }
        
        if (!empty($software)) {
            $setsQuery->whereHas('software.software', function(Builder $q) use ($software) {
                $q->whereIn('id', $software);
            });
        }
        
        $sets = $setsQuery->orderBy('created_at', 'desc')->paginate(12);
        
        $selectedCategory = $categorySlug ? Category::where('slug', $categorySlug)->first() : null;
        $selectedAlbum = $albumSlug ? Album::where('slug', $albumSlug)->first() : null;
        
        $categories = Category::orderBy('order', 'asc')->get();
        
        $albums = Album::orderBy('created_at', 'desc')->get();
        
        $allColors = \App\Models\Color::orderBy('name', 'asc')->get();
        
        $allSoftware = \App\Models\Software::orderBy('name', 'asc')->get();
        
        $relatedTags = \App\Models\Tag::whereHas('sets', function(Builder $q) use ($query, $categorySlug, $albumSlug, $tagSlug, $tags, $colors, $software) {
            $q->where('status', Set::STATUS_ACTIVE);
            
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
        
        $setsQuery = Set::select('id', 'name', 'image', 'created_at')
            ->with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path')->take(1);
                }
            ])
            ->where('status', Set::STATUS_ACTIVE);
        
        if ($query) {
            $setsQuery->where(function(Builder $q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('keywords', 'like', "%{$query}%");
            });
        }
        
        if ($categorySlug) {
            $setsQuery->whereHas('categories.category', function(Builder $q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        if ($albumSlug) {
            $setsQuery->whereHas('albums.album', function(Builder $q) use ($albumSlug) {
                $q->where('slug', $albumSlug);
            });
        }
        
        if ($tagSlug) {
            $setsQuery->whereHas('tags.tag', function(Builder $q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }
        
        if (!empty($tags)) {
            $setsQuery->whereHas('tags.tag', function(Builder $q) use ($tags) {
                $q->whereIn('slug', $tags);
            });
        }
        
        if (!empty($colors)) {
            $setsQuery->whereHas('colors.color', function(Builder $q) use ($colors) {
                $q->whereIn('value', $colors);
            });
        }
        
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

    public function getSetDetailsBySlug(Request $request, $setSlug)
    {
        $userId = auth()->id();
        
        $set = Set::with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path');
                },
                'tags.tag:id,name,slug',
                'categories.category:id,name,slug',
                'albums.album:id,name,slug',
                'colors.color:id,value,name',
                'software.software:id,name',
                'bookmarks' => function($query) {
                    $query->select('id', 'set_id', 'user_id');
                }
            ])
                ->select('id', 'name', 'slug', 'description', 'formats', 'size', 'image', 'keywords', 'type', 'price')
            ->where('slug', $setSlug)
            ->where('status', Set::STATUS_ACTIVE)
            ->first();

        if (!$set) {
            return response()->json([
                'success' => false,
                'message' => 'Set not found'
            ], 404);
        }

        $isFavorited = false;
        if ($userId) {
            $isFavorited = Bookmark::where('user_id', $userId)
                ->where('set_id', $set->id)
                ->exists();
        }

        $set->isFavorited = $isFavorited;

        $relatedSets = Set::with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path')->take(1);
                }
            ])
            ->select('id', 'name', 'image', 'created_at')
            ->where('status', Set::STATUS_ACTIVE)
            ->where('id', '!=', $set->id)
            ->where(function($query) use ($set) {
                $categoryIds = $set->categories->pluck('category_id');
                if ($categoryIds->isNotEmpty()) {
                    $query->whereHas('categories', function($q) use ($categoryIds) {
                        $q->whereIn('category_id', $categoryIds);
                    });
                }
                
                $albumIds = $set->albums->pluck('album_id');
                if ($albumIds->isNotEmpty()) {
                    $query->orWhereHas('albums', function($q) use ($albumIds) {
                        $q->whereIn('album_id', $albumIds);
                    });
                }
            })
            ->limit(20)
            ->get();

        $featuredSets = Set::with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path')->take(1);
                }
            ])
            ->select('id', 'name', 'image', 'created_at')
            ->where('status', Set::STATUS_ACTIVE)
            ->where('is_featured', true)
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $set,
            'relatedSets' => $relatedSets,
            'featuredSets' => $featuredSets
        ]);
    }

    public function getSetDetails(Request $request, $setId)
    {
        $userId = auth()->id();
        
        $set = Set::with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path');
                },
                'tags.tag:id,name,slug',
                'categories.category:id,name,slug',
                'albums.album:id,name,slug',
                'colors.color:id,value,name',
                'software.software:id,name',
                'bookmarks' => function($query) {
                    $query->select('id', 'set_id', 'user_id');
                }
            ])
                ->select('id', 'name', 'slug', 'description', 'formats', 'size', 'image', 'keywords', 'type', 'price')
            ->where('id', $setId)
            ->where('status', Set::STATUS_ACTIVE)
            ->first();

        if (!$set) {
            return response()->json([
                'success' => false,
                'message' => 'Set not found'
            ], 404);
        }

        $isFavorited = false;
        if ($userId) {
            $isFavorited = Bookmark::where('user_id', $userId)
                ->where('set_id', $set->id)
                ->exists();
        }

        $set->isFavorited = $isFavorited;

        $relatedSets = Set::with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path')->take(1);
                }
            ])
            ->select('id', 'name', 'image', 'created_at')
            ->where('status', Set::STATUS_ACTIVE)
            ->where('id', '!=', $set->id)
            ->where(function($query) use ($set) {
                $categoryIds = $set->categories->pluck('category_id');
                if ($categoryIds->isNotEmpty()) {
                    $query->whereHas('categories', function($q) use ($categoryIds) {
                        $q->whereIn('category_id', $categoryIds);
                    });
                }
                
                $albumIds = $set->albums->pluck('album_id');
                if ($albumIds->isNotEmpty()) {
                    $query->orWhereHas('albums', function($q) use ($albumIds) {
                        $q->whereIn('album_id', $albumIds);
                    });
                }
            })
            ->limit(20)
            ->get();

        $featuredSets = Set::with([
                'photos' => function($query) {
                    $query->select('id', 'set_id', 'path')->take(1);
                }
            ])
            ->select('id', 'name', 'image', 'created_at')
            ->where('status', Set::STATUS_ACTIVE)
            ->where('is_featured', true)
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $set,
            'relatedSets' => $relatedSets,
            'featuredSets' => $featuredSets
        ]);
    }

}