<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $key = 'feedback:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Bạn đã gửi quá nhiều góp ý. Vui lòng thử lại sau {$seconds} giây."
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10|max:1000',
            'captcha' => 'nullable|string',
        ], [
            'message.required' => 'Vui lòng nhập nội dung góp ý',
            'message.min' => 'Nội dung góp ý phải có ít nhất 10 ký tự',
            'message.max' => 'Nội dung góp ý không được quá 1000 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if ($request->captcha && !$this->verifyCaptcha($request->captcha)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác thực không đúng'
            ], 422);
        }

        try {
            $user = auth()->user();
            
            $feedback = Feedback::create([
                'name' => $user ? $user->full_name : null,
                'email' => $user ? $user->email : null,
                'message' => $request->message,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            RateLimiter::hit($key, 60);

            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã góp ý! Chúng tôi sẽ xem xét và phản hồi sớm nhất.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi góp ý. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    private function verifyCaptcha($captcha)
    {
        $sessionCaptcha = session('feedback_captcha');
        return (int)$captcha === (int)$sessionCaptcha;
    }

    public function generateCaptcha()
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $answer = $num1 + $num2;
        
        session(['feedback_captcha' => $answer]);
        
        return response()->json([
            'question' => "{$num1} + {$num2} = ?",
            'answer' => $answer
        ]);
    }
}