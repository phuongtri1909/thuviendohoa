<?php

namespace App\Http\Controllers\Client;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            $existingUser = User::where('facebook_id', $facebookUser->id)->first();

            if ($existingUser) {
                $existingUser->active = true;
                $existingUser->save();
                Auth::login($existingUser);

                return redirect()->route('home');
            } else {
                $user = new User();
                $user->full_name = $facebookUser->name;
                $user->facebook_id  = $facebookUser->id;
                $user->password = bcrypt(Str::random(16)); 
                $user->active = true;
                
                if ($facebookUser->avatar) {
                    try {
                        $avatar = file_get_contents($facebookUser->avatar);
                        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
                        file_put_contents($tempFile, $avatar);

                        $avatarPaths = $this->processAndSaveAvatar($tempFile);
                        $user->avatar = $avatarPaths['original'];
                        unlink($tempFile);
                    } catch (\Exception $e) {
                        Log::error('Error processing Facebook avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();
                Auth::login($user);

                return redirect()->route('home');
            }
        } catch (Exception $e) {
            Log::error('Facebook login error:', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Đăng nhập Facebook thất bại!');
        }
    }
}