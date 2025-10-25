<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinHistory;
use App\Models\User;
use Illuminate\Http\Request;

class CoinHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = CoinHistory::with(['user:id,full_name,email', 'admin:id,full_name,email'])
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

        $histories = $query->paginate(15)->withQueryString();

        // Get filter options
        $admins = User::where('role', 'admin')
            ->select('id', 'full_name', 'email')
            ->orderBy('full_name')
            ->get();

        $users = User::select('id', 'full_name', 'email')
            ->orderBy('full_name')
            ->get();

        $types = [
            CoinHistory::TYPE_PAYMENT => 'Nạp tiền',
            CoinHistory::TYPE_PURCHASE => 'Mua file',
            CoinHistory::TYPE_MANUAL => 'Thủ công',
            CoinHistory::TYPE_MONTHLY_BONUS => 'Thưởng tháng'
        ];

        return view('admin.pages.coin-histories.index', compact(
            'histories',
            'admins',
            'users',
            'types'
        ));
    }

    public function show($id)
    {
        $history = CoinHistory::with(['user', 'admin'])->findOrFail($id);
        
        return view('admin.pages.coin-histories.show', compact('history'));
    }

}