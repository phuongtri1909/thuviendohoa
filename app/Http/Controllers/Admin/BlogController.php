<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\CategoryBlog;
use App\Models\TagBlog;
use App\Models\BlogTag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['category', 'user']);

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $blogs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $categories = CategoryBlog::orderBy('name')->get();

        return view('admin.pages.blogs.index', compact('blogs', 'categories'));
    }

    public function create()
    {
        $categories = CategoryBlog::orderBy('name')->get();
        $tags = TagBlog::orderBy('name')->get();
        return view('admin.pages.blogs.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:blogs,title',
            'content' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'image_left' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'category_id' => 'required|exists:category_blogs,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tag_blogs,id',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.unique' => 'Tiêu đề đã tồn tại',
            'content.required' => 'Nội dung là bắt buộc',
            'image.required' => 'Ảnh đại diện là bắt buộc',
            'image.max' => 'Ảnh không được vượt quá 10MB',
            'category_id.required' => 'Danh mục là bắt buộc',
        ]);

        $mainImagePath = Blog::processAndSaveImage($request->file('image'), 'main');
        
        $leftImagePath = null;
        if ($request->hasFile('image_left')) {
            $leftImagePath = Blog::processAndSaveImage($request->file('image_left'), 'left');
        }

        // Move temp images to permanent directory
        $content = $this->moveTempImages($request->content);

        $blog = Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $content,
            'image' => $mainImagePath,
            'image_left' => $leftImagePath,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
            'create_by' => Auth::user()->name,
            'views' => 0,
            'is_featured' => $request->has('is_featured') ? 1 : 0,
        ]);

        // Save tags
        if ($request->filled('tag_ids')) {
            foreach ($request->tag_ids as $tagId) {
                BlogTag::create([
                    'blog_id' => $blog->id,
                    'tag_id' => $tagId,
                ]);
            }
        }

        // Clear temp images from session
        session()->forget('temp_blog_images');

        return redirect()->route('admin.blogs.index')->with('success', 'Blog đã được tạo thành công!');
    }

    public function show(Blog $blog)
    {
        $blog->load(['category', 'tags.tag', 'user']);
        return view('admin.pages.blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $categories = CategoryBlog::orderBy('name')->get();
        $tags = TagBlog::orderBy('name')->get();
        $blog->load('tags');
        $selectedTags = $blog->tags->pluck('tag_id')->toArray();
        return view('admin.pages.blogs.edit', compact('blog', 'categories', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:blogs,title,' . $blog->id,
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'image_left' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'category_id' => 'required|exists:category_blogs,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tag_blogs,id',
        ]);

        // Move temp images to permanent directory
        $content = $this->moveTempImages($request->content);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $content,
            'category_id' => $request->category_id,
            'is_featured' => $request->has('is_featured') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            Blog::deleteImage($blog->image);
            $data['image'] = Blog::processAndSaveImage($request->file('image'), 'main');
        }

        if ($request->hasFile('image_left')) {
            Blog::deleteImage($blog->image_left);
            $data['image_left'] = Blog::processAndSaveImage($request->file('image_left'), 'left');
        }

        $blog->update($data);

        // Update tags
        BlogTag::where('blog_id', $blog->id)->delete();
        if ($request->filled('tag_ids')) {
            foreach ($request->tag_ids as $tagId) {
                BlogTag::create([
                    'blog_id' => $blog->id,
                    'tag_id' => $tagId,
                ]);
            }
        }

        // Clear temp images from session
        session()->forget('temp_blog_images');

        return redirect()->route('admin.blogs.index')->with('success', 'Blog đã được cập nhật thành công!');
    }

    public function destroy(Blog $blog)
    {
        // Delete main image
        Blog::deleteImage($blog->image);
        
        // Delete left image
        Blog::deleteImage($blog->image_left);
        
        // Delete content images
        $this->deleteContentImages($blog->content);
        
        // Delete tags
        BlogTag::where('blog_id', $blog->id)->delete();
        
        // Delete blog
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog đã được xóa thành công!');
    }

    /**
     * Upload image from CKEditor
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            ]);

            $imageFile = $request->file('upload');
            
            if (!$imageFile) {
                return response()->json([
                    'uploaded' => 0,
                    'error' => [
                        'message' => 'Không tìm thấy file upload.'
                    ]
                ], 400);
            }

            // Save to temp directory with timestamp for cleanup
            $now = Carbon::now();
            $yearMonth = $now->format('Y/m');
            $timestamp = $now->format('YmdHis');
            $randomString = Str::random(8);
            $fileName = "temp_{$timestamp}_{$randomString}";

            Storage::disk('public')->makeDirectory("blogs/{$yearMonth}/temp");

            $processed = Image::make($imageFile);
            $processed->encode('webp', 80);

            $relativePath = "blogs/{$yearMonth}/temp/{$fileName}.webp";
            Storage::disk('public')->put($relativePath, $processed->stream());

            // Track uploaded temp images in session
            $tempImages = session('temp_blog_images', []);
            $tempImages[] = $relativePath;
            session(['temp_blog_images' => $tempImages]);

            $url = asset('storage/' . $relativePath);

            return response()->json([
                'uploaded' => 1,
                'fileName' => basename($relativePath),
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'Lỗi khi tải ảnh lên: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Move temp images to permanent directory
     */
    private function moveTempImages($content)
    {
        // Find all temp images in content
        preg_match_all('/<img[^>]+src="([^">]+)"/', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $imageUrl) {
                if (strpos($imageUrl, '/temp/temp_') !== false) {
                    // Extract relative path
                    $tempPath = str_replace(asset('storage/'), '', $imageUrl);
                    
                    if (Storage::disk('public')->exists($tempPath)) {
                        // Create new permanent filename
                        $pathInfo = pathinfo($tempPath);
                        $dirname = str_replace('/temp', '/content', $pathInfo['dirname']);
                        $filename = str_replace('temp_', '', $pathInfo['filename']);
                        $newPath = $dirname . '/' . $filename . '.' . $pathInfo['extension'];
                        
                        // Ensure directory exists
                        Storage::disk('public')->makeDirectory($dirname);
                        
                        // Move file
                        Storage::disk('public')->move($tempPath, $newPath);
                        
                        // Update content
                        $content = str_replace($imageUrl, asset('storage/' . $newPath), $content);
                    }
                }
            }
        }
        
        return $content;
    }

    /**
     * Delete images from content when deleting blog
     */
    private function deleteContentImages($content)
    {
        // Extract image URLs from content
        preg_match_all('/<img[^>]+src="([^">]+)"/', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $imageUrl) {
                // Extract path from URL
                if (strpos($imageUrl, '/storage/') !== false) {
                    $path = str_replace('/storage/', '', parse_url($imageUrl, PHP_URL_PATH));
                    Blog::deleteImage($path);
                }
            }
        }
    }

    /**
     * Cleanup temp images when user cancels
     */
    public function cleanupTempImages()
    {
        $tempImages = session('temp_blog_images', []);
        
        foreach ($tempImages as $imagePath) {
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        
        session()->forget('temp_blog_images');
        
        return response()->json(['success' => true]);
    }
}
