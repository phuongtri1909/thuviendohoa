<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\OTPUpdateUserMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Set;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function userProfile()
    {
        $user = auth()->user();
        return view('client.pages.user.profile')->with('user', $user);
    }

    public function updateName(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|min:3|max:255',
            ], [
                'name.required' => 'Hãy nhập tên',
                'name.string' => 'Tên phải là chuỗi',
                'name.min' => 'Tên phải có ít nhất 3 ký tự',
                'name.max' => 'Tên không được vượt quá 255 ký tự'
            ]);

            $user = Auth::user();
            $user->full_name = $request->name;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật tên thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($request->has('otp')) {
            $otpArray = is_array($request->otp) ? $request->otp : str_split($request->otp);
            $otp = implode('', $otpArray);

            if (!password_verify($otp, $user->key_reset_password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã OTP không chính xác',
                ], 422);
            }

            if ($request->has('password')) {
                try {
                    $request->validate([
                        'password' => 'required|min:6|confirmed',
                    ], [
                        'password.required' => 'Hãy nhập mật khẩu mới',
                        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                        'password.confirmed' => 'Mật khẩu xác nhận không khớp',
                    ]);

                    $user->password = bcrypt($request->password);
                    $user->key_reset_password = null;
                    $user->save();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Cập nhật mật khẩu thành công',
                    ], 200);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ], 422);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Xác thực OTP thành công',
                'verified' => true
            ]);
        }

        $otp = rand(100000, 999999);
        $user->key_reset_password = bcrypt($otp);
        $user->reset_password_at = now();
        $user->save();

        Mail::to($user->email)->send(new OTPUpdateUserMail($otp, 'password'));

        return response()->json([
            'status' => 'success',
            'message' => 'Mã OTP đã được gửi đến email của bạn'
        ]);
    }

    private function processAndSaveAvatar($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        // Create directories if they don't exist
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/thumbnail");

        // Process original image
        $originalImage = Image::make($imageFile);
        $originalImage->encode('webp', 90);
        Storage::disk('public')->put(
            "avatars/{$yearMonth}/original/{$fileName}.webp",
            $originalImage->stream()
        );

        return [
            'original' => "avatars/{$yearMonth}/original/{$fileName}.webp",
        ];
    }

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            ], [
                'avatar.required' => 'Hãy chọn ảnh avatar',
                'avatar.image' => 'Avatar phải là ảnh',
                'avatar.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg hoặc gif',
                'avatar.max' => 'Dung lượng avatar không được vượt quá 4MB'
            ]);

            $user = Auth::user();
            DB::beginTransaction();

            try {
                $oldAvatar = $user->avatar;
                $oldAvatarThumbnail = $user->avatar_thumbnail;

                $avatarPaths = $this->processAndSaveAvatar($request->file('avatar'));

                $user->avatar = $avatarPaths['original'];
                $user->save();

                DB::commit();

                if ($oldAvatar) {
                    Storage::disk('public')->delete($oldAvatar);
                }
                if ($oldAvatarThumbnail) {
                    Storage::disk('public')->delete($oldAvatarThumbnail);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật avatar thành công',
                    'avatar' => $avatarPaths['original'],
                    'avatar_url' => Storage::url($avatarPaths['original']),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();

                if (isset($avatarPaths)) {
                    Storage::disk('public')->delete([
                        $avatarPaths['original'],
                    ]);
                }

                \Log::error('Avatar update error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }
    }

    public function updateNameOrPhone(Request $request)
    {

        if ($request->has('full_name')) {
            try {
                $request->validate([
                    'full_name' => 'required|string|min:3|max:255',
                ], [
                    'full_name.required' => 'Hãy nhập tên',
                    'full_name.string' => 'Tên phải là chuỗi',
                    'full_name.min' => 'Tên phải có ít nhất 3 ký tự',
                    'full_name.max' => 'Tên không được vượt quá 255 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('user.profile')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->full_name = $request->full_name;
                $user->save();
                return redirect()->route('user.profile')->with('success', 'Cập nhật tên thành công');
            } catch (\Exception $e) {
                return redirect()->route('user.profile')->with('error', 'Cập nhật tên thất bại');
            }
        } elseif ($request->has('phone')) {

            try {
                $request->validate([
                    'phone' => 'required|string|min:10|max:10',
                ], [
                    'phone.required' => 'Hãy nhập số điện thoại',
                    'phone.string' => 'Số điện thoại phải là chuỗi',
                    'phone.min' => 'Số điện thoại phải có ít nhất 10 ký tự',
                    'phone.max' => 'Số điện thoại không được vượt quá 10 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('user.profile')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->phone = $request->phone;
                $user->save();
                return redirect()->route('user.profile')->with('success', 'Cập nhật số điện thoại thành công');
            } catch (\Exception $e) {
                return redirect()->route('user.profile')->with('error', 'Cập nhật số điện thoại thất bại');
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ'
            ], 422);
        }
    }

    public function favorites()
    {
        $user = Auth::user();
        $favorites = $user->favorites()
            ->with('set')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('client.pages.user.favorites', compact('favorites'));
    }

    public function purchases()
    {
        $user = Auth::user();
        $purchases = $user->purchasedSets()
            ->with('set')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('client.pages.user.purchases', compact('purchases'));
    }

    public function addFavorite(Request $request)
    {
        try {
            $request->validate([
                'set_id' => 'required|exists:sets,id',
            ], [
                'set_id.required' => 'ID bộ thiết kế không được để trống',
                'set_id.exists' => 'Bộ thiết kế không tồn tại',
            ]);

            $user = Auth::user();
            
            $existingFavorite = $user->favorites()->where('set_id', $request->set_id)->first();
            
            if ($existingFavorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã yêu thích bộ thiết kế này rồi'
                ], 400);
            }

            $user->favorites()->create([
                'set_id' => $request->set_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào danh sách yêu thích'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm yêu thích'
            ], 500);
        }
    }

    public function removeFavorite(Request $request)
    {
        try {
            $request->validate([
                'favorite_id' => 'required|exists:bookmarks,id',
            ], [
                'favorite_id.required' => 'ID yêu thích không được để trống',
                'favorite_id.exists' => 'Yêu thích không tồn tại',
            ]);

            $user = Auth::user();
            
            $favorite = $user->favorites()->where('id', $request->favorite_id)->first();
            
            if (!$favorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền xóa yêu thích này'
                ], 403);
            }

            $favorite->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khỏi danh sách yêu thích'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa yêu thích'
            ], 500);
        }
    }

    public function toggleFavorite(Request $request, $setId)
    {
        $set = Set::find($setId);
        
        if (!$set) {
            return response()->json([
                'success' => false,
                'message' => 'Set not found'
            ], 404);
        }

        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $existingBookmark = Bookmark::where('user_id', $userId)
            ->where('set_id', $setId)
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            $isFavorited = false;
        } else {
            Bookmark::create([
                'user_id' => $userId,
                'set_id' => $setId
            ]);
            $isFavorited = true;
        }

        $favoriteCount = Bookmark::where('set_id', $setId)->count();

        return response()->json([
            'success' => true,
            'isFavorited' => $isFavorited,
            'favoriteCount' => $favoriteCount
        ]);
    }
}
