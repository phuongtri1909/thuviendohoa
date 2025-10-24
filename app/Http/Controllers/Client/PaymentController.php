<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Package;
use App\Models\PaymentCasso;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $packages = Package::orderBy('amount', 'asc')->get();
        
        return view('client.pages.user.payment.index', compact(
            'user',
            
            'packages'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);

        $package = Package::findOrFail($request->package_id);
        $bank = Bank::first();
        
        if (!$bank) {
            Log::error('No bank found');
            return response()->json([
                'success' => false,
                'message' => 'Hệ thống không phản hồi, vui lòng thử lại sau'
            ], 400);
        }
        
        $transactionCode = 'VIETFILE' . time() . strtoupper(Str::random(6)) . Auth::id();
        
        DB::beginTransaction();
        try {
            $payment = PaymentCasso::create([
                'user_id' => Auth::id(),
                'bank_id' => $bank->id,
                'transaction_code' => $transactionCode,
                'package_plan' => $package->plan,
                'coins' => $package->coins,
                'amount' => $package->amount,
                'expiry' => $package->expiry,
                'status' => PaymentCasso::STATUS_PENDING
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'transaction_code' => $transactionCode,
                'amount' => $package->amount,
                'coins' => $package->coins,
                'package_name' => $package->name,
                'expiry' => $package->expiry,
                'bank_info' => [
                    'name' => $bank->name,
                    'code' => $bank->code,
                    'account_number' => $bank->account_number,
                    'account_name' => $bank->account_name,
                    'qr_code' => $bank->qr_code ? asset('storage/' . $bank->qr_code) : null,
                ],
                'message' => 'Vui lòng chuyển khoản theo thông tin bên dưới'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating payment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo giao dịch'
            ], 500);
        }
    }

    public function sse(Request $request)
    {
        $transactionCode = $request->query('transaction_code');
        
        if (!$transactionCode) {
            return response('Hệ thống không phản hồi, vui lòng thử lại sau', 400);
        }
        
        return response()->stream(function () use ($transactionCode) {
            $filename = storage_path('app/sse_payment_' . $transactionCode . '.json');
            $lastModified = 0;
            
            while (true) {
                if (file_exists($filename)) {
                    $currentModified = filemtime($filename);
                    
                    if ($currentModified > $lastModified) {
                        $data = json_decode(file_get_contents($filename), true);
                        
                        echo "data: " . json_encode($data) . "\n\n";
                        
                        $lastModified = $currentModified;
                        
                        if ($data['status'] === 'success') {
                            echo "data: " . json_encode(['type' => 'close']) . "\n\n";
                            break;
                        }
                    }
                }
                
                sleep(1);
                
                if (connection_aborted()) {
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Cache-Control',
        ]);
    }

    public function cassoCallback(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Casso-Signature');
        
        if (!$signature) {
            Log::warning('Mã xác thực không tồn tại, vui lòng thử lại sau');
            return response()->json(['success' => false, 'message' => 'Hệ thống không phản hồi, vui lòng thử lại sau'], 401);
        }
        
        if (!$this->verifyCassoSignature($payload, $signature)) {
            Log::warning('Mã xác thực không hợp lệ, vui lòng thử lại sau', [
                'signature' => $signature,
                'payload_preview' => substr($payload, 0, 100)
            ]);
            return response()->json(['success' => false, 'message' => 'Mã xác thực không hợp lệ, vui lòng thử lại sau'], 401);
        }
        
        $data = json_decode($payload, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON payload', ['error' => json_last_error_msg()]);
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ, vui lòng thử lại sau'], 400);
        }
        
        $transactionId = $data['data']['id'] ?? null;
        $reference = $data['data']['reference'] ?? null;
        $description = $data['data']['description'] ?? '';
        $amount = $data['data']['amount'] ?? 0;
        $accountNumber = $data['data']['accountNumber'] ?? '';
        $bankName = $data['data']['bankName'] ?? '';
        $transactionDateTime = $data['data']['transactionDateTime'] ?? null;
        
        if (!$transactionId) {
            Log::warning('Mã giao dịch không tồn tại, vui lòng thử lại sau', ['data' => $data]);
            return response()->json(['success' => false, 'message' => 'Mã giao dịch không tồn tại, vui lòng thử lại sau']);
        }
        
        $existingPayment = PaymentCasso::where('casso_transaction_id', $transactionId)
            ->where('status', PaymentCasso::STATUS_SUCCESS)
            ->first();
            
        if ($existingPayment) {
            return response()->json(['success' => true, 'message' => 'Giao dịch đã được xử lý, vui lòng thử lại sau']);
        }
        
        DB::beginTransaction();
        try {
            $transactionCode = null;
            
            if (preg_match_all('/(VIETFILE[a-zA-Z0-9]{14,})/', $description, $matches)) {
                $transactionCode = $matches[1][0];
            }
            
            $payment = null;
            if ($transactionCode) {
                $payment = PaymentCasso::where('transaction_code', $transactionCode)
                    ->where('status', PaymentCasso::STATUS_PENDING)
                    ->first();
            }
                
            if (!$payment) {
                Log::warning('Giao dịch không tồn tại, vui lòng thử lại sau', [
                    'reference' => $reference,
                    'transaction_id' => $transactionId,
                    'description' => $description,
                    'extracted_code' => $transactionCode
                ]);
                return response()->json(['success' => false, 'message' => 'Giao dịch không tồn tại, vui lòng thử lại sau']);
            }
            
            if ($amount < $payment->amount) {
                Log::warning('Số tiền nhận được không đủ, vui lòng thử lại sau', [
                    'expected' => $payment->amount,
                    'received' => $amount,
                    'reference' => $reference,
                    'description' => $description
                ]);
                
                $payment->update([
                    'status' => PaymentCasso::STATUS_FAILED,
                    'note' => 'Số tiền nhận được không đủ',
                    'casso_response' => $data
                ]);
                
                DB::commit();
                return response()->json(['success' => false, 'message' => 'Số tiền nhận được không đủ, vui lòng thử lại sau']);
            }
            
            $payment->update([
                'status' => PaymentCasso::STATUS_SUCCESS,
                'processed_at' => now(),
                'casso_transaction_id' => $transactionId,
                'casso_response' => $data
            ]);
            
            $this->broadcastPaymentUpdate($transactionCode, 'success', $payment);
            
            $user = $payment->user;
            if ($user) {
                $user->increment('coins', $payment->coins);
                
                $currentPackage = $user->package_id;
                $newPackage = $payment->package_plan;
                
                $packageHierarchy = [
                    Package::PLAN_BRONZE => 1,
                    Package::PLAN_SILVER => 2,
                    Package::PLAN_GOLD => 3,
                    Package::PLAN_PLATINUM => 4,
                ];
                
                $currentLevel = $packageHierarchy[$currentPackage] ?? 0;
                $newLevel = $packageHierarchy[$newPackage] ?? 0;
                
                if ($newLevel > $currentLevel) {
                    $currentExpiry = $user->package_expired_at;
                    
                    if ($currentExpiry && Carbon::parse($currentExpiry)->isFuture()) {
                        $baseDate = Carbon::parse($currentExpiry);
                    } else {
                        $baseDate = now();
                    }
                    
                    $newExpiry = $baseDate->addMonths($payment->expiry);
                    
                    $user->update([
                        'package_id' => $newPackage,
                        'package_expired_at' => $newExpiry
                    ]);
                    
                }
                
                Log::info('Giao dịch thành công', [
                    'user_id' => $user->id,
                    'transaction_code' => $transactionCode,
                    'casso_transaction_id' => $transactionId,
                    'coins_added' => $payment->coins,
                    'amount_received' => $amount,
                    'bank_name' => $bankName,
                    'description' => $description
                ]);
            }
            
            DB::commit();
            
            return response()->json(['success' => true], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xảy ra khi xử lý giao dịch: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
                'description' => $description
            ]);
            return response()->json(['success' => false, 'message' => 'Lỗi xảy ra khi xử lý giao dịch, vui lòng thử lại sau']);
        }
    }

    private function verifyCassoSignature($payload, $signature)
    {
        $secret = config('services.casso.webhook_secret');
        
        if (!$secret) {
            Log::error('Mã xác thực không được cấu hình');
            return false;
        }
        
        if (!preg_match('/t=(\d+),v1=(.+)/', $signature, $matches)) {
            Log::warning('Mã xác thực không hợp lệ, vui lòng thử lại sau', ['signature' => $signature]);
            return false;
        }
        
        $timestamp = $matches[1];
        $receivedSignature = $matches[2];
        
        $currentTime = time() * 1000;
        $signatureTime = (int)$timestamp;
        $timeDiff = abs($currentTime - $signatureTime);
        
        if ($timeDiff > 300000) {
            Log::warning('Mã xác thực hết hạn, vui lòng thử lại sau', [
                'current_time' => $currentTime,
                'signature_time' => $signatureTime,
                'time_diff' => $timeDiff
            ]);
            return false;
        }
        
        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Dữ liệu không hợp lệ, vui lòng thử lại sau', ['error' => json_last_error_msg()]);
            return false;
        }
        
        $sortedData = $this->sortDataByKey($data);
        
        $messageToSign = $timestamp . '.' . json_encode($sortedData, JSON_UNESCAPED_SLASHES);
        
        $expectedSignature = hash_hmac('sha512', $messageToSign, $secret);
        
        return hash_equals($expectedSignature, $receivedSignature);
    }
    
    /**
     * Sắp xếp dữ liệu theo key
     */
    private function sortDataByKey($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        
        $sortedData = [];
        $keys = array_keys($data);
        sort($keys);
        
        foreach ($keys as $key) {
            if (is_array($data[$key])) {
                $sortedData[$key] = $this->sortDataByKey($data[$key]);
            } else {
                $sortedData[$key] = $data[$key];
            }
        }
        
        return $sortedData;
    }
    
    /**
     */
    private function broadcastPaymentUpdate($transactionCode, $status, $payment)
    {
        $sseData = [
            'type' => 'payment',
            'status' => $status,
            'transaction_code' => $transactionCode,
            'coins' => $payment->coins,
            'amount' => $payment->amount,
            'timestamp' => now()->toISOString(),
        ];
        
        $filename = storage_path('app/sse_payment_' . $transactionCode . '.json');
        file_put_contents($filename, json_encode($sseData));
    }
}
