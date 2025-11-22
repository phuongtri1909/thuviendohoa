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
use App\Services\FileCleanupService;
use Illuminate\Support\Str;

class SetController extends Controller
{
    protected $cleanupService;

    public function __construct(FileCleanupService $cleanupService)
    {
        $this->cleanupService = $cleanupService;
    }
    public function index(Request $request)
    {
        $query = Set::with(['categories.category', 'albums.album', 'colors.color', 'tags.tag', 'software.software']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories.category', function($q) use ($request) {
                $q->where('id', $request->category_id);
            });
        }

        if ($request->filled('album_id')) {
            $query->whereHas('albums.album', function($q) use ($request) {
                $q->where('id', $request->album_id);
            });
        }

        if ($request->filled('color_id')) {
            $query->whereHas('colors.color', function($q) use ($request) {
                $q->where('id', $request->color_id);
            });
        }

        if ($request->filled('tag_id')) {
            $query->whereHas('tags.tag', function($q) use ($request) {
                $q->where('id', $request->tag_id);
            });
        }

        if ($request->filled('software_id')) {
            $query->whereHas('software.software', function($q) use ($request) {
                $q->where('id', $request->software_id);
            });
        }

        $sets = $query->orderBy('order', 'asc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Load data for filter dropdowns
        $categories = Category::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id', 'name']);
        $albums = Album::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id', 'name']);
        $colors = Color::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id', 'name']);
        $tags = Tag::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id', 'name']);
        $software = Software::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id', 'name']);

        // Prepare sets data for JavaScript
        $setsData = $sets->keyBy('id')->map(function($set) {
            return [
                'categories' => $set->categories->pluck('category.name')->all(),
                'albums' => $set->albums->pluck('album.name')->all(),
                'colors' => $set->colors->pluck('color.name')->all(),
                'tags' => $set->tags->pluck('tag.name')->all(),
                'software' => $set->software->pluck('software.name')->all(),
            ];
        });

        return view('admin.pages.sets.index', compact('sets', 'categories', 'albums', 'colors', 'tags', 'software', 'setsData'));
    }

    public function create()
    {
        $albums = Album::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name']);
        $categories = Category::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name']);
        $colors = Color::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name','value']);
        $software = Software::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name']);
        $tags = Tag::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name']);
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
            'keywords' => 'nullable|string',
            'formats' => 'nullable|string',
            'size' => 'required|numeric',
            'price' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
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
            'order' => 'nullable|integer|min:0',
            'can_use_free_downloads' => 'nullable|boolean',
            'download_method' => 'nullable|in:' . Set::DOWNLOAD_METHOD_BOTH . ',' . Set::DOWNLOAD_METHOD_COINS_ONLY . ',' . Set::DOWNLOAD_METHOD_FREE_ONLY,
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
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $imagePath = Set::processAndSaveImage($request->file('image'));

        $keywords = null;
        if ($request->filled('keywords')) {
            $keywordsInput = trim($request->input('keywords'));
            if (!empty($keywordsInput)) {
                $decoded = json_decode($keywordsInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $keywords = $decoded;
                } else {
                    $keywords = array_filter(array_map('trim', explode(',', $keywordsInput)));
                    $keywords = !empty($keywords) ? array_values($keywords) : null;
                }
            }
        }

        $formats = null;
        if ($request->filled('formats')) {
            $formatsInput = trim($request->input('formats'));
            if (!empty($formatsInput)) {
                $decoded = json_decode($formatsInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $formats = $decoded;
                } else {
                    $parts = array_filter(array_map('trim', explode(',', $formatsInput)));
                    $formats = !empty($parts) ? array_values($parts) : [$formatsInput];
                }
            }
        }

        $set = Set::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'description' => $request->description,
            'image' => $imagePath,
            'drive_url' => $request->drive_url,
            'status' => (bool) $request->status,
            'keywords' => $keywords,
            'formats' => $formats,
            'size' => $request->size,
            'price' => $request->price,
            'is_featured' => (bool) $request->is_featured,
            'order' => $request->order ?? (Set::max('order') ?? 0) + 1,
            'can_use_free_downloads' => (bool) $request->can_use_free_downloads,
            'download_method' => $request->type === Set::TYPE_PREMIUM 
                ? ($request->download_method ?? Set::DOWNLOAD_METHOD_COINS_ONLY)
                : Set::DOWNLOAD_METHOD_FREE_ONLY,
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
        $albums = Album::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name']);
        $categories = Category::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name']);
        $colors = Color::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name','value']);
        $software = Software::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name']);
        $tags = Tag::orderBy('order', 'asc')->orderBy('name', 'asc')->get(['id','name']);
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
            'keywords' => 'nullable|string',
            'formats' => 'nullable|string',
            'size' => 'required|numeric',
            'price' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
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
            'order' => 'nullable|integer|min:0',
            'can_use_free_downloads' => 'nullable|boolean',
            'download_method' => 'nullable|in:' . Set::DOWNLOAD_METHOD_BOTH . ',' . Set::DOWNLOAD_METHOD_COINS_ONLY . ',' . Set::DOWNLOAD_METHOD_FREE_ONLY,
        ], [
            'name.required' => 'Tên set là bắt buộc',
            'name.unique' => 'Tên set đã tồn tại',
            'type.required' => 'Loại set là bắt buộc',
            'description.required' => 'Mô tả là bắt buộc',
            'image.image' => 'Logo phải là hình ảnh',
            'image.max' => 'Ảnh không được vượt quá 10MB',
            'drive_url.required' => 'URL Drive là bắt buộc',
            'drive_url.url' => 'URL Drive không hợp lệ',
            'size.required' => 'Kích thước là bắt buộc',
            'photos.array' => 'Danh sách ảnh không hợp lệ',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        // Check if photos or drive_url changed
        $shouldCleanFiles = $request->hasFile('photos') || 
                           $request->filled('delete_photos') || 
                           $request->drive_url !== $set->drive_url;

        $keywords = null;
        if ($request->filled('keywords')) {
            $keywordsInput = trim($request->input('keywords'));
            if (!empty($keywordsInput)) {
                $decoded = json_decode($keywordsInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $keywords = $decoded;
                } else {
                    $keywords = array_filter(array_map('trim', explode(',', $keywordsInput)));
                    $keywords = !empty($keywords) ? array_values($keywords) : null;
                }
            }
        }

        $formats = null;
        if ($request->filled('formats')) {
            $formatsInput = trim($request->input('formats'));
            if (!empty($formatsInput)) {
                $decoded = json_decode($formatsInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $formats = $decoded;
                } else {
                    $parts = array_filter(array_map('trim', explode(',', $formatsInput)));
                    $formats = !empty($parts) ? array_values($parts) : [$formatsInput];
                }
            }
        }

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'description' => $request->description,
            'drive_url' => $request->drive_url,
            'status' => (bool) $request->status,
            'keywords' => $keywords,
            'formats' => $formats,
            'size' => $request->size,
            'price' => $request->price,
            'is_featured' => (bool) $request->is_featured,
            'order' => $request->order ?? $set->order,
            'can_use_free_downloads' => (bool) $request->can_use_free_downloads,
            'download_method' => $request->type === Set::TYPE_PREMIUM 
                ? ($request->download_method ?? $set->download_method ?? Set::DOWNLOAD_METHOD_COINS_ONLY)
                : Set::DOWNLOAD_METHOD_FREE_ONLY,
        ];

        if ($request->hasFile('image')) {
            Set::deleteImage($set->image);
            $data['image'] = Set::processAndSaveImage($request->file('image'));
        }

        $set->update($data);

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

        if ($set->photos()->count() === 0) {
            return back()->withErrors(['photos' => 'Set phải có ít nhất 1 ảnh'])->withInput();
        }

        if ($shouldCleanFiles) {
            $this->cleanupService->cleanupSetCompletely($set->id);
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
            
        $this->cleanupService->cleanupSetCompletely($set->id);
        
        $set->delete();
        return redirect()->route('admin.sets.index')->with('success', 'Set đã được xóa thành công!');
    }

    public function cleanFiles(Set $set)
    {
        $result = $this->cleanupService->cleanupSetCompletely($set->id);
        
        $totalCount = $result['total_count'];
        $totalSize = $result['total_size'];
        $zipCount = $result['zip']['count'];
        $tempFilesCount = $result['set_files']['count'];
        
        if ($totalCount > 0) {
            $parts = [];
            
            if ($tempFilesCount > 0) {
                $parts[] = "{$tempFilesCount} file tạm";
            }
            
            if ($zipCount > 0) {
                $parts[] = "{$zipCount} file ZIP";
            }
            
            $filesList = implode(' và ', $parts);
            $message = "Đã xóa {$filesList} (" . $this->cleanupService->formatBytes($totalSize) . ").";
        } else {
            $message = "Không có file nào cần xóa.";
        }
        
        return redirect()->back()->with('success', $message);
    }
}
