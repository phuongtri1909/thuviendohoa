<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TwitterSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class TwitterController extends Controller
{
    /**
     * Redirect to Twitter OAuth
     */
    public function redirectToTwitter()
    {
        $twitterSettings = TwitterSetting::first();

        if (!$twitterSettings) {
            return redirect()->route('login')
                ->with('error', 'Đăng nhập bằng Twitter hiện không khả dụng. Vui lòng thử lại sau.');
        }

        config([
            'services.twitter.client_id' => $twitterSettings->twitter_client_id,
            'services.twitter.client_secret' => $twitterSettings->twitter_client_secret,
            'services.twitter.redirect' => route($twitterSettings->twitter_redirect)
        ]);

        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Handle Twitter callback
     */
    public function handleTwitterCallback()
    {
        try {
            $twitterSettings = TwitterSetting::first();

            if (!$twitterSettings) {
                return redirect()->route('login')
                    ->with('error', 'Đăng nhập bằng Twitter hiện không khả dụng. Vui lòng thử lại sau.');
            }

            config([
                'services.twitter.client_id' => $twitterSettings->twitter_client_id,
                'services.twitter.client_secret' => $twitterSettings->twitter_client_secret,
                'services.twitter.redirect' => route($twitterSettings->twitter_redirect)
            ]);

            $twitterUser = Socialite::driver('twitter')->user();
            
            $existingUser = User::where('twitter_id', $twitterUser->id)->first();
            
            if ($existingUser) {
                $existingUser->active = true;
                $existingUser->save();
                Auth::login($existingUser);

                return redirect()->route('home');
            } else {
                $user = new User();
                $user->full_name = $twitterUser->name;
                $user->twitter_id = $twitterUser->id;
                $user->email = $twitterUser->email ?? $twitterUser->nickname . '@twitter.com';
                $user->password = bcrypt(Str::random(16));
                $user->active = true;
                
                if ($twitterUser->avatar) {
                    try {
                        $avatar = file_get_contents($twitterUser->avatar);
                        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
                        file_put_contents($tempFile, $avatar);

                        $avatarPaths = $this->processAndSaveAvatar($tempFile);
                        $user->avatar = $avatarPaths['original'];
                        unlink($tempFile);
                    } catch (\Exception $e) {
                        Log::error('Error processing Twitter avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();
                Auth::login($user);

                return redirect()->route('home');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['error' => 'Đăng nhập Twitter thất bại: ' . $e->getMessage()]);
        }
    }

    private function processAndSaveAvatar($imageFile)
    {
        $now = \Carbon\Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/original");
        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/thumbnail");

        $originalImage = \Intervention\Image\Facades\Image::make($imageFile);
        $originalImage->encode('webp', 90);
        \Illuminate\Support\Facades\Storage::disk('public')->put(
            "avatars/{$yearMonth}/original/{$fileName}.webp",
            $originalImage->stream()
        );

        return [
            'original' => "avatars/{$yearMonth}/original/{$fileName}.webp",
        ];
    }
}