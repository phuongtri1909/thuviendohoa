<?php

namespace App\Http\Controllers\Admin;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $query = Color::withCount('sets');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('value')) {
            $query->where('value', 'like', '%' . $request->value . '%');
        }

        $colors = $query->orderBy('order', 'asc')->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('admin.pages.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.pages.colors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
            'value' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên màu là bắt buộc',
            'name.string' => 'Tên màu phải là chuỗi',
            'name.max' => 'Tên màu không được vượt quá 255 ký tự',
            'name.unique' => 'Tên màu đã tồn tại',
            'value.required' => 'Giá trị màu là bắt buộc',
            'value.string' => 'Giá trị màu phải là chuỗi',
            'value.max' => 'Giá trị màu không được vượt quá 7 ký tự',
            'value.regex' => 'Giá trị màu phải là mã hex hợp lệ (ví dụ: #FF0000)',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $maxOrder = Color::max('order') ?? 0;
        
        Color::create([
            'name' => $request->name,
            'value' => $request->value,
            'order' => $request->order ?? ($maxOrder + 1),
        ]);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Màu đã được tạo thành công!');
    }

    public function show(Color $color)
    {
        $color->loadCount('sets');
        return view('admin.pages.colors.show', compact('color'));
    }

    public function edit(Color $color)
    {
        return view('admin.pages.colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name,' . $color->id,
            'value' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên màu là bắt buộc',
            'name.string' => 'Tên màu phải là chuỗi',
            'name.max' => 'Tên màu không được vượt quá 255 ký tự',
            'name.unique' => 'Tên màu đã tồn tại',
            'value.required' => 'Giá trị màu là bắt buộc',
            'value.string' => 'Giá trị màu phải là chuỗi',
            'value.max' => 'Giá trị màu không được vượt quá 7 ký tự',
            'value.regex' => 'Giá trị màu phải là mã hex hợp lệ (ví dụ: #FF0000)',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $color->update([
            'name' => $request->name,
            'value' => $request->value,
            'order' => $request->order ?? $color->order,
        ]);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Màu đã được cập nhật thành công!');
    }

    public function destroy(Color $color)
    {
        $color->colorSets()->delete();
        $color->delete();
        
        return redirect()->route('admin.colors.index')
            ->with('success', 'Màu đã được xóa thành công!');
    }
}
