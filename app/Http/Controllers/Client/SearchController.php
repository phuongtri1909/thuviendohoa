<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\BannerHelper;
use App\Helpers\VietnameseHelper;
use App\Models\Set;
use App\Models\Category;
use App\Models\Album;
use App\Models\Bookmark;
use App\Models\SeoSetting;
use Illuminate\Database\Eloquent\Builder;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

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
        $type = $request->get('type');
        
        // SEO for search page
        $seoSetting = SeoSetting::getByPageKey('search');
        $thumbnail = $seoSetting && $seoSetting->thumbnail ? $seoSetting->thumbnail_url : asset('images/d/Thumbnail.png');
        
        if ($query) {
            $title = "Tìm kiếm: {$query} - " . config('app.name');
            $description = "Kết quả tìm kiếm cho từ khóa '{$query}'. Khám phá hàng ngàn mẫu thiết kế phù hợp.";
            $keywords = "tim kiem, {$query}, mockup, template, vector";
        } elseif ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            $title = "{$category->name} - " . config('app.name');
            $description = "Khám phá các mẫu thiết kế {$category->name}. Tải về miễn phí và premium.";
            $keywords = "{$category->name}, mockup, template, vector";
            // Use category image if available
            if ($category && $category->image) {
                $thumbnail = asset('storage/' . $category->image);
            }
        } elseif ($albumSlug) {
            $album = Album::where('slug', $albumSlug)->first();
            $title = "{$album->name} - " . config('app.name');
            $description = "Bộ sưu tập {$album->name}. Các mẫu thiết kế được tuyển chọn.";
            $keywords = "{$album->name}, album, collection, mockup, template";
            // Use album image if available
            if ($album && $album->image) {
                $thumbnail = asset('storage/' . $album->image);
            }
        } elseif ($seoSetting) {
            $title = $seoSetting->title;
            $description = $seoSetting->description;
            $keywords = $seoSetting->keywords;
        } else {
            $title = 'Tìm kiếm - ' . config('app.name');
            $description = 'Tìm kiếm mẫu thiết kế đồ họa';
            $keywords = 'tim kiem, search, mockup, template';
        }
        
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords($keywords);
        SEOTools::setCanonical(url()->current());

        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl(url()->current());
        OpenGraph::setSiteName(config('app.name'));
        OpenGraph::addProperty('type', 'website');
        OpenGraph::addProperty('locale', 'vi_VN');
        OpenGraph::addImage($thumbnail);

        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);
        TwitterCard::setType('summary_large_image');
        TwitterCard::addImage($thumbnail);
        
        if ($query) {
            $searchQuery = trim($query);
            $queryLength = mb_strlen($searchQuery);
            
            $searchTerms = VietnameseHelper::getSearchTerms($searchQuery);
            $fuzzyPatterns = [];
            if ($queryLength >= 3) {
                $fuzzyPatterns = VietnameseHelper::createFuzzyPatterns($searchQuery);
            }
            $allPatterns = array_unique(array_merge($searchTerms, $fuzzyPatterns));
            
            $filteredPatterns = array_filter($allPatterns, function($pattern) use ($searchQuery) {
                if ($pattern === $searchQuery || in_array($pattern, VietnameseHelper::getSearchTerms($searchQuery))) {
                    return true;
                }
                return mb_strlen($pattern) >= 3 
                    && VietnameseHelper::isRelevantPattern($pattern, $searchQuery);
            });
            
            $setsQuery = Set::select('id', 'name', 'image', 'created_at', 'type', 'price')
                ->with([
                    'photos' => function($q) {
                        $q->select('id', 'set_id', 'path')->take(1);
                    },
                    'software.software:id,name,logo,logo_hover,logo_active'
                ])
                ->where('status', Set::STATUS_ACTIVE)
                ->where(function(Builder $q) use ($searchQuery, $filteredPatterns, $queryLength) {
                    $q->where('name', 'LIKE', $searchQuery)
                      ->orWhere(function(Builder $subQ) use ($searchQuery, $queryLength) {
                          if ($queryLength >= 3) {
                              $subQ->where('name', 'LIKE', $searchQuery . '%');
                          }
                      })
                      ->orWhere(function(Builder $subQ) use ($searchQuery, $queryLength) {
                          if ($queryLength >= 3) {
                              $subQ->where('name', 'LIKE', '%' . $searchQuery . '%');
                          }
                      })
                      ->orWhere(function(Builder $subQ) use ($searchQuery, $queryLength) {
                          if ($queryLength >= 3) {
                              $subQ->where('keywords', 'LIKE', '%' . $searchQuery . '%');
                          }
                      })
                      ->orWhere(function(Builder $subQ) use ($searchQuery, $queryLength) {
                          if ($queryLength >= 3) {
                              $subQ->whereHas('tags.tag', function(Builder $tagQ) use ($searchQuery) {
                                  $tagQ->where('name', 'LIKE', $searchQuery)
                                       ->orWhere('name', 'LIKE', $searchQuery . '%')
                                       ->orWhere('name', 'LIKE', '%' . $searchQuery . '%');
                              });
                          }
                      });
                    
                    if (!empty($filteredPatterns) && $queryLength >= 3) {
                        $q->orWhere(function(Builder $subQ) use ($filteredPatterns) {
                            foreach ($filteredPatterns as $pattern) {
                                if (mb_strlen($pattern) >= 3) {
                                    $subQ->orWhere('name', 'LIKE', '%' . $pattern . '%')
                                         ->orWhere('keywords', 'LIKE', '%' . $pattern . '%');
                                }
                            }
                        })
                        ->orWhereHas('tags.tag', function(Builder $tagQ) use ($filteredPatterns) {
                            foreach ($filteredPatterns as $pattern) {
                                if (mb_strlen($pattern) >= 3) {
                                    $tagQ->orWhere('name', 'LIKE', '%' . $pattern . '%');
                                }
                            }
                        });
                    }
                });
            
            // Apply filters
            if ($categorySlug) {
                $setsQuery->whereHas('categories.category', fn($q) => $q->where('slug', $categorySlug));
            }
            
            if ($albumSlug) {
                $setsQuery->whereHas('albums.album', fn($q) => $q->where('slug', $albumSlug));
            }
            
            if ($type && in_array($type, [Set::TYPE_PREMIUM, Set::TYPE_FREE])) {
                $setsQuery->where('type', $type);
            }
            
            if ($tagSlug) {
                $setsQuery->whereHas('tags.tag', fn($q) => $q->where('slug', $tagSlug));
            }
            
            if (!empty($tags)) {
                $setsQuery->whereHas('tags.tag', fn($q) => $q->whereIn('slug', $tags));
            }
            
            if (!empty($colors)) {
                $setsQuery->whereHas('colors.color', fn($q) => $q->whereIn('value', $colors));
            }
            
            if (!empty($software)) {
                $setsQuery->whereHas('software.software', fn($q) => $q->whereIn('id', $software));
            }
            
            // Order by relevance: exact match -> starts with -> contains -> tags -> others
            $exactMatch = $searchQuery;
            $startsWith = $searchQuery . '%';
            $contains = '%' . $searchQuery . '%';
            $setsQuery->orderByRaw("
                CASE 
                    WHEN name = ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN name LIKE ? THEN 3
                    WHEN keywords LIKE ? THEN 4
                    WHEN EXISTS(SELECT 1 FROM tag_sets ts INNER JOIN tags t ON ts.tag_id = t.id WHERE ts.set_id = sets.id AND t.name LIKE ?) THEN 5
                    ELSE 6
                END ASC
            ", [$exactMatch, $startsWith, $contains, $contains, $contains])
            ->orderBy('created_at', 'desc');
            
            $sets = $setsQuery->paginate(30);
        } else {
            // Không có text search, dùng query builder thông thường
            $setsQuery = Set::select('id', 'name', 'image', 'created_at', 'type', 'price')
                ->with([
                    'photos' => function($q) {
                        $q->select('id', 'set_id', 'path')->take(1);
                    },
                    'software.software:id,name,logo,logo_hover,logo_active'
                ])
                ->where('status', Set::STATUS_ACTIVE);
            
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
            
            if ($type && in_array($type, [Set::TYPE_PREMIUM, Set::TYPE_FREE])) {
                $setsQuery->where('type', $type);
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
            
            $sets = $setsQuery->orderBy('created_at', 'desc')->paginate(30);
        }
        
        $selectedCategory = $categorySlug ? Category::where('slug', $categorySlug)->first() : null;
        $selectedAlbum = $albumSlug ? Album::where('slug', $albumSlug)->first() : null;
        
        $categories = Category::orderBy('order', 'asc')->get();
        
        $albums = Album::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
        
        $allColors = \App\Models\Color::orderBy('order', 'asc')->orderBy('name', 'asc')->get();
        
        $allSoftware = \App\Models\Software::orderBy('order', 'asc')->orderBy('name', 'asc')->get();
        
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
        })->orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id', 'name', 'slug']);
        
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
            'type',
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
        $type = $request->get('type');
        $page = $request->get('page', 1);
        
        if ($query) {
            $searchQuery = trim($query);
            $queryLength = mb_strlen($searchQuery);
            
            // Chỉ tạo fuzzy patterns nếu query có độ dài >= 3
            $searchTerms = VietnameseHelper::getSearchTerms($searchQuery);
            $fuzzyPatterns = [];
            if ($queryLength >= 3) {
                $fuzzyPatterns = VietnameseHelper::createFuzzyPatterns($searchQuery);
            }
            $allPatterns = array_unique(array_merge($searchTerms, $fuzzyPatterns));
            
            // Lọc patterns: chỉ giữ những pattern có độ dài >= 3 và khác query gốc
            $filteredPatterns = array_filter($allPatterns, function($pattern) use ($searchQuery) {
                return mb_strlen($pattern) >= 3 && $pattern !== $searchQuery;
            });
            
            // Tạo query builder với search tối ưu
            $setsQuery = Set::select('id', 'name', 'image', 'created_at', 'type', 'price')
                ->with([
                    'photos' => function($q) {
                        $q->select('id', 'set_id', 'path')->take(1);
                    },
                    'software.software:id,name,logo,logo_hover,logo_active',
                    'bookmarks' => function($q) {
                        $q->select('id', 'set_id', 'user_id');
                    }
                ])
                ->where('status', Set::STATUS_ACTIVE)
                ->where(function(Builder $q) use ($searchQuery, $filteredPatterns, $queryLength) {
                    // Ưu tiên 1: Exact match trong name
                    $q->where('name', 'LIKE', $searchQuery)
                      // Ưu tiên 2: Starts with trong name (chỉ khi query >= 3 ký tự)
                      ->orWhere(function(Builder $subQ) use ($searchQuery, $queryLength) {
                          if ($queryLength >= 3) {
                              $subQ->where('name', 'LIKE', $searchQuery . '%');
                          }
                      })
                      // Ưu tiên 3: Contains trong name (chỉ khi query >= 3 ký tự)
                      ->orWhere(function(Builder $subQ) use ($searchQuery, $queryLength) {
                          if ($queryLength >= 3) {
                              $subQ->where('name', 'LIKE', '%' . $searchQuery . '%');
                          }
                      })
                      // Ưu tiên 4: Tìm trong keywords (chỉ khi query >= 3 ký tự)
                      ->orWhere(function(Builder $subQ) use ($searchQuery, $queryLength) {
                          if ($queryLength >= 3) {
                              $subQ->where('keywords', 'LIKE', '%' . $searchQuery . '%');
                          }
                      })
                      // Ưu tiên 5: Tìm trong tags (chỉ khi query >= 3 ký tự)
                      ->orWhere(function(Builder $subQ) use ($searchQuery, $queryLength) {
                          if ($queryLength >= 3) {
                              $subQ->whereHas('tags.tag', function(Builder $tagQ) use ($searchQuery) {
                                  $tagQ->where('name', 'LIKE', $searchQuery)
                                       ->orWhere('name', 'LIKE', $searchQuery . '%')
                                       ->orWhere('name', 'LIKE', '%' . $searchQuery . '%');
                              });
                          }
                      });
                    
                    // Chỉ thêm fuzzy patterns nếu có và query đủ dài
                    if (!empty($filteredPatterns) && $queryLength >= 3) {
                        // Ưu tiên 6: Fuzzy patterns trong name/keywords (chỉ patterns dài >= 3)
                        $q->orWhere(function(Builder $subQ) use ($filteredPatterns) {
                            foreach ($filteredPatterns as $pattern) {
                                if (mb_strlen($pattern) >= 3) {
                                    $subQ->orWhere('name', 'LIKE', '%' . $pattern . '%')
                                         ->orWhere('keywords', 'LIKE', '%' . $pattern . '%');
                                }
                            }
                        })
                        // Ưu tiên 7: Fuzzy patterns trong tags
                        ->orWhereHas('tags.tag', function(Builder $tagQ) use ($filteredPatterns) {
                            foreach ($filteredPatterns as $pattern) {
                                if (mb_strlen($pattern) >= 3) {
                                    $tagQ->orWhere('name', 'LIKE', '%' . $pattern . '%');
                                }
                            }
                        });
                    }
                });
            
            // Apply filters
            if ($categorySlug) {
                $setsQuery->whereHas('categories.category', fn($q) => $q->where('slug', $categorySlug));
            }
            
            if ($albumSlug) {
                $setsQuery->whereHas('albums.album', fn($q) => $q->where('slug', $albumSlug));
            }
            
            if ($type && in_array($type, [Set::TYPE_PREMIUM, Set::TYPE_FREE])) {
                $setsQuery->where('type', $type);
            }
            
            if ($tagSlug) {
                $setsQuery->whereHas('tags.tag', fn($q) => $q->where('slug', $tagSlug));
            }
            
            if (!empty($tags)) {
                $setsQuery->whereHas('tags.tag', fn($q) => $q->whereIn('slug', $tags));
            }
            
            if (!empty($colors)) {
                $setsQuery->whereHas('colors.color', fn($q) => $q->whereIn('value', $colors));
            }
            
            if (!empty($software)) {
                $setsQuery->whereHas('software.software', fn($q) => $q->whereIn('id', $software));
            }
            
            // Order by relevance: exact match -> starts with -> contains -> tags -> others
            $exactMatch = $searchQuery;
            $startsWith = $searchQuery . '%';
            $contains = '%' . $searchQuery . '%';
            $setsQuery->orderByRaw("
                CASE 
                    WHEN name = ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN name LIKE ? THEN 3
                    WHEN keywords LIKE ? THEN 4
                    WHEN EXISTS(SELECT 1 FROM tag_sets ts INNER JOIN tags t ON ts.tag_id = t.id WHERE ts.set_id = sets.id AND t.name LIKE ?) THEN 5
                    ELSE 6
                END ASC
            ", [$exactMatch, $startsWith, $contains, $contains, $contains])
            ->orderBy('created_at', 'desc');
        } else {
            $setsQuery = Set::select('id', 'name', 'image', 'created_at', 'type', 'price')
                ->with([
                    'photos' => function($query) {
                        $query->select('id', 'set_id', 'path')->take(1);
                    },
                    'software.software:id,name,logo,logo_hover,logo_active',
                    'bookmarks' => function($query) {
                        $query->select('id', 'set_id', 'user_id');
                    }
                ])
                ->where('status', Set::STATUS_ACTIVE);
            
            // Apply filters
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
            
            if ($type && in_array($type, [Set::TYPE_PREMIUM, Set::TYPE_FREE])) {
                $setsQuery->where('type', $type);
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
            
            $setsQuery->orderBy('created_at', 'desc');
        }
        
        $sets = $setsQuery->paginate(30);
        
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
                ->select('id', 'name', 'slug', 'description', 'formats', 'size', 'image', 'keywords', 'type', 'price', 'download_method')
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
                ->select('id', 'name', 'slug', 'description', 'formats', 'size', 'image', 'keywords', 'type', 'price', 'download_method')
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