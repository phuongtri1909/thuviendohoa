<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GetLinkConfig;
use App\Models\GetLinkHistory;
use App\Models\CoinHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GetLinkController extends Controller
{
    public function index()
    {
        $config = GetLinkConfig::getInstance();
        
        $supportedSites = GetLinkHistory::select('title', 'url', 'favicon')
            ->whereNotNull('title')
            ->whereNotNull('favicon')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) {
                $parsed = parse_url($item->url);
                return $parsed['host'] ?? $item->url;
            })
            ->take(12)
            ->map(function ($history) {
                $parsed = parse_url($history->url);
                return [
                    'url' => $history->url,
                    'name' => $history->title,
                    'domain' => $parsed['host'] ?? '',
                    'favicon' => $history->favicon,
                ];
            });
        
        return view('client.pages.get-link', compact('config', 'supportedSites'));
    }

    public function processGetLink(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:2048',
        ], [
            'url.required' => 'Vui lòng nhập URL',
            'url.url' => 'URL không hợp lệ',
            'url.max' => 'URL quá dài',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng dịch vụ'
            ], 401);
        }

        $user = Auth::user();
        $config = GetLinkConfig::getInstance();
        $url = $request->input('url');

        if ($user->coins < $config->coins) {
            return response()->json([
                'success' => false,
                'message' => "Bạn không đủ xu. Cần {$config->coins} xu, bạn hiện có {$user->coins} xu"
            ], 400);
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ])
                ->get($url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể truy cập URL. Vui lòng kiểm tra lại'
                ], 400);
            }

            $html = $response->body();
            $title = $this->extractTitle($html, $url);
            $favicon = $this->extractFavicon($html, $url);

            DB::beginTransaction();
            try {
                $user->decrement('coins', $config->coins);

                GetLinkHistory::create([
                    'user_id' => $user->id,
                    'url' => $url,
                    'title' => $title,
                    'favicon' => $favicon,
                    'coins_spent' => $config->coins,
                ]);

                CoinHistory::create([
                    'user_id' => $user->id,
                    'type' => 'getlink',
                    'source' => 'getlink',
                    'reason' => 'Get link premium',
                    'amount' => -$config->coins,
                    'description' => "Get link: {$title}",
                    'is_read' => false,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Get link thành công!',
                    'data' => [
                        'url' => $url,
                        'title' => $title,
                        'favicon' => $favicon,
                        'coins_spent' => $config->coins,
                        'remaining_coins' => $user->coins - $config->coins,
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối đến URL. Vui lòng thử lại'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    private function extractTitle($html, $url)
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return trim(strip_tags($matches[1]));
        }

        if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\'](.*?)["\']/is', $html, $matches)) {
            return trim($matches[1]);
        }

        $parsedUrl = parse_url($url);
        return $parsedUrl['host'] ?? 'Untitled';
    }

    private function extractFavicon($html, $url)
    {
        $parsedUrl = parse_url($url);
        $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        if (preg_match('/<link[^>]+rel=["\'](?:icon|shortcut icon)["\'][^>]+href=["\'](.*?)["\']/is', $html, $matches)) {
            $faviconUrl = $matches[1];
            if (!filter_var($faviconUrl, FILTER_VALIDATE_URL)) {
                $faviconUrl = $baseUrl . '/' . ltrim($faviconUrl, '/');
            }
            return $faviconUrl;
        }

        if (preg_match('/<link[^>]+href=["\'](.*?)["\'][^>]+rel=["\'](?:icon|shortcut icon)["\']/is', $html, $matches)) {
            $faviconUrl = $matches[1];
            if (!filter_var($faviconUrl, FILTER_VALIDATE_URL)) {
                $faviconUrl = $baseUrl . '/' . ltrim($faviconUrl, '/');
            }
            return $faviconUrl;
        }

        return "https://www.google.com/s2/favicons?domain={$parsedUrl['host']}&sz=32";
    }

    public function getConfig()
    {
        $config = GetLinkConfig::getInstance();
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'coins' => $config->coins,
            'user_coins' => $user ? $user->coins : 0,
            'is_logged_in' => Auth::check(),
        ]);
    }
}
