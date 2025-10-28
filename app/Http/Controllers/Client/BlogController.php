<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\CategoryBlog;
use App\Models\TagBlog;
use App\Models\BlogTag;
use App\Models\BlogSidebarSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class BlogController extends Controller
{
    public function index()
    {
        $featuredBlogs = Blog::where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $featuredCount = $featuredBlogs->count();
        if ($featuredCount < 5) {
            $additionalBlogs = Blog::whereNotIn('id', $featuredBlogs->pluck('id'))
                ->orderBy('created_at', 'desc')
                ->take(5 - $featuredCount)
                ->get();
            
            $featuredBlogs = $featuredBlogs->merge($additionalBlogs);
        }
        
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(8);
        $categories = CategoryBlog::orderBy('name')->get();
        
        $sidebarSetting = BlogSidebarSetting::getInstance();
        $sidebarBlogs = $sidebarSetting->getFeaturedBlogs(3);
        
        return view('client.pages.blog', compact('blogs', 'featuredBlogs', 'categories', 'sidebarSetting', 'sidebarBlogs'));
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->with(['category', 'tags.tag', 'user'])->firstOrFail();
        
        $blog->increment('views');
        
        $tableOfContents = extractTableOfContents($blog->content);
        $blogContent = addIdsToHeadings($blog->content);
        
        $relatedBlogs = Blog::where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
        
        $categories = CategoryBlog::orderBy('name')->get();
        $sidebarSetting = BlogSidebarSetting::getInstance();
        $sidebarBlogs = $sidebarSetting->getFeaturedBlogs(3);
        
        return view('client.pages.blog-item', compact('blog', 'blogContent', 'tableOfContents', 'relatedBlogs', 'categories', 'sidebarSetting', 'sidebarBlogs'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $blogs = Blog::when($query, function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('content', 'like', '%' . $query . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(8)
            ->withQueryString();
        
        if ($request->ajax()) {
            $html = '';
            foreach ($blogs as $blog) {
                $html .= view('components.blog.blog-item', compact('blog'))->render();
            }
            
            return response()->json([
                'html' => $html,
                'pagination' => $blogs->appends(['q' => $query])->links('components.paginate')->render(),
                'total' => $blogs->total(),
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage()
            ]);
        }
        
        return redirect()->route('blog');
    }
}
