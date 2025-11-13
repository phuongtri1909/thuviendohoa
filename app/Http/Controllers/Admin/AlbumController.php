<?php

namespace App\Http\Controllers\Admin;

use App\Models\Album;
use App\Models\AlbumType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Album::with(['featuredType', 'trendingType']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('featured')) {
            $query->featured();
        }

        if ($request->filled('trending')) {
            $query->trending();
        }

        $albums = $query->orderBy('order', 'asc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.pages.albums.index', compact('albums'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.albums.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:albums,name',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'icon' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'featured' => 'nullable|boolean',
            'trending' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên album là bắt buộc',
            'name.string' => 'Tên album phải là chuỗi',
            'name.max' => 'Tên album không được vượt quá 255 ký tự',
            'name.unique' => 'Tên album đã tồn tại',
            'image.required' => 'Ảnh album là bắt buộc',
            'image.file' => 'Ảnh album phải là file',
            'image.mimes' => 'Ảnh album chỉ chấp nhận jpeg, png, jpg, gif, webp, svg',
            'image.max' => 'Ảnh album không được vượt quá 10MB',
            'icon.file' => 'Icon album phải là file',
            'icon.mimes' => 'Icon album chỉ chấp nhận jpeg, png, jpg, gif, webp, svg',
            'icon.max' => 'Icon album không được vượt quá 2MB',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = Album::processAndSaveImage($request->file('image'));
        }

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = Album::processAndSaveImage($request->file('icon'));
        }

        $maxOrder = Album::max('order') ?? 0;
        
        $album = Album::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
            'icon' => $iconPath,
            'order' => $request->order ?? ($maxOrder + 1),
        ]);

        if ($request->boolean('featured')) {
            $album->markFeatured(0);
        } else {
            $album->unmarkFeatured();
        }

        if ($request->boolean('trending')) {
            $album->markTrending(0);
        } else {
            $album->unmarkTrending();
        }

        return redirect()->route('admin.albums.index')->with('success', 'Album đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album)
    {
        $album->load(['featuredType', 'trendingType']);
        return view('admin.pages.albums.show', compact('album'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album)
    {
        $album->load(['featuredType', 'trendingType']);
        return view('admin.pages.albums.edit', compact('album'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:albums,name,' . $album->id,
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'icon' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'featured' => 'nullable|boolean',
            'trending' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên album là bắt buộc',
            'name.string' => 'Tên album phải là chuỗi',
            'name.max' => 'Tên album không được vượt quá 255 ký tự',
            'name.unique' => 'Tên album đã tồn tại',
            'image.file' => 'Ảnh album phải là file',
            'image.mimes' => 'Ảnh album chỉ chấp nhận jpeg, png, jpg, gif, webp, svg',
            'image.max' => 'Ảnh album không được vượt quá 10MB',
            'icon.file' => 'Icon album phải là file',
            'icon.mimes' => 'Icon album chỉ chấp nhận jpeg, png, jpg, gif, webp, svg',
            'icon.max' => 'Icon album không được vượt quá 2MB',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'order' => $request->order ?? $album->order,
        ];

        if ($request->hasFile('image')) {
            Album::deleteImage($album->image);
            $data['image'] = Album::processAndSaveImage($request->file('image'));
        }

        if ($request->hasFile('icon')) {
            Album::deleteImage($album->icon);
            $data['icon'] = Album::processAndSaveImage($request->file('icon'));
        }

        $album->update($data);

        if ($request->boolean('featured')) {
            $album->markFeatured(0);
        } else {
            $album->unmarkFeatured();
        }

        if ($request->boolean('trending')) {
            $album->markTrending(0);
        } else {
            $album->unmarkTrending();
        }

        return redirect()->route('admin.albums.index')->with('success', 'Album đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        $album->albumTypes()->delete();
        $album->albumSets()->delete();
        
        Album::deleteImage($album->image);
        Album::deleteImage($album->icon);
        
        $album->delete();
        
        return redirect()->route('admin.albums.index')->with('success', 'Album đã được xóa thành công!');
    }
}
