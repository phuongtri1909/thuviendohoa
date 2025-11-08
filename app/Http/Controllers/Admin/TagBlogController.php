<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\TagBlog;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class TagBlogController extends Controller
{
    public function index(Request $request)
    {
        $query = TagBlog::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('slug')) {
            $query->where('slug', 'like', '%' . $request->slug . '%');
        }

        $tags = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.pages.tag-blogs.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.pages.tag-blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tag_blogs,name',
        ], [
            'name.required' => 'Tên tag là bắt buộc',
            'name.string' => 'Tên tag phải là chuỗi',
            'name.max' => 'Tên tag không được vượt quá 255 ký tự',
            'name.unique' => 'Tên tag đã tồn tại',
        ]);

        TagBlog::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.tag-blogs.index')->with('success', 'Tag blog đã được tạo thành công!');
    }

    public function show(TagBlog $tagBlog)
    {
        return view('admin.pages.tag-blogs.show', compact('tagBlog'));
    }

    public function edit(TagBlog $tagBlog)
    {
        return view('admin.pages.tag-blogs.edit', compact('tagBlog'));
    }

    public function update(Request $request, TagBlog $tagBlog)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tag_blogs,name,' . $tagBlog->id,
        ], [
            'name.required' => 'Tên tag là bắt buộc',
            'name.string' => 'Tên tag phải là chuỗi',
            'name.max' => 'Tên tag không được vượt quá 255 ký tự',
            'name.unique' => 'Tên tag đã tồn tại',
        ]);

        $tagBlog->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.tag-blogs.index')->with('success', 'Tag blog đã được cập nhật thành công!');
    }

    public function destroy(TagBlog $tagBlog)
    {
        $tagBlog->blogTags()->delete();
        
        $tagBlog->delete();

        return redirect()->route('admin.tag-blogs.index')->with('success', 'Tag blog đã được xóa thành công!');
    }
}
