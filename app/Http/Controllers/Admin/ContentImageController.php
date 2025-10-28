<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContentImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contentImages = ContentImage::orderBy('key')->get();
        return view('admin.pages.content-images.index', compact('contentImages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContentImage $contentImage)
    {
        return view('admin.pages.content-images.edit', compact('contentImage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContentImage $contentImage)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'url' => 'nullable|url|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_position_x' => 'nullable|string|max:10',
            'button_position_y' => 'nullable|string|max:10',
            'status' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'url' => $request->url,
            'button_text' => $request->button_text,
            'button_position_x' => $request->button_position_x,
            'button_position_y' => $request->button_position_y,
            'status' => $request->has('status') ? true : false,
        ];

        if ($request->hasFile('image')) {
            ContentImage::deleteImage($contentImage->image);
            
            $data['image'] = ContentImage::processAndSaveImage($request->file('image'));
        }

        $contentImage->update($data);

        return redirect()
            ->route('admin.content-images.index')
            ->with('success', 'Cập nhật content image thành công!');
    }

    /**
     * Remove the image from the specified resource.
     */
    public function deleteImage(ContentImage $contentImage)
    {
        if ($contentImage->image && str_starts_with($contentImage->image, 'content-images/')) {
            ContentImage::deleteImage($contentImage->image);
            $contentImage->update(['image' => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa hình ảnh thành công!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể xóa hình ảnh này!'
        ], 404);
    }
}

