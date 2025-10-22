<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Set;
use App\Models\Album;
use App\Models\Category;
use App\Models\Color;
use App\Models\Software;
use App\Models\Tag;
use App\Models\AlbumSet;
use App\Models\CategorySet;
use App\Models\ColorSet;
use App\Models\SoftwareSet;
use App\Models\TagSet;
use App\Models\Photo;
use Illuminate\Support\Str;

class SetController extends Controller
{
    public function index(Request $request)
    {
        $query = Set::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $sets = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.pages.sets.index', compact('sets'));
    }

    public function create()
    {
        $albums = Album::orderBy('name')->get(['id','name']);
        $categories = Category::orderBy('name')->get(['id','name']);
        $colors = Color::orderBy('name')->get(['id','name','value']);
        $software = Software::orderBy('name')->get(['id','name']);
        $tags = Tag::orderBy('name')->get(['id','name']);
        return view('admin.pages.sets.create', compact('albums','categories','colors','software','tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sets,name',
            'type' => 'required|in:' . Set::TYPE_FREE . ',' . Set::TYPE_PREMIUM,
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'drive_url' => 'required|url',
            'status' => 'nullable|boolean',
            'keywords' => 'nullable',
            'formats' => 'nullable',
            'size' => 'required|numeric',
            'price' => 'nullable|integer',
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'album_ids' => 'nullable|array',
            'album_ids.*' => 'integer|exists:albums,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'color_ids' => 'nullable|array',
            'color_ids.*' => 'integer|exists:colors,id',
            'software_ids' => 'nullable|array',
            'software_ids.*' => 'integer|exists:software,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
        ], [
            'name.required' => 'Tên set là bắt buộc',
            'name.unique' => 'Tên set đã tồn tại',
            'type.required' => 'Loại set là bắt buộc',
            'description.required' => 'Mô tả là bắt buộc',
            'image.required' => 'Logo (ảnh) là bắt buộc',
            'image.max' => 'Ảnh không được vượt quá 10MB',
            'drive_url.required' => 'URL Drive là bắt buộc',
            'drive_url.url' => 'URL Drive không hợp lệ',
            'size.required' => 'Kích thước là bắt buộc',
            'price.required' => 'Giá là bắt buộc',
            'photos.required' => 'Phải tải lên ít nhất 1 ảnh cho set',
        ]);

        $imagePath = Set::processAndSaveImage($request->file('image'));

        $set = Set::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'description' => $request->description,
            'image' => $imagePath,
            'drive_url' => $request->drive_url,
            'status' => (bool) $request->status,
            'keywords' => $request->filled('keywords') ? $request->input('keywords') : null,
            'formats' => $request->filled('formats') ? $request->input('formats') : null,
            'size' => $request->size,
            'price' => $request->price,
        ]);

        // create relations
        if ($request->filled('album_ids')) {
            $albumRows = collect($request->album_ids)->unique()->values()->map(fn($id) => ['album_id' => (int)$id]);
            $albumRows->each(fn($row) => AlbumSet::create($row + ['set_id' => $set->id]));
        }
        if ($request->filled('category_ids')) {
            $rows = collect($request->category_ids)->unique()->values()->map(fn($id) => ['category_id' => (int)$id]);
            $rows->each(fn($row) => CategorySet::create($row + ['set_id' => $set->id]));
        }
        if ($request->filled('color_ids')) {
            $rows = collect($request->color_ids)->unique()->values()->map(fn($id) => ['color_id' => (int)$id]);
            $rows->each(fn($row) => ColorSet::create($row + ['set_id' => $set->id]));
        }
        if ($request->filled('software_ids')) {
            $rows = collect($request->software_ids)->unique()->values()->map(fn($id) => ['software_id' => (int)$id]);
            $rows->each(fn($row) => SoftwareSet::create($row + ['set_id' => $set->id]));
        }
        if ($request->filled('tag_ids')) {
            $rows = collect($request->tag_ids)->unique()->values()->map(fn($id) => ['tag_id' => (int)$id]);
            $rows->each(fn($row) => TagSet::create($row + ['set_id' => $set->id]));
        }

        // save photos
        foreach ($request->file('photos', []) as $photoFile) {
            $path = Photo::processAndSavePhoto($photoFile);
            $sizeMb = (int) ceil(($photoFile->getSize() ?? 0) / 1048576);
            Photo::create([
                'set_id' => $set->id,
                'path' => $path,
                'size' => $sizeMb,
            ]);
        }

        return redirect()->route('admin.sets.index')->with('success', 'Set đã được tạo thành công!');
    }

    public function show(Set $set)
    {
        return view('admin.pages.sets.show', compact('set'));
    }

    public function edit(Set $set)
    {
        $albums = Album::orderBy('name')->get(['id','name']);
        $categories = Category::orderBy('name')->get(['id','name']);
        $colors = Color::orderBy('name')->get(['id','name']);
        $software = Software::orderBy('name')->get(['id','name']);
        $tags = Tag::orderBy('name')->get(['id','name']);
        $set->load(['albums','categories','colors','software','tags','photos']);
        return view('admin.pages.sets.edit', compact('set','albums','categories','colors','software','tags'));
    }

    public function update(Request $request, Set $set)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sets,name,' . $set->id,
            'type' => 'required|in:' . Set::TYPE_FREE . ',' . Set::TYPE_PREMIUM,
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'drive_url' => 'required|url',
            'status' => 'nullable|boolean',
            'keywords' => 'nullable',
            'formats' => 'nullable',
            'size' => 'required|numeric',
            'price' => 'nullable|integer',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'album_ids' => 'nullable|array',
            'album_ids.*' => 'integer|exists:albums,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'color_ids' => 'nullable|array',
            'color_ids.*' => 'integer|exists:colors,id',
            'software_ids' => 'nullable|array',
            'software_ids.*' => 'integer|exists:software,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'description' => $request->description,
            'drive_url' => $request->drive_url,
            'status' => (bool) $request->status,
            'keywords' => $request->filled('keywords') ? $request->input('keywords') : null,
            'formats' => $request->filled('formats') ? $request->input('formats') : null,
            'size' => $request->size,
            'price' => $request->price,
        ];

        if ($request->hasFile('image')) {
            Set::deleteImage($set->image);
            $data['image'] = Set::processAndSaveImage($request->file('image'));
        }

        $set->update($data);

        // Handle photo deletions
        if ($request->filled('delete_photos')) {
            $photosToDelete = Photo::whereIn('id', $request->delete_photos)->where('set_id', $set->id)->get();
            foreach ($photosToDelete as $photo) {
                Photo::deletePhoto($photo->path);
                $photo->delete();
            }
        }

        AlbumSet::where('set_id', $set->id)->delete();
        CategorySet::where('set_id', $set->id)->delete();
        ColorSet::where('set_id', $set->id)->delete();
        SoftwareSet::where('set_id', $set->id)->delete();
        TagSet::where('set_id', $set->id)->delete();

        if ($request->filled('album_ids')) {
            collect($request->album_ids)->unique()->values()->each(function ($id) use ($set) {
                AlbumSet::create(['album_id' => (int)$id, 'set_id' => $set->id]);
            });
        }
        if ($request->filled('category_ids')) {
            collect($request->category_ids)->unique()->values()->each(function ($id) use ($set) {
                CategorySet::create(['category_id' => (int)$id, 'set_id' => $set->id]);
            });
        }
        if ($request->filled('color_ids')) {
            collect($request->color_ids)->unique()->values()->each(function ($id) use ($set) {
                ColorSet::create(['color_id' => (int)$id, 'set_id' => $set->id]);
            });
        }
        if ($request->filled('software_ids')) {
            collect($request->software_ids)->unique()->values()->each(function ($id) use ($set) {
                SoftwareSet::create(['software_id' => (int)$id, 'set_id' => $set->id]);
            });
        }
        if ($request->filled('tag_ids')) {
            collect($request->tag_ids)->unique()->values()->each(function ($id) use ($set) {
                TagSet::create(['tag_id' => (int)$id, 'set_id' => $set->id]);
            });
        }

        // save additional photos if any
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $path = Photo::processAndSavePhoto($photoFile);
                $sizeMb = (int) ceil(($photoFile->getSize() ?? 0) / 1048576);
                Photo::create([
                    'set_id' => $set->id,
                    'path' => $path,
                    'size' => $sizeMb,
                ]);
            }
        }

        // ensure at least one photo exists overall
        if ($set->photos()->count() === 0) {
            return back()->withErrors(['photos' => 'Set phải có ít nhất 1 ảnh'])->withInput();
        }

        return redirect()->route('admin.sets.index')->with('success', 'Set đã được cập nhật thành công!');
    }

    public function destroy(Set $set)
    {
        Set::deleteImage($set->image);
        foreach ($set->photos as $photo) {
            Photo::deletePhoto($photo->path);
        }
        $set->photos()->delete();
        AlbumSet::where('set_id', $set->id)->delete();
        CategorySet::where('set_id', $set->id)->delete();
        ColorSet::where('set_id', $set->id)->delete();
        SoftwareSet::where('set_id', $set->id)->delete();
        TagSet::where('set_id', $set->id)->delete();
        $set->delete();
        return redirect()->route('admin.sets.index')->with('success', 'Set đã được xóa thành công!');
    }
}
