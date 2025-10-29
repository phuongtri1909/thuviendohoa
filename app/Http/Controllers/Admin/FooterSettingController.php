<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\FooterSetting;
use App\Http\Controllers\Controller;

class FooterSettingController extends Controller
{
    public function edit()
    {
        $setting = FooterSetting::first();
        
        if (!$setting) {
            $setting = FooterSetting::create([
                'facebook_url' => '',
                'support_hotline' => '',
                'support_email' => '',
                'support_fanpage' => '',
                'support_fanpage_url' => '',
                'partners' => [],
            ]);
        }

        return view('admin.pages.footer-setting.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'facebook_url' => 'nullable|url',
            'support_hotline' => 'nullable|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'support_fanpage' => 'nullable|string|max:255',
            'support_fanpage_url' => 'nullable|url',
            'partners' => 'nullable|array',
            'partners.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'partners.*.url' => 'nullable|url',
        ], [
            'facebook_url.url' => 'URL Facebook không hợp lệ',
            'support_email.email' => 'Email không hợp lệ',
            'support_fanpage_url.url' => 'URL Fanpage không hợp lệ',
            'partners.*.image.image' => 'File phải là hình ảnh',
            'partners.*.image.max' => 'Kích thước ảnh không được vượt quá 5MB',
            'partners.*.url.url' => 'URL không hợp lệ',
        ]);

        try {
            $setting = FooterSetting::first();
            
            if (!$setting) {
                $setting = new FooterSetting();
            }

            // Handle partners
            $partners = [];
            if ($request->has('partners')) {
                foreach ($request->partners as $index => $partner) {
                    $partnerData = [
                        'name' => $partner['name'] ?? '',
                        'url' => $partner['url'] ?? '',
                        'image' => $partner['existing_image'] ?? null,
                    ];

                    // If new image uploaded
                    if ($request->hasFile("partners.{$index}.image")) {
                        // Delete old image if exists
                        if (!empty($partner['existing_image'])) {
                            FooterSetting::deletePartnerImage($partner['existing_image']);
                        }
                        
                        $partnerData['image'] = FooterSetting::processAndSavePartnerImage(
                            $request->file("partners.{$index}.image")
                        );
                    }

                    if (!empty($partnerData['image'])) {
                        $partners[] = $partnerData;
                    }
                }
            }

            $setting->facebook_url = $request->facebook_url;
            $setting->support_hotline = $request->support_hotline;
            $setting->support_email = $request->support_email;
            $setting->support_fanpage = $request->support_fanpage;
            $setting->support_fanpage_url = $request->support_fanpage_url;
            $setting->partners = $partners;
            $setting->save();

            return redirect()->route('admin.footer-setting.edit')->with('success', 'Cập nhật Footer thành công');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function deletePartner(Request $request)
    {
        try {
            $setting = FooterSetting::first();
            $partners = $setting->partners ?? [];
            $index = $request->index;

            if (isset($partners[$index])) {
                // Delete image
                if (!empty($partners[$index]['image'])) {
                    FooterSetting::deletePartnerImage($partners[$index]['image']);
                }
                
                // Remove from array
                array_splice($partners, $index, 1);
                $setting->partners = $partners;
                $setting->save();

                return response()->json(['success' => true, 'message' => 'Xóa đối tác thành công']);
            }

            return response()->json(['success' => false, 'message' => 'Không tìm thấy đối tác'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
