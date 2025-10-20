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

        $albums = $query->paginate(15)->withQueryString();

        return view('Admin.pages.albums.index', compact('albums'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.pages.albums.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:albums,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'featured' => 'nullable|boolean',
            'trending' => 'nullable|boolean',
        ], [
            'name.required' => 'Tên album là bắt buộc',
            'name.string' => 'Tên album phải là chuỗi',
            'name.max' => 'Tên album không được vượt quá 255 ký tự',
            'name.unique' => 'Tên album đã tồn tại',
            'image.required' => 'Ảnh album là bắt buộc',
            'image.image' => 'Ảnh album phải là ảnh',
            'image.mimes' => 'Ảnh album chỉ chấp nhận jpeg, png, jpg, gif, webp',
            'image.max' => 'Ảnh album không được vượt quá 10MB',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = Album::processAndSaveImage($request->file('image'));
        }

        $album = Album::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
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
        return view('Admin.pages.albums.show', compact('album'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album)
    {
        $album->load(['featuredType', 'trendingType']);
        return view('Admin.pages.albums.edit', compact('album'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:albums,name,' . $album->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'featured' => 'nullable|boolean',
            'trending' => 'nullable|boolean',
        ], [
            'name.required' => 'Tên album là bắt buộc',
            'name.string' => 'Tên album phải là chuỗi',
            'name.max' => 'Tên album không được vượt quá 255 ký tự',
            'name.unique' => 'Tên album đã tồn tại',
            'image.image' => 'Ảnh album phải là ảnh',
            'image.mimes' => 'Ảnh album chỉ chấp nhận jpeg, png, jpg, gif, webp',
            'image.max' => 'Ảnh album không được vượt quá 10MB',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ];

        if ($request->hasFile('image')) {
            Album::deleteImage($album->image);
            $data['image'] = Album::processAndSaveImage($request->file('image'));
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
        Album::deleteImage($album->image);
        $album->albumTypes()->delete();
        $album->delete();
        return redirect()->route('admin.albums.index')->with('success', 'Album đã được xóa thành công!');
    }
}
