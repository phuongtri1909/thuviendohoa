<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentCasso;
use App\Models\User;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentCassoController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentCasso::with(['user', 'bank'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show success, failed, cancelled (not pending)
            $query->whereIn('status', [
                PaymentCasso::STATUS_SUCCESS,
                PaymentCasso::STATUS_FAILED,
                PaymentCasso::STATUS_CANCELLED
            ]);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by bank
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->paginate(15)->withQueryString();

        // Get filter options
        $users = User::select('id', 'full_name', 'email')->orderBy('full_name')->get();
        $banks = Bank::select('id', 'name')->orderBy('name')->get();
        $statuses = [
            PaymentCasso::STATUS_PENDING => 'Chờ xử lý',
            PaymentCasso::STATUS_SUCCESS => 'Thành công',
            PaymentCasso::STATUS_FAILED => 'Thất bại',
            PaymentCasso::STATUS_CANCELLED => 'Đã hủy'
        ];

        return view('admin.pages.payments.index', compact(
            'payments',
            'users',
            'banks',
            'statuses'
        ));
    }

    public function show($id)
    {
        $payment = PaymentCasso::with(['user', 'bank'])->findOrFail($id);
        
        return view('admin.pages.payments.show', compact('payment'));
    }

    public function destroy($id)
    {
        $payment = PaymentCasso::findOrFail($id);
        
        // Only allow deletion of pending payments
        if ($payment->status !== PaymentCasso::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Chỉ có thể xóa giao dịch đang chờ xử lý.');
        }
        
        $payment->delete();
        
        return redirect()->back()->with('success', 'Giao dịch đã được xóa thành công.');
    }
}
