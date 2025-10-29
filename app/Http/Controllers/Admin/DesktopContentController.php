<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesktopContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesktopContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $desktopContents = DesktopContent::orderBy('key')->get();
        return view('admin.pages.desktop-contents.index', compact('desktopContents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DesktopContent $desktopContent)
    {
        return view('admin.pages.desktop-contents.edit', compact('desktopContent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DesktopContent $desktopContent)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'title' => 'required|string|max:500',
            'description' => 'required|string',
            'features' => 'required|array|min:1',
            'features.*.icon' => 'nullable|string|max:255',
            'features.*.icon_file' => 'nullable|image|mimes:svg,png,jpg,jpeg,gif,webp|max:5120',
            'features.*.title' => 'required|string|max:255',
            'features.*.description' => 'required|string|max:500',
            'status' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'name.required' => 'Tên là bắt buộc',
            'title.required' => 'Tiêu đề là bắt buộc',
            'description.required' => 'Mô tả là bắt buộc',
            'features.required' => 'Phải có ít nhất 1 tính năng',
            'features.*.title.required' => 'Tiêu đề tính năng là bắt buộc',
            'features.*.description.required' => 'Mô tả tính năng là bắt buộc',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->has('status') ? true : false,
        ];

        if ($request->hasFile('logo')) {
            DesktopContent::deleteImage($desktopContent->logo);
            $data['logo'] = DesktopContent::processAndSaveImage($request->file('logo'), 'logo');
        }

        $features = [];
        foreach ($request->features as $index => $feature) {
            $iconPath = $feature['icon'] ?? null;
            
            if ($request->hasFile("features.{$index}.icon_file")) {
                if ($iconPath && str_starts_with($iconPath, 'desktop-content/')) {
                    DesktopContent::deleteImage($iconPath);
                }
                $iconPath = DesktopContent::processAndSaveImage(
                    $request->file("features.{$index}.icon_file"),
                    'features'
                );
            }

            $features[] = [
                'icon' => $iconPath,
                'title' => $feature['title'],
                'description' => $feature['description'],
            ];
        }

        $data['features'] = $features;

        $desktopContent->update($data);

        return redirect()
            ->route('admin.desktop-contents.index')
            ->with('success', 'Cập nhật desktop content thành công!');
    }

    /**
     * Remove the logo from the specified resource.
     */
    public function deleteLogo(DesktopContent $desktopContent)
    {
        if ($desktopContent->logo && str_starts_with($desktopContent->logo, 'desktop-content/')) {
            DesktopContent::deleteImage($desktopContent->logo);
            $desktopContent->update(['logo' => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa logo thành công!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể xóa logo này!'
        ], 404);
    }

    /**
     * Remove a feature icon from the specified resource.
     */
    public function deleteFeatureIcon(Request $request, DesktopContent $desktopContent)
    {
        $featureIndex = $request->input('feature_index');
        
        if (!isset($desktopContent->features[$featureIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tính năng!'
            ], 404);
        }

        $features = $desktopContent->features;
        $icon = $features[$featureIndex]['icon'] ?? null;

        if ($icon && str_starts_with($icon, 'desktop-content/')) {
            DesktopContent::deleteImage($icon);
            $features[$featureIndex]['icon'] = null;
            $desktopContent->update(['features' => $features]);
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa icon thành công!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể xóa icon này!'
        ], 404);
    }
}

