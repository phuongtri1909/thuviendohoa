<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\Request;

class AboutContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aboutContents = AboutContent::orderBy('key')->get();
        
        return view('admin.pages.about-contents.index', compact('aboutContents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AboutContent $aboutContent)
    {
        return view('admin.pages.about-contents.edit', compact('aboutContent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AboutContent $aboutContent)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
        ], [
            'content.required' => 'Nội dung là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
        ]);

        $aboutContent->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.about-contents.index')
            ->with('success', 'Nội dung đã được cập nhật thành công!');
    }
}
