<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagSet;
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

        $tags = $query->orderBy('order', 'asc')->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('admin.pages.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.pages.tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên tag là bắt buộc',
            'name.string' => 'Tên tag phải là chuỗi',
            'name.max' => 'Tên tag không được vượt quá 255 ký tự',
            'name.unique' => 'Tên tag đã tồn tại',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $maxOrder = Tag::max('order') ?? 0;
        
        Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'order' => $request->order ?? ($maxOrder + 1),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag đã được tạo thành công!');
    }

    public function show(Tag $tag)
    {
        $tag->loadCount('sets');
        return view('admin.pages.tags.show', compact('tag'));
    }

    public function edit(Tag $tag)
    {
        $tag->loadCount('sets');
        return view('admin.pages.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên tag là bắt buộc',
            'name.string' => 'Tên tag phải là chuỗi',
            'name.max' => 'Tên tag không được vượt quá 255 ký tự',
            'name.unique' => 'Tên tag đã tồn tại',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'order' => $request->order ?? $tag->order,
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag đã được cập nhật thành công!');
    }

    public function destroy(Tag $tag)
    {
        TagSet::where('tag_id', $tag->id)->delete();
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'Tag đã được xóa thành công!');
    }
}
