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
    /**
     * Hàm check chung - tái sử dụng cho tất cả logic
     */
    private function validateDownloadConditions($user, $set)
    {
        // Check set exists and active
        if (!$set || $set->status !== Set::STATUS_ACTIVE) {
            return [
                'success' => false,
                'message' => 'File không tồn tại hoặc đã bị xóa',
                'code' => 'SET_NOT_FOUND'
            ];
        }

        // Check if already purchased (only for premium sets)
        if (!$set->isFree()) {
            $hasPurchased = $user->hasPurchasedSet($set->id);
            if ($hasPurchased) {
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

        // Check conditions based on set type
        if ($set->isFree()) {
            if ($user->hasUnlimitedDownloads()) {
                // User có package active - tải free không giới hạn
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
                // User thường hoặc VIP hết hạn nhưng còn free_downloads
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
                // Hết lượt tải free
                return [
                    'success' => false,
                    'can_download' => false,
                    'message' => 'Bạn đã hết lượt tải miễn phí. Vui lòng nạp gói để tiếp tục.',
                    'require_package' => true,
                    'code' => 'NO_FREE_DOWNLOADS'
                ];
            }
        } else {
            // Premium set
            $price = $set->price ?? 0;

            if ($user->package_id && !$user->hasValidPackage()) {
                return [
                    'success' => false,
                    'can_download' => false,
                    'message' => 'Gói '.$user->package->getPlanPluralName().' của bạn đã hết hạn. Vui lòng gia hạn.',
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
    }

    /**
     * Kiểm tra điều kiện và trả về thông tin set để hiển thị modal xác nhận
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

            // Sử dụng hàm check chung
            $result = $this->validateDownloadConditions($user, $set);
            
            if (!$result['success']) {
                $statusCode = match($result['code']) {
                    'SET_NOT_FOUND' => 404,
                    'NO_FREE_DOWNLOADS', 'PACKAGE_EXPIRED', 'INSUFFICIENT_COINS' => 403,
                    default => 400
                };
                return response()->json($result, $statusCode);
            }

            return response()->json($result);

        } catch (\Exception $e) {
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
     * Xác nhận mua và tải xuống trực tiếp
     * Tích hợp download vào confirmPurchase để tăng security
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

            // Validate CSRF token (automatically checked by Laravel middleware)
            // Require explicit user confirmation
            if (!$request->has('user_confirmed') || !$request->input('user_confirmed')) {
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

            // Sử dụng hàm check chung để validate lại
            $validationResult = $this->validateDownloadConditions($user, $set);
            
            if (!$validationResult['success']) {
                DB::rollBack();
                $statusCode = match($validationResult['code']) {
                    'SET_NOT_FOUND' => 404,
                    'NO_FREE_DOWNLOADS', 'PACKAGE_EXPIRED', 'INSUFFICIENT_COINS' => 403,
                    default => 400
                };
                return response()->json($validationResult, $statusCode);
            }

            // Nếu đã mua rồi, chỉ cần download
            if (isset($validationResult['already_purchased']) && $validationResult['already_purchased']) {
                DB::commit();
                return $this->processDownload($set);
            }

            // Xử lý purchase logic
            if ($set->isFree()) {
                if ($user->hasUnlimitedDownloads()) {
                    // User có package - không trừ free_downloads, không tạo PurchaseSet
                    // Chỉ download trực tiếp
                } elseif ($user->canDownloadFree()) {
                    // User thường - trừ free_downloads, không tạo PurchaseSet
                    $user->free_downloads -= 1;
                    $user->save();
                }
            } else {
                // Premium set - trừ coins và tạo PurchaseSet
                $price = $set->price ?? 0;
                $user->coins -= $price;
                $user->save();

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

            // Tích hợp download trực tiếp
            return $this->processDownload($set);

        } catch (\Exception $e) {
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
     * Xử lý download file từ Google Drive
     * Private method để tái sử dụng
     */
    private function processDownload($set)
    {
        try {
            // Check if set has drive_url
            if (!$set->drive_url) {
                return response()->json([
                    'success' => false,
                    'message' => 'File này chưa có link Drive'
                ], 400);
            }

            $driveService = new GoogleDriveService();
            
            // Download folder as ZIP
            $zipPath = $driveService->downloadFolderAsZip(
                $set->drive_url, 
                $set->id, 
                $set->name
            );

            // Mark as downloaded
            $purchase = PurchaseSet::where('user_id', Auth::id())
                ->where('set_id', $set->id)
                ->first();
            
            if ($purchase) {
                $purchase->markAsDownloaded();
            }

            // Stream ZIP file to client
            return $driveService->streamZipDownload($zipPath, $set->name . '.zip');

        } catch (\Exception $e) {
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
