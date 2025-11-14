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
                    'price' => $set->price ?? 0,
                    'can_use_free_downloads' => $set->can_use_free_downloads ?? false
                ]
            ];
        }

        if ($set->isFree()) {
            return [
                'success' => true,
                'can_download' => true,
                'is_free' => true,
                'no_charge' => true,
                'message' => 'File miễn phí - Tải ngay',
                'set' => [
                    'id' => $set->id,
                    'name' => $set->name,
                    'type' => $set->type,
                    'price' => 0,
                    'can_use_free_downloads' => false
                ]
            ];
        }

        $price = $set->price ?? 0;
        $downloadMethod = $set->download_method ?? Set::DOWNLOAD_METHOD_COINS_ONLY;
        $canUseFreeDownloads = $set->canUseFreeDownloads();
        $canUseCoins = $set->canUseCoins();
        $hasFreeDownloads = $user->canDownloadFree();

        if ($downloadMethod === Set::DOWNLOAD_METHOD_FREE_ONLY) {
            if (!$hasFreeDownloads) {
                return [
                    'success' => false,
                    'can_download' => false,
                    'message' => "File này chỉ có thể tải bằng lượt miễn phí. Bạn đã hết lượt tải miễn phí. Vui lòng nạp gói để tiếp tục.",
                    'no_free_downloads' => true,
                    'code' => 'NO_FREE_DOWNLOADS'
                ];
            }

            return [
                'success' => true,
                'can_download' => true,
                'is_premium' => true,
                'requires_free_download' => true,
                'free_downloads_left' => $user->free_downloads,
                'message' => "File này chỉ có thể tải bằng lượt miễn phí. Bạn còn {$user->free_downloads} lượt.",
                'set' => [
                    'id' => $set->id,
                    'name' => $set->name,
                    'type' => $set->type,
                    'price' => $price,
                    'download_method' => $downloadMethod
                ],
                'user' => [
                    'free_downloads' => $user->free_downloads
                ]
            ];
        }

        if ($downloadMethod === Set::DOWNLOAD_METHOD_COINS_ONLY) {
            if (!$user->canPurchaseWithCoins()) {
                if (!$user->package_id) {
                    return [
                        'success' => false,
                        'can_download' => false,
                        'message' => "File này chỉ có thể mua bằng xu. Bạn cần mua gói để có thể mua file bằng xu.",
                        'no_package' => true,
                        'code' => 'NO_PACKAGE'
                    ];
                } else {
                    return [
                        'success' => false,
                        'can_download' => false,
                        'message' => "Gói của bạn đã hết hạn. Vui lòng nạp gói mới để tiếp tục mua file bằng xu.",
                        'package_expired' => true,
                        'code' => 'PACKAGE_EXPIRED'
                    ];
                }
            }

            if ($user->coins < $price) {
                return [
                    'success' => false,
                    'can_download' => false,
                    'message' => "File này chỉ có thể mua bằng xu. Bạn không đủ xu để mua file này. Cần {$price} xu, bạn có {$user->coins} xu.",
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
                'message' => "File này chỉ có thể mua bằng xu. Mua file này với {$price} xu?",
                'set' => [
                    'id' => $set->id,
                    'name' => $set->name,
                    'type' => $set->type,
                    'price' => $price,
                    'download_method' => $downloadMethod
                ],
                'user' => [
                    'coins' => $user->coins,
                    'remaining_coins' => $user->coins - $price
                ]
            ];
        }

        if ($canUseFreeDownloads && $hasFreeDownloads) {
            $canPurchaseWithCoins = $user->canPurchaseWithCoins() && $user->coins >= $price;
            
            return [
                'success' => true,
                'can_download' => true,
                'is_premium' => true,
                'has_multiple_options' => true,
                'can_use_free_download' => true,
                'can_use_coins' => $canPurchaseWithCoins,
                'free_downloads_left' => $user->free_downloads,
                'message' => $canPurchaseWithCoins 
                    ? "Bạn có thể mua file này với {$price} xu hoặc dùng 1 lượt miễn phí (còn {$user->free_downloads} lượt)"
                    : "Bạn có thể dùng 1 lượt miễn phí để tải file này (còn {$user->free_downloads} lượt)",
                'set' => [
                    'id' => $set->id,
                    'name' => $set->name,
                    'type' => $set->type,
                    'price' => $price,
                    'download_method' => $downloadMethod
                ],
                'user' => [
                    'coins' => $user->coins,
                    'free_downloads' => $user->free_downloads,
                    'remaining_coins' => $user->coins - $price
                ]
            ];
        }

        if ($canUseFreeDownloads && !$hasFreeDownloads) {
            if (!$user->canPurchaseWithCoins()) {
                if (!$user->package_id) {
                    return [
                        'success' => false,
                        'can_download' => false,
                        'message' => "Bạn đã hết lượt tải miễn phí. Bạn cần mua gói để có thể mua file bằng xu.",
                        'no_package' => true,
                        'no_free_downloads' => true,
                        'code' => 'NO_PACKAGE'
                    ];
                } else {
                    return [
                        'success' => false,
                        'can_download' => false,
                        'message' => "Bạn đã hết lượt tải miễn phí và gói của bạn đã hết hạn. Vui lòng nạp gói mới để tiếp tục mua file bằng xu.",
                        'package_expired' => true,
                        'no_free_downloads' => true,
                        'code' => 'PACKAGE_EXPIRED'
                    ];
                }
            }

            if ($user->coins < $price) {
                return [
                    'success' => false,
                    'can_download' => false,
                    'message' => "Bạn đã hết lượt tải miễn phí và không đủ xu để mua file này. Cần {$price} xu, bạn có {$user->coins} xu.",
                    'insufficient_coins' => true,
                    'no_free_downloads' => true,
                    'required_coins' => $price,
                    'current_coins' => $user->coins,
                    'code' => 'INSUFFICIENT_RESOURCES'
                ];
            }

            return [
                'success' => true,
                'can_download' => true,
                'is_premium' => true,
                'requires_purchase' => true,
                'message' => "Bạn đã hết lượt tải miễn phí. Mua file này với {$price} xu?",
                'set' => [
                    'id' => $set->id,
                    'name' => $set->name,
                    'type' => $set->type,
                    'price' => $price,
                    'download_method' => $downloadMethod
                ],
                'user' => [
                    'coins' => $user->coins,
                    'remaining_coins' => $user->coins - $price
                ]
            ];
        }
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
                'trace' => $e->getTraceAsString(),
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

            try {
                $set = Set::where('id', $setId)
                    ->where('status', Set::STATUS_ACTIVE)
                    ->lockForUpdate()
                    ->first();

                $validation = $this->validateDownloadConditions($user, $set);

                if (!$validation['success']) {
                    DB::rollBack();
                    $statusCode = match ($validation['code'] ?? null) {
                        'SET_NOT_FOUND' => 404,
                        'NO_FREE_DOWNLOADS', 'PACKAGE_EXPIRED', 'NO_PACKAGE', 'INSUFFICIENT_COINS' => 403,
                        default => 400
                    };
                    return response()->json($validation, $statusCode);
                }

                if (!empty($validation['already_purchased'])) {
                    DB::commit();
                    return $this->processDownload($set, $user);
                }

                $downloadMethod = $set->download_method ?? Set::DOWNLOAD_METHOD_COINS_ONLY;

                if ($set->isFree()) {
                    $paymentMethod = 'free';
                } elseif ($downloadMethod === Set::DOWNLOAD_METHOD_FREE_ONLY) {
                    $paymentMethod = 'free_download';
                } elseif ($downloadMethod === Set::DOWNLOAD_METHOD_COINS_ONLY) {
                    $paymentMethod = 'coins';
                } else {
                    $requestPaymentMethod = $request->input('payment_method');
                    
                    if (in_array($requestPaymentMethod, ['coins', 'free_download'])) {
                        $paymentMethod = $requestPaymentMethod;
                    } else {
                        $paymentMethod = 'coins';
                    }
                }

                if ($set->isFree()) {
                } else {
                    $price = $set->price ?? 0;

                    if ($paymentMethod === 'free_download') {
                        if (!$set->canUseFreeDownloads()) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'File này không cho phép dùng lượt miễn phí'
                            ], 400);
                        }
                        
                        $user->refresh();
                        
                        if (!$user->canDownloadFree()) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'Bạn đã hết lượt tải miễn phí. Có thể bạn đã tải file khác ở tab khác.'
                            ], 403);
                        }
                    } elseif ($paymentMethod === 'coins') {
                        if (!$set->canUseCoins()) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'File này không cho phép mua bằng xu'
                            ], 400);
                        }
                        
                        $user->refresh();
                        
                        if (!$user->canPurchaseWithCoins()) {
                            DB::rollBack();
                            if (!$user->package_id) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Bạn cần mua gói để có thể mua file bằng xu.'
                                ], 403);
                            } else {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Gói của bạn đã hết hạn. Vui lòng nạp gói mới để tiếp tục mua file bằng xu.'
                                ], 403);
                            }
                        }
                        
                        if ($user->coins < $price) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Bạn không đủ xu để mua file này. Cần {$price} xu, bạn hiện có {$user->coins} xu. Có thể bạn đã mua file khác ở tab khác."
                            ], 403);
                        }
                    }

                    if ($paymentMethod === 'free_download') {
                        $user->decrement('free_downloads');

                        $purchase = PurchaseSet::create([
                            'user_id' => $user->id,
                            'set_id' => $set->id,
                            'coins' => 0,
                            'payment_method' => 'free_download'
                        ]);

                        \App\Models\CoinHistory::create([
                            'user_id' => $user->id,
                            'amount' => 0,
                            'type' => \App\Models\CoinHistory::TYPE_FREE_DOWNLOAD,
                            'source' => $purchase->id,
                            'reason' => 'Tải file bằng lượt miễn phí',
                            'description' => "Tải file '{$set->name}' bằng lượt miễn phí (còn {$user->free_downloads} lượt)",
                            'metadata' => json_encode([
                                'purchase_id' => $purchase->id,
                                'set_id' => $set->id,
                                'set_name' => $set->name,
                                'set_type' => $set->type,
                                'payment_method' => 'free_download'
                            ])
                        ]);
                    } else {
                        $user->decrement('coins', $price);

                        $purchase = PurchaseSet::create([
                            'user_id' => $user->id,
                            'set_id' => $set->id,
                            'coins' => $price,
                            'payment_method' => 'coins'
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
                                'set_type' => $set->type,
                                'payment_method' => 'coins'
                            ])
                        ]);
                    }
                }

                DB::commit();

                return $this->processDownload($set, $user);

            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::error('Error confirming purchase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'set_id' => $setId,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý giao dịch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tải file từ Google Drive (optimized với streaming)
     */
    private function processDownload($set, $user)
    {
        try {
            set_time_limit(0);
            ignore_user_abort(false);
            ini_set('memory_limit', '512M');

            if (!$set->drive_url) {
                return response()->json([
                    'success' => false,
                    'message' => 'File này chưa có link Drive'
                ], 400);
            }

            Log::info('Download start', [
                'set_id' => $set->id,
                'set_name' => $set->name,
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            $zipPath = $this->driveService->downloadFolderAsZip(
                $set->drive_url,
                $set->id,
                $set->name
            );

            if ($purchase = PurchaseSet::where('user_id', $user->id)
                ->where('set_id', $set->id)
                ->first()) {
                $purchase->markAsDownloaded();
            }

            Log::info('Download ready', [
                'set_id' => $set->id,
                'zip_path' => $zipPath,
                'zip_size' => file_exists($zipPath) ? filesize($zipPath) : 0,
                'user_id' => $user->id
            ]);

            return $this->driveService->streamZipDownload($zipPath, $set->name);

        } catch (\Throwable $e) {
            Log::error('Error processing download', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'set_id' => $set->id,
                'user_id' => $user->id,
                'drive_url' => $set->drive_url
            ]);

            if (connection_aborted()) {
                Log::info('Client aborted download', [
                    'set_id' => $set->id,
                    'user_id' => $user->id
                ]);
                return;
            }

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải file: ' . $e->getMessage()
            ], 500);
        }
    }
}