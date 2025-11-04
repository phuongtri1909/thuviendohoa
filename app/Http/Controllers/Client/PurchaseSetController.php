<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Set;
use App\Models\PurchaseSet;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PurchaseSetController extends Controller
{
    protected GoogleDriveService $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    /**
     * Hàm check chung - tái sử dụng cho tất cả logic
     */
    private function validateDownloadConditions($user, $set)
    {
        if (!$set || $set->status !== Set::STATUS_ACTIVE) {
            return [
                'success' => false,
                'message' => 'File không tồn tại hoặc đã bị xóa',
                'code' => 'SET_NOT_FOUND'
            ];
        }

        // Check purchased
        if (!$set->isFree()) {
            if ($user->hasPurchasedSet($set->id)) {
                return [
                    'success' => true,
                    'can_download' => true,
                    'already_purchased' => true,
                    'message' => 'Bạn đã mua file này',
                    'set' => [
                        'id' => $set->id,
                        'name' => $set->name,
                        'type' => $set->type,
                        'price' => $set->price ?? 0
                    ]
                ];
            }
        }

        // Free set
        if ($set->isFree()) {
            if ($user->hasUnlimitedDownloads()) {
                return [
                    'success' => true,
                    'can_download' => true,
                    'is_free' => true,
                    'unlimited' => true,
                    'message' => 'File miễn phí - Tải ngay (VIP)',
                    'set' => [
                        'id' => $set->id,
                        'name' => $set->name,
                        'type' => $set->type,
                        'price' => 0
                    ]
                ];
            } elseif ($user->canDownloadFree()) {
                return [
                    'success' => true,
                    'can_download' => true,
                    'is_free' => true,
                    'free_downloads_left' => $user->free_downloads,
                    'message' => "File miễn phí - Còn {$user->free_downloads} lượt tải",
                    'set' => [
                        'id' => $set->id,
                        'name' => $set->name,
                        'type' => $set->type,
                        'price' => 0
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'can_download' => false,
                    'message' => 'Bạn đã hết lượt tải miễn phí. Vui lòng nạp gói để tiếp tục.',
                    'require_package' => true,
                    'code' => 'NO_FREE_DOWNLOADS'
                ];
            }
        }

        // Premium set
        $price = $set->price ?? 0;

        if ($user->package_id && !$user->hasValidPackage()) {
            return [
                'success' => false,
                'can_download' => false,
                'message' => 'Gói ' . $user->package->getPlanPluralName() . ' của bạn đã hết hạn. Vui lòng gia hạn.',
                'package_expired' => true,
                'code' => 'PACKAGE_EXPIRED'
            ];
        }

        if ($user->coins < $price) {
            return [
                'success' => false,
                'can_download' => false,
                'message' => "Bạn không đủ xu để mua file này. Cần {$price} xu, bạn có {$user->coins} xu.",
                'insufficient_coins' => true,
                'required_coins' => $price,
                'current_coins' => $user->coins,
                'code' => 'INSUFFICIENT_COINS'
            ];
        }

        return [
            'success' => true,
            'can_download' => true,
            'is_premium' => true,
            'requires_purchase' => true,
            'message' => "Mua file này với {$price} xu?",
            'set' => [
                'id' => $set->id,
                'name' => $set->name,
                'type' => $set->type,
                'price' => $price
            ],
            'user' => [
                'coins' => $user->coins,
                'remaining_coins' => $user->coins - $price
            ]
        ];
    }

    /**
     * Kiểm tra điều kiện để hiển thị modal xác nhận tải
     */
    public function checkDownloadCondition(Request $request, $setId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tải file',
                    'require_login' => true
                ], 401);
            }

            $set = Set::where('id', $setId)
                ->where('status', Set::STATUS_ACTIVE)
                ->first();

            $result = $this->validateDownloadConditions($user, $set);

            if (!$result['success']) {
                $statusCode = match ($result['code'] ?? null) {
                    'SET_NOT_FOUND' => 404,
                    'NO_FREE_DOWNLOADS', 'PACKAGE_EXPIRED', 'INSUFFICIENT_COINS' => 403,
                    default => 400
                };
                return response()->json($result, $statusCode);
            }

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error('Error checking download condition', [
                'error' => $e->getMessage(),
                'set_id' => $setId,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra điều kiện tải'
            ], 500);
        }
    }

    /**
     * Xác nhận mua và tải xuống
     */
    public function confirmPurchase(Request $request, $setId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tải file'
                ], 401);
            }

            if (!$request->boolean('user_confirmed')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng xác nhận hành động này'
                ], 400);
            }

            DB::beginTransaction();

            $set = Set::where('id', $setId)
                ->where('status', Set::STATUS_ACTIVE)
                ->lockForUpdate()
                ->first();

            $validation = $this->validateDownloadConditions($user, $set);

            if (!$validation['success']) {
                DB::rollBack();
                $statusCode = match ($validation['code'] ?? null) {
                    'SET_NOT_FOUND' => 404,
                    'NO_FREE_DOWNLOADS', 'PACKAGE_EXPIRED', 'INSUFFICIENT_COINS' => 403,
                    default => 400
                };
                return response()->json($validation, $statusCode);
            }

            // Đã mua -> tải trực tiếp
            if (!empty($validation['already_purchased'])) {
                DB::commit();
                return $this->processDownload($set);
            }

            // Xử lý giao dịch
            if ($set->isFree()) {
                if (!$user->hasUnlimitedDownloads() && $user->canDownloadFree()) {
                    $user->decrement('free_downloads');
                }
            } else {
                $price = $set->price ?? 0;
                $user->decrement('coins', $price);

                $purchase = PurchaseSet::create([
                    'user_id' => $user->id,
                    'set_id' => $set->id,
                    'coins' => $price
                ]);

                \App\Models\CoinHistory::create([
                    'user_id' => $user->id,
                    'amount' => -$price,
                    'type' => \App\Models\CoinHistory::TYPE_PURCHASE,
                    'source' => $purchase->id,
                    'reason' => 'Mua file premium',
                    'description' => "Mua file '{$set->name}' với {$price} xu",
                    'metadata' => json_encode([
                        'purchase_id' => $purchase->id,
                        'set_id' => $set->id,
                        'set_name' => $set->name,
                        'set_type' => $set->type
                    ])
                ]);
            }

            DB::commit();

            return $this->processDownload($set);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error confirming purchase', [
                'error' => $e->getMessage(),
                'set_id' => $setId,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý giao dịch'
            ], 500);
        }
    }

    /**
     * Tải file từ Google Drive (đã stream tối ưu)
     */
    private function processDownload($set)
    {
        try {
            set_time_limit(0);
            ignore_user_abort(true);
            
            // Tăng memory limit cho file lớn
            ini_set('memory_limit', '1024M');

            if (!$set->drive_url) {
                return response()->json([
                    'success' => false,
                    'message' => 'File này chưa có link Drive'
                ], 400);
            }

            Log::info('Download start', [
                'set_id' => $set->id,
                'user_id' => Auth::id()
            ]);

            $zipPath = $this->driveService->downloadFolderAsZip(
                $set->drive_url,
                $set->id,
                $set->name
            );

            if ($purchase = PurchaseSet::where('user_id', Auth::id())
                ->where('set_id', $set->id)
                ->first()) {
                $purchase->markAsDownloaded();
            }

            Log::info('Download ready', [
                'set_id' => $set->id,
                'zip' => $zipPath
            ]);

            try {
                return $this->driveService->streamZipDownload($zipPath, $set->name);
            } catch (\Throwable $e) {
                Log::warning('Client aborted download', [
                    'set_id' => $set->id,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tải xuống bị gián đoạn.'
                ], 499);
            }
        } catch (\Throwable $e) {
            Log::error('Error processing download', [
                'error' => $e->getMessage(),
                'set_id' => $set->id,
                'user_id' => Auth::id(),
                'drive_url' => $set->drive_url
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải file từ Drive: ' . $e->getMessage()
            ], 500);
        }
    }
}
