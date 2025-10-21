<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('sets');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('slug')) {
            $query->where('slug', 'like', '%' . $request->slug . '%');
        }

        $categories = $query->paginate(15)->withQueryString();

        return view('admin.pages.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'order' => 'required|integer|min:0',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc',
            'name.string' => 'Tên danh mục phải là chuỗi',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'image.required' => 'Ảnh danh mục là bắt buộc',
            'image.image' => 'Ảnh danh mục phải là ảnh',
            'image.mimes' => 'Ảnh danh mục chỉ chấp nhận jpeg, png, jpg, gif, webp',
            'image.max' => 'Ảnh danh mục không được vượt quá 10MB',
            'order.required' => 'Thứ tự là bắt buộc',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = Category::processAndSaveImage($request->file('image'));
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
            'order' => $request->order,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->loadCount('sets');
        return view('admin.pages.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category->loadCount('sets');
        return view('admin.pages.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'order' => 'required|integer|min:0',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc',
            'name.string' => 'Tên danh mục phải là chuỗi',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'image.image' => 'Ảnh danh mục phải là ảnh',
            'image.mimes' => 'Ảnh danh mục chỉ chấp nhận jpeg, png, jpg, gif, webp',
            'image.max' => 'Ảnh danh mục không được vượt quá 10MB',
            'order.required' => 'Thứ tự là bắt buộc',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'order' => $request->order,
        ];

        if ($request->hasFile('image')) {
            Category::deleteImage($category->image);
            $data['image'] = Category::processAndSaveImage($request->file('image'));
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        Category::deleteImage($category->image);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được xóa thành công!');
    }
}
