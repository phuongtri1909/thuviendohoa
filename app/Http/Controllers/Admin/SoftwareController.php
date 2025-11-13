<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Software;
use Illuminate\Support\Facades\Storage;

class SoftwareController extends Controller
{
    public function index(Request $request)
    {
        $query = Software::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $software = $query->orderBy('order', 'asc')->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('admin.pages.software.index', compact('software'));
    }

    public function create()
    {
        return view('admin.pages.software.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:software,name',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'logo_hover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'logo_active' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên phần mềm là bắt buộc',
            'name.unique' => 'Tên phần mềm đã tồn tại',
            'logo.required' => 'Logo là bắt buộc',
            'logo.image' => 'logo phải là hình ảnh',
            'logo.mimes' => 'định dạng logo không đúng',
            'logo.max' => 'logo không được vượt quá 10MB',
            'logo_hover.image' => 'logo hover phải là hình ảnh',
            'logo_hover.mimes' => 'định dạng logo hover không đúng',
            'logo_hover.max' => 'logo hover không được vượt quá 10MB',
            'logo_active.image' => 'logo active phải là hình ảnh',
            'logo_active.mimes' => 'định dạng logo active không đúng',
            'logo_active.max' => 'logo active không được vượt quá 10MB',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $maxOrder = Software::max('order') ?? 0;
        
        $data = [
            'name' => $validated['name'],
            'order' => $request->order ?? ($maxOrder + 1),
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = Software::processAndSaveImage($request->file('logo'), 'logo');
        }
        if ($request->hasFile('logo_hover')) {
            $data['logo_hover'] = Software::processAndSaveImage($request->file('logo_hover'), 'logo_hover');
        }
        if ($request->hasFile('logo_active')) {
            $data['logo_active'] = Software::processAndSaveImage($request->file('logo_active'), 'logo_active');
        }

        Software::create($data);

        return redirect()->route('admin.software.index')->with('success', 'Phần mềm đã được tạo thành công!');
    }

    public function show(Software $software)
    {
        return view('admin.pages.software.show', compact('software'));
    }

    public function edit(Software $software)
    {
        return view('admin.pages.software.edit', compact('software'));
    }

    public function update(Request $request, Software $software)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:software,name,' . $software->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'logo_hover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'logo_active' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên phần mềm là bắt buộc',
            'name.unique' => 'Tên phần mềm đã tồn tại',
            'logo.image' => 'logo phải là hình ảnh',
            'logo.mimes' => 'định dạng logo không đúng',
            'logo.max' => 'logo không được vượt quá 10MB',
            'logo_hover.image' => 'logo hover phải là hình ảnh',
            'logo_hover.mimes' => 'định dạng logo hover không đúng',
            'logo_hover.max' => 'logo hover không được vượt quá 10MB',
            'logo_active.image' => 'logo active phải là hình ảnh',
            'logo_active.mimes' => 'định dạng logo active không đúng',
            'logo_active.max' => 'logo active không được vượt quá 10MB',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ]);

        $data = [
            'name' => $validated['name'],
            'order' => $request->order ?? $software->order,
        ];

        if ($request->hasFile('logo')) {
            Software::deleteImage($software->logo);
            $data['logo'] = Software::processAndSaveImage($request->file('logo'), 'logo');
        }
        if ($request->hasFile('logo_hover')) {
            Software::deleteImage($software->logo_hover);
            $data['logo_hover'] = Software::processAndSaveImage($request->file('logo_hover'), 'logo_hover');
        }
        if ($request->hasFile('logo_active')) {
            Software::deleteImage($software->logo_active);
            $data['logo_active'] = Software::processAndSaveImage($request->file('logo_active'), 'logo_active');
        }

        $software->update($data);

        return redirect()->route('admin.software.index')->with('success', 'Phần mềm đã được cập nhật thành công!');
    }

    public function destroy(Software $software)
    {
        if ($software->sets()->count() > 0) {
            return redirect()->route('admin.software.index')
                ->with('error', "Không thể xóa phần mềm '{$software->name}' vì đang được sử dụng bởi {$software->sets()->count()} set. Vui lòng xóa hoặc chuyển phần mềm của các set trước.");
        }
        
        Software::deleteImage($software->logo);
        Software::deleteImage($software->logo_hover);
        Software::deleteImage($software->logo_active);
        
        $software->delete();

        return redirect()->route('admin.software.index')->with('success', 'Phần mềm đã được xóa thành công!');
    }
}
