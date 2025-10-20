<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::withCount('sets');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('slug')) {
            $query->where('slug', 'like', '%' . $request->slug . '%');
        }

        $tags = $query->paginate(15)->withQueryString();

        return view('Admin.pages.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('Admin.pages.tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ], [
            'name.required' => 'Tên tag là bắt buộc',
            'name.string' => 'Tên tag phải là chuỗi',
            'name.max' => 'Tên tag không được vượt quá 255 ký tự',
            'name.unique' => 'Tên tag đã tồn tại',
        ]);

        Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag đã được tạo thành công!');
    }

    public function show(Tag $tag)
    {
        $tag->loadCount('sets');
        return view('Admin.pages.tags.show', compact('tag'));
    }

    public function edit(Tag $tag)
    {
        $tag->loadCount('sets');
        return view('Admin.pages.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
        ], [
            'name.required' => 'Tên tag là bắt buộc',
            'name.string' => 'Tên tag phải là chuỗi',
            'name.max' => 'Tên tag không được vượt quá 255 ký tự',
            'name.unique' => 'Tên tag đã tồn tại',
        ]);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag đã được cập nhật thành công!');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('admin.tags.index')->with('success', 'Tag đã được xóa thành công!');
    }
}
