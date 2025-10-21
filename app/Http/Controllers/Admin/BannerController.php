<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('banners');

        if ($request->filled('page_filter')) {
            $query->where('key_page', $request->page_filter);
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $banners = $query->orderByDesc('created_at')
            ->orderBy('order', 'ASC')
            ->paginate(15)
            ->withQueryString();

        $banners->getCollection()->transform(function ($item) {
            return Banner::find($item->id);
        });

        return view('admin.pages.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.pages.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'key_page' => 'required|in:' . Banner::PAGE_HOME . ',' . Banner::PAGE_SEARCH . ',' . Banner::PAGE_ALBUMS,
            'order' => 'required|integer|min:0',
            'status' => 'nullable|boolean',
        ], [
            'image.required' => 'Ảnh banner là bắt buộc',
            'image.max' => 'Ảnh không được vượt quá 10MB',
            'key_page.required' => 'Trang là bắt buộc',
            'key_page.in' => 'Trang không hợp lệ',
            'order.required' => 'Thứ tự là bắt buộc',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $imagePath = Banner::processAndSaveImage($request->file('image'));

        Banner::create([
            'image' => $imagePath,
            'key_page' => $request->key_page,
            'order' => $request->order,
            'status' => (bool) $request->status,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được tạo thành công!');
    }

    public function show(Banner $banner)
    {
        return view('admin.pages.banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        return view('admin.pages.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'key_page' => 'required|in:' . Banner::PAGE_HOME . ',' . Banner::PAGE_SEARCH . ',' . Banner::PAGE_ALBUMS,
            'order' => 'required|integer|min:0',
            'status' => 'nullable|boolean',
        ], [
            'image.max' => 'Ảnh không được vượt quá 10MB',
            'key_page.required' => 'Trang là bắt buộc',
            'key_page.in' => 'Trang không hợp lệ',
            'order.required' => 'Thứ tự là bắt buộc',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $data = [
            'key_page' => $request->key_page,
            'order' => $request->order,
            'status' => (bool) $request->status,
        ];

        if ($request->hasFile('image')) {
            Banner::deleteImage($banner->image);
            $data['image'] = Banner::processAndSaveImage($request->file('image'));
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được cập nhật thành công!');
    }

    public function destroy(Banner $banner)
    {
        Banner::deleteImage($banner->image);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được xóa thành công!');
    }
}
