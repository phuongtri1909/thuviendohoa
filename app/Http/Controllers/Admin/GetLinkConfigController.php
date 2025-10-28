<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GetLinkConfig;

class GetLinkConfigController extends Controller
{
    public function edit()
    {
        $config = GetLinkConfig::getInstance();
        return view('admin.pages.get-link-config.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'coins' => 'required|integer|min:1|max:1000',
        ], [
            'coins.required' => 'Vui lòng nhập số xu',
            'coins.integer' => 'Số xu phải là số nguyên',
            'coins.min' => 'Số xu phải lớn hơn 0',
            'coins.max' => 'Số xu không được vượt quá 1000',
        ]);

        $config = GetLinkConfig::getInstance();
        $config->update([
            'coins' => $request->coins,
        ]);

        return redirect()->route('admin.get-link-config.edit')
            ->with('success', 'Cập nhật cấu hình get link thành công!');
    }
}
