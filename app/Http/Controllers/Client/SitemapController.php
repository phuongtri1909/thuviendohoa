<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Page;
use App\Models\Set;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Exception;

class SitemapController extends Controller
{
    /**
     * Main sitemap index
     */
    public function index()
    {
        try {
            if (!View::exists('client.sitemap.index')) {
                Log::error('Sitemap index view does not exist');
                return response()->json(['error' => 'Sitemap template not found'], 500);
            }
            
            return response()->view('client.sitemap.index')->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap index: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Main pages sitemap (home, search, albums, blog, get-link)
     */
    public function mainPages()
    {
        try {
            return response()->view('client.sitemap.main_pages', [])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in main pages sitemap: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Categories sitemap
     */
    public function categories()
    {
        try {
            $categories = Category::all();
            
            return response()->view('client.sitemap.categories', [
                'categories' => $categories,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap categories: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Albums sitemap
     */
    public function albums()
    {
        try {
            $albums = Album::all();
            
            return response()->view('client.sitemap.albums', [
                'albums' => $albums,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap albums: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sets sitemap (paginated for better performance)
     */
    public function sets()
    {
        try {
            $page = request()->get('page', 1);
            $perPage = 5000; // Each sitemap should contain max ~50,000 URLs
            
            $sets = Set::where('status', Set::STATUS_ACTIVE)
                ->orderBy('id', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return response()->view('client.sitemap.sets', [
                'sets' => $sets,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap sets: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sets sitemap index (for pagination)
     */
    public function setsIndex()
    {
        try {
            $totalSets = Set::where('status', Set::STATUS_ACTIVE)->count();
            $perPage = 5000;
            $totalPages = ceil($totalSets / $perPage);
            
            return response()->view('client.sitemap.sets_index', [
                'totalPages' => $totalPages,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sets sitemap index: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Blogs sitemap
     */
    public function blogs()
    {
        try {
            // Blog table doesn't have status column, get all blogs
            $blogs = Blog::orderBy('created_at', 'desc')->get();
            
            return response()->view('client.sitemap.blogs', [
                'blogs' => $blogs,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap blogs: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Pages sitemap (custom pages)
     */
    public function pages()
    {
        try {
            // Get active pages (status = 1)
            $pages = Page::where('status', 1)->get();
            
            return response()->view('client.sitemap.pages', [
                'pages' => $pages,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap pages: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }
}
