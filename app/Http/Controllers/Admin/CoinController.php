<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinTransaction;
use App\Models\User;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CoinController extends Controller
{
    public function index(Request $request)
    {
        $query = CoinTransaction::with(['user:id,full_name,email', 'admin:id,full_name,email'])
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(15)->withQueryString();

        // Get filter options
        $admins = User::where('role', 'admin')
            ->select('id', 'full_name', 'email')
            ->orderBy('full_name')
            ->get();

        $users = User::select('id', 'full_name', 'email')
            ->orderBy('full_name')
            ->get();

        $types = [
            CoinTransaction::TYPE_MANUAL => 'Thủ công',
            CoinTransaction::TYPE_PACKAGE_BONUS => 'Thưởng gói',
            CoinTransaction::TYPE_REFUND => 'Hoàn tiền',
            CoinTransaction::TYPE_PENALTY => 'Phạt'
        ];

        return view('admin.pages.coins.index', compact(
            'transactions',
            'admins',
            'users',
            'types'
        ));
    }

    public function create()
    {
        // Get users with their package info for selection
        $users = User::with(['package:id,name'])
            ->select('id', 'full_name', 'email', 'package_id', 'coins')
            ->orderBy('full_name')
            ->get();

        // Get packages for bulk operations
        $packages = Package::select('id', 'name', 'plan')
            ->orderBy('name')
            ->get();

        return view('admin.pages.coins.create', compact('users', 'packages'));
    }

    public function store(Request $request)
    {
        // Basic validation first
        $request->validate([
            'operation_type' => 'required|in:individual,package',
            'amount_type' => 'required|in:add,subtract',
            'amount' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000'
        ], [
            'operation_type.required' => 'Vui lòng chọn loại thao tác',
            'operation_type.in' => 'Loại thao tác không hợp lệ',
            'amount.required' => 'Vui lòng nhập số xu',
            'amount.integer' => 'Số xu phải là số nguyên',
            'amount.min' => 'Số xu phải lớn hơn 0',
            'reason.required' => 'Vui lòng nhập lý do',
            'reason.max' => 'Lý do không được quá 255 ký tự',
            'note.max' => 'Ghi chú không được quá 1000 ký tự'
        ]);
        
        // Additional validation based on operation type
        if ($request->operation_type === 'individual') {
            $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'required|exists:users,id'
            ], [
                'user_ids.required' => 'Vui lòng chọn ít nhất 1 người dùng',
                'user_ids.min' => 'Vui lòng chọn ít nhất 1 người dùng',
                'user_ids.*.required' => 'Vui lòng chọn người dùng hợp lệ',
                'user_ids.*.exists' => 'Người dùng không tồn tại'
            ]);
        } elseif ($request->operation_type === 'package') {
            $request->validate([
                'package_id' => 'required|exists:packages,id'
            ], [
                'package_id.required' => 'Vui lòng chọn gói',
                'package_id.exists' => 'Gói không tồn tại'
            ]);
        }

        try {
            DB::beginTransaction();

            $adminId = Auth::id();
            $amountType = $request->amount_type;
            $amount = $request->amount;
            $reason = $request->reason;
            $note = $request->note;
            
            // Xác định số xu thực tế (dương cho cộng, âm cho trừ)
            $actualAmount = $amountType === 'add' ? $amount : -$amount;

            if ($request->operation_type === 'individual') {
                // Cộng xu cho từng user riêng lẻ
                $userIds = $request->user_ids;
                
                // Tối ưu: Lấy tất cả user cần cập nhật trong 1 query
                $users = User::whereIn('id', $userIds)->get();
                
                $transactions = [];
                foreach ($users as $user) {
                    // Cập nhật coins
                    $user->increment('coins', $actualAmount);
                    
                    // Tạo transaction record
                    $transactions[] = [
                        'user_id' => $user->id,
                        'admin_id' => $adminId,
                        'amount' => $actualAmount,
                        'type' => CoinTransaction::TYPE_MANUAL,
                        'reason' => $reason,
                        'note' => $note,
                        'target_data' => json_encode(['user_ids' => $userIds]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                
                // Bulk insert transactions
                CoinTransaction::insert($transactions);
                
                $action = $amountType === 'add' ? 'cộng' : 'trừ';
                $message = "Đã {$action} {$amount} xu cho " . count($userIds) . " người dùng";
                
            } else {
                // Cộng xu cho tất cả user có package
                $packageId = $request->package_id;
                
                // Tối ưu: Lấy tất cả user có package này
                $users = User::where('package_id', $packageId)->get();
                
                if ($users->isEmpty()) {
                    return redirect()->back()
                        ->with('error', 'Không có người dùng nào sở hữu gói này')
                        ->withInput();
                }
                
                $transactions = [];
                foreach ($users as $user) {
                    // Cập nhật coins
                    $user->increment('coins', $actualAmount);
                    
                    // Tạo transaction record
                    $transactions[] = [
                        'user_id' => $user->id,
                        'admin_id' => $adminId,
                        'amount' => $actualAmount,
                        'type' => CoinTransaction::TYPE_PACKAGE_BONUS,
                        'reason' => $reason,
                        'note' => $note,
                        'target_data' => json_encode(['package_id' => $packageId]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                
                // Bulk insert transactions
                CoinTransaction::insert($transactions);
                
                $action = $amountType === 'add' ? 'cộng' : 'trừ';
                $message = "Đã {$action} {$amount} xu cho " . $users->count() . " người dùng có gói này";
            }

            DB::commit();

            return redirect()->route('admin.coins.index')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in coin store: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $transaction = CoinTransaction::with(['user', 'admin'])->findOrFail($id);
        
        return view('admin.pages.coins.show', compact('transaction'));
    }

    public function getPackageUsers($packageId)
    {
        try {
            $users = User::where('package_id', $packageId)
                ->select('id', 'full_name', 'email', 'coins')
                ->orderBy('full_name')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu'
            ], 500);
        }
    }

}