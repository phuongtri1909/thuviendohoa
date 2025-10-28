<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\CategoryBlog;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class CategoryBlogController extends Controller
{
    public function index(Request $request)
    {
        $query = CategoryBlog::withCount('blogs');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('slug')) {
            $query->where('slug', 'like', '%' . $request->slug . '%');
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.pages.category-blogs.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.pages.category-blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:category_blogs,name',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc',
            'name.string' => 'Tên danh mục phải là chuỗi',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'name.unique' => 'Tên danh mục đã tồn tại',
        ]);

        CategoryBlog::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.category-blogs.index')->with('success', 'Danh mục blog đã được tạo thành công!');
    }

    public function show(CategoryBlog $categoryBlog)
    {
        $categoryBlog->loadCount('blogs');
        return view('admin.pages.category-blogs.show', compact('categoryBlog'));
    }

    public function edit(CategoryBlog $categoryBlog)
    {
        $categoryBlog->loadCount('blogs');
        return view('admin.pages.category-blogs.edit', compact('categoryBlog'));
    }

    public function update(Request $request, CategoryBlog $categoryBlog)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:category_blogs,name,' . $categoryBlog->id,
        ], [
            'name.required' => 'Tên danh mục là bắt buộc',
            'name.string' => 'Tên danh mục phải là chuỗi',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'name.unique' => 'Tên danh mục đã tồn tại',
        ]);

        $categoryBlog->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.category-blogs.index')->with('success', 'Danh mục blog đã được cập nhật thành công!');
    }

    public function destroy(CategoryBlog $categoryBlog)
    {
        $categoryBlog->delete();

        return redirect()->route('admin.category-blogs.index')->with('success', 'Danh mục blog đã được xóa thành công!');
    }
}
