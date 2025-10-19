<?php

namespace App\Http\Controllers\Client;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\OTPForgotPWMail;
use App\Models\GoogleSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter your email.',
            'email.email' => 'The email you entered is invalid.',
            'password.required' => 'Please enter your password.',
        ]);

        try {

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không hợp lệ. Vui lòng thử lại.',
                ]);
            }

            if ($user->active == false) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không hợp lệ. Vui lòng thử lại.',
                ]);
            }

            if (!password_verify($request->password, $user->password)) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không hợp lệ. Vui lòng thử lại.',
                ]);
            }


            Auth::login($user);


            $user->ip_address = $request->ip();
            $user->save();

            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred during login. Please try again later.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function redirectToGoogle()
    {
        $googleSettings = GoogleSetting::first();

        if (!$googleSettings) {
            return redirect()->route('login')
                ->with('error', 'Đang nhập bằng Google hiện không khả dụng. Vui lòng thử lại sau.');
        }

        config([
            'services.google.client_id' => $googleSettings->google_client_id,
            'services.google.client_secret' => $googleSettings->google_client_secret,
            'services.google.redirect' => route($googleSettings->google_redirect)
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            $googleSettings = GoogleSetting::first();

            if (!$googleSettings) {
                return redirect()->route('login')
                    ->with('error', 'Đang nhập bằng Google hiện không khả dụng. Vui lòng thử lại sau.');
            }

            config([
                'services.google.client_id' => $googleSettings->google_client_id,
                'services.google.client_secret' => $googleSettings->google_client_secret,
                'services.google.redirect' => route($googleSettings->google_redirect)
            ]);

            $googleUser = Socialite::driver('google')->user();
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                $existingUser->active = true;
                $existingUser->save();
                Auth::login($existingUser);

                return redirect()->route('home');
            } else {
                $user = new User();
                $user->full_name = $googleUser->getName();
                $user->email = $googleUser->getEmail();
                $user->password = bcrypt(Str::random(16)); 
                $user->active = true;
                
                if ($googleUser->getAvatar()) {
                    try {
                        $avatar = file_get_contents($googleUser->getAvatar());
                        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
                        file_put_contents($tempFile, $avatar);

                        $avatarPaths = $this->processAndSaveAvatar($tempFile);
                        $user->avatar = $avatarPaths['original'];
                        unlink($tempFile);
                    } catch (\Exception $e) {
                        Log::error('Error processing Google avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();
                Auth::login($user);

                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            Log::error('Google login error:', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại sau.');
        }
    }

    private function processAndSaveAvatar($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/thumbnail");

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

    public function register(Request $request)
    {
        Log::info('Registration request data:', $request->all());

        if ($request->has('email') && $request->has('otp') && $request->has('password')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                    'otp' => 'required',
                    'password' => 'required|min:6',
                    'full_name' => 'required|max:255',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                ], [
                    'email.required' => 'Hãy nhập email của bạn vào đi',
                    'email.email' => 'Email bạn nhập không hợp lệ rồi',
                    'otp.required' => 'Hãy nhập mã OTP của bạn vào đi',
                    'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
                    'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                    'full_name.required' => 'Hãy nhập tên của bạn vào đi',
                    'full_name.max' => 'Tên của bạn quá dài rồi',
                    'avatar.required' => 'Hãy chọn ảnh đại diện của bạn',
                    'avatar.image' => 'Ảnh bạn chọn không hợp lệ',
                    'avatar.mimes' => 'Ảnh bạn chọn phải có định dạng jpeg, png, jpg, gif, svg, webp',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Email này không hợp lệ']],
                    ], 422);
                }

                if (!password_verify($request->otp, $user->key_active)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['otp' => ['Mã OTP không chính xác']],
                    ], 422);
                }
                $user->key_active = null;
                $user->full_name = $request->full_name;
                $user->password = bcrypt($request->password);
                $user->active = true;

                if ($request->hasFile('avatar')) {
                    try {
                        $avatarPaths = $this->processAndSaveAvatar($request->file('avatar'));
                        $user->avatar = $avatarPaths['original'];
                    } catch (\Exception $e) {
                        Log::error('Error processing avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();

                Auth::login($user);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Đăng ký thành công, chào mừng bạn đến với ' . env('APP_NAME'),
                    'url' => route('home'),
                ]);
            } catch (Exception $e) {
                Log::error('Registration error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
                    'error' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
                ], 500);
            }
        }
        try {
            $request->validate([
                'email' => 'required|email',
            ], [
                'email.required' => 'Hãy nhập email của bạn vào đi',
                'email.email' => 'Email bạn nhập không hợp lệ rồi',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if ($user->active == true) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Email này đã tồn tại, hãy dùng email khác']],
                    ], 422);
                }

                if (!$user->updated_at->lt(Carbon::now()->subMinutes(3)) && $user->key_active != null) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Dùng lại OTP đã gửi trước đó, nhận OTP mới sau 3 phút',
                    ], 200);
                }
            } else {
                $user = new User();
                $user->email = $request->email;
                $user->full_name = 'VF ' . Str::random(5);
            }

            $randomPassword = Str::random(10);
            $user->password = bcrypt($randomPassword);

            $otp = generateRandomOTP();
            $user->save();

            Mail::to($user->email)->send(new OTPMail($otp));
            $user->key_active = bcrypt($otp);
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Đăng ký thành công, hãy kiểm tra email của bạn để lấy mã OTP',
            ]);
        } catch (Exception $e) {
            Log::error('Registration error:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
                'error' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        if ($request->has('email')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                ], [
                    'email.required' => 'Hãy nhập email của bạn vào đi',
                    'email.email' => 'Email bạn nhập không hợp lệ rồi',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user || $user->active == false) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Thông tin xác thực không chính xác']],
                    ], 422);
                }

                if ($request->has('email') && $request->has('otp')) {

                    try {
                        $request->validate([
                            'otp' => 'required',
                        ], [
                            'otp.required' => 'Hãy nhập mã OTP của bạn vào đi',
                        ]);
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $e->errors()
                        ], 422);
                    }

                    if (!password_verify($request->otp, $user->key_reset_password)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => ['otp' => ['Mã OTP không chính xác']],
                        ], 422);
                    }

                    if ($request->has('email') && $request->has('otp') && $request->has('password')) {
                        try {
                            $request->validate([
                                'password' => 'required|min:6',
                            ], [
                                'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
                                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                            ]);
                        } catch (\Illuminate\Validation\ValidationException $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => $e->errors()
                            ], 422);
                        }

                        try {

                            $user->key_reset_password = null;
                            $user->password = bcrypt($request->password);
                            $user->save();

                            Auth::login($user);

                            return response()->json([
                                'status' => 'success',
                                'message' => 'Đặt lại mật khẩu thành công',
                                'url' => route('home'),
                            ]);
                        } catch (Exception $e) {
                            Log::error('Reset password error:', ['error' => $e->getMessage()]);
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                                'error' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                            ], 500);
                        }
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Hãy nhập mật khẩu mới của bạn',
                    ], 200);
                }

                if ($user->reset_password_at != null) {
                    $resetPasswordAt = Carbon::parse($user->reset_password_at);
                    if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(3))) {
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Dùng lại OTP đã gửi trước đó, nhận OTP mới sau 3 phút',
                        ], 200);
                    }
                }

                $randomOTPForgotPW = generateRandomOTP();
                $user->key_reset_password = bcrypt($randomOTPForgotPW);
                $user->reset_password_at = Carbon::now();
                $user->save();

                Mail::to($user->email)->send(new OTPForgotPWMail($randomOTPForgotPW));
                return response()->json([
                    'status' => 'success',
                    'message' => 'Hãy kiểm tra email của bạn để lấy mã OTP',
                ], 200);
            } catch (Exception $e) {
                Log::error('Forgot password error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                    'error' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                ], 500);
            }
        }
    }

}
