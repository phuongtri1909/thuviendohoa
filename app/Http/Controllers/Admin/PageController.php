<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        $pages = $query->orderBy('order', 'asc')->paginate(15)->withQueryString();

        return view('admin.pages.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'required',
            'status' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug đã tồn tại',
            'content.required' => 'Nội dung là bắt buộc',
            'status.required' => 'Trạng thái là bắt buộc',
            'order.required' => 'Thứ tự là bắt buộc',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        try {
            // Move temp images to permanent directory
            $content = $this->moveTempImages($request->content);

            Page::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'content' => $content,
                'status' => $request->status,
                'order' => $request->order,
            ]);

            // Clear temp images from session
            session()->forget('temp_page_images');

            return redirect()->route('admin.pages.index')->with('success', 'Tạo trang thành công');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show(Page $page)
    {
        return view('admin.pages.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'required',
            'status' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug đã tồn tại',
            'content.required' => 'Nội dung là bắt buộc',
            'status.required' => 'Trạng thái là bắt buộc',
            'order.required' => 'Thứ tự là bắt buộc',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        try {
            // Move temp images to permanent directory
            $content = $this->moveTempImages($request->content);

            $page->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'content' => $content,
                'status' => $request->status,
                'order' => $request->order,
            ]);

            // Clear temp images from session
            session()->forget('temp_page_images');

            return redirect()->route('admin.pages.index')->with('success', 'Cập nhật trang thành công');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Page $page)
    {
        try {
            // Delete content images
            $this->deleteContentImages($page->content);
            
            $page->delete();
            return redirect()->route('admin.pages.index')->with('success', 'Xóa trang thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Upload image for CKEditor
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            ]);

            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                
                // Save to temp directory with timestamp for cleanup
                $now = Carbon::now();
                $yearMonth = $now->format('Y/m');
                $timestamp = $now->format('YmdHis');
                $randomString = Str::random(8);
                $fileName = "temp_{$timestamp}_{$randomString}";

                Storage::disk('public')->makeDirectory("pages/{$yearMonth}/temp");

                // Process image with Intervention Image 2
                $image = InterventionImage::make($file);
                
                // Resize if too large (max width 1200px)
                if ($image->width() > 1200) {
                    $image->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Convert to webp with quality 80
                $image->encode('webp', 80);

                $relativePath = "pages/{$yearMonth}/temp/{$fileName}.webp";
                Storage::disk('public')->put($relativePath, $image->stream());

                // Track uploaded temp images in session
                $tempImages = session('temp_page_images', []);
                $tempImages[] = $relativePath;
                session(['temp_page_images' => $tempImages]);

                $url = asset('storage/' . $relativePath);

                // Return response for CKEditor
                return response()->json([
                    'uploaded' => 1,
                    'fileName' => basename($relativePath),
                    'url' => $url
                ]);
            }

            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'Không tìm thấy file upload'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'Lỗi khi upload ảnh: ' . $e->getMessage()
                ]
            ]);
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
     * Delete images from content when deleting page
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
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        }
    }

    /**
     * Cleanup temp images when user cancels
     */
    public function cleanupTempImages()
    {
        $tempImages = session('temp_page_images', []);
        
        foreach ($tempImages as $imagePath) {
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        
        session()->forget('temp_page_images');
        
        return response()->json(['success' => true]);
    }
}
