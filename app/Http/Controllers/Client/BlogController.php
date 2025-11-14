<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\CategoryBlog;
use App\Models\TagBlog;
use App\Models\BlogTag;
use App\Models\BlogSidebarSetting;
use App\Models\SeoSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class BlogController extends Controller
{
    public function index()
    {
        // SEO for blog index page
        $seoSetting = SeoSetting::getByPageKey('blog');
        
        if ($seoSetting) {
            SEOTools::setTitle($seoSetting->title);
            SEOTools::setDescription($seoSetting->description);
            SEOMeta::setKeywords($seoSetting->keywords);
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle($seoSetting->title);
            OpenGraph::setDescription($seoSetting->description);
            OpenGraph::setUrl(url()->current());
            OpenGraph::setSiteName(config('app.name'));
            OpenGraph::addProperty('type', 'website');
            OpenGraph::addProperty('locale', 'vi_VN');
            if ($seoSetting->thumbnail) {
                OpenGraph::addImage($seoSetting->thumbnail_url);
            }

            TwitterCard::setTitle($seoSetting->title);
            TwitterCard::setDescription($seoSetting->description);
            TwitterCard::setType('summary_large_image');
            if ($seoSetting->thumbnail) {
                TwitterCard::addImage($seoSetting->thumbnail_url);
            }
        }
        
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
        $categories = CategoryBlog::orderBy('order', 'asc')->get();
        
        $sidebarSetting = BlogSidebarSetting::getInstance();
        $sidebarBlogs = $sidebarSetting->getFeaturedBlogs(3);
        
        return view('client.pages.blog', compact('blogs', 'featuredBlogs', 'categories', 'sidebarSetting', 'sidebarBlogs'));
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->with(['category', 'tags.tag', 'user'])->firstOrFail();
        
        // SEO for blog post
        $baseSeo = SeoSetting::getByPageKey('blog');
        $seo = SeoSetting::getBlogSeo($blog, $baseSeo);
        
        $thumbnail = $blog->image ? asset('storage/' . $blog->image) : $seo->thumbnail;
        
        SEOTools::setTitle($seo->title);
        SEOTools::setDescription($seo->description);
        SEOMeta::setKeywords($seo->keywords);
        SEOTools::setCanonical(url()->current());

        OpenGraph::setTitle($seo->title);
        OpenGraph::setDescription($seo->description);
        OpenGraph::setUrl(url()->current());
        OpenGraph::setSiteName(config('app.name'));
        OpenGraph::addProperty('type', 'article');
        OpenGraph::addProperty('locale', 'vi_VN');
        if ($blog->created_at) {
            OpenGraph::addProperty('article:published_time', $blog->created_at->toIso8601String());
        }
        if ($blog->updated_at) {
            OpenGraph::addProperty('article:modified_time', $blog->updated_at->toIso8601String());
        }
        OpenGraph::addImage($thumbnail);

        TwitterCard::setTitle($seo->title);
        TwitterCard::setDescription($seo->description);
        TwitterCard::setType('summary_large_image');
        TwitterCard::addImage($thumbnail);
        
        $blog->increment('views');
        
        $tableOfContents = extractTableOfContents($blog->content);
        $blogContent = addIdsToHeadings($blog->content);
        
        $relatedBlogs = Blog::where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
        
        $categories = CategoryBlog::orderBy('order', 'asc')->get();
        $sidebarSetting = BlogSidebarSetting::getInstance();
        $sidebarBlogs = $sidebarSetting->getFeaturedBlogs(3);
        
        return view('client.pages.blog-item', compact('blog', 'blogContent', 'tableOfContents', 'relatedBlogs', 'categories', 'sidebarSetting', 'sidebarBlogs'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        // Sử dụng Scout nếu có query, nếu không dùng query builder
        if ($query) {
            $blogs = Blog::search($query)
                ->query(fn($builder) => $builder->orderBy('created_at', 'desc'))
                ->paginate(8)
                ->withQueryString();
        } else {
            $blogs = Blog::orderBy('created_at', 'desc')
                ->paginate(8)
                ->withQueryString();
        }
        
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
