<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogSidebarSetting;
use App\Models\CategoryBlog;
use Illuminate\Http\Request;

class BlogSidebarSettingController extends Controller
{
    public function edit()
    {
        $setting = BlogSidebarSetting::getInstance();
        $categories = CategoryBlog::orderBy('name')->get();
        
        return view('admin.pages.blog-sidebar-setting.edit', compact('setting', 'categories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'section_title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:category_blogs,id',
            'extra_link_title' => 'nullable|string|max:255',
            'extra_link_url' => 'nullable|url|max:500',
            'banner_images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ], [
            'section_title.required' => 'Tiêu đề phần là bắt buộc.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'extra_link_url.url' => 'URL không hợp lệ.',
            'banner_images.*.image' => 'File phải là hình ảnh.',
            'banner_images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, jpg, png, gif, webp.',
            'banner_images.*.max' => 'Kích thước hình ảnh không được vượt quá 10MB.',
        ]);

        $setting = BlogSidebarSetting::getInstance();
        
        $data = [
            'section_title' => $request->section_title,
            'category_id' => $request->category_id,
            'extra_link_title' => $request->extra_link_title,
            'extra_link_url' => $request->extra_link_url,
        ];

        // Handle banner images upload
        if ($request->hasFile('banner_images')) {
            $bannerPaths = [];
            foreach ($request->file('banner_images') as $image) {
                $path = $image->store('blog/banners', 'public');
                $bannerPaths[] = $path;
            }
            $data['banner_images'] = $bannerPaths;
        }

        $setting->update($data);

        return redirect()->route('admin.blog-sidebar-setting.edit')
            ->with('success', 'Cài đặt sidebar blog đã được cập nhật thành công!');
    }

    public function deleteBanner(Request $request)
    {
        $setting = BlogSidebarSetting::getInstance();
        $banners = $setting->banner_images ?? [];
        
        $indexToRemove = $request->input('index');
        
        if (isset($banners[$indexToRemove])) {
            \Storage::disk('public')->delete($banners[$indexToRemove]);
            unset($banners[$indexToRemove]);
            $setting->update(['banner_images' => array_values($banners)]);
        }

        return response()->json(['success' => true]);
    }
}
