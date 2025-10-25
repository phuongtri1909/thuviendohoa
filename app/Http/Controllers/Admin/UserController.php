<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PaymentCasso;
use App\Models\PurchaseSet;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['package'])
            ->orderBy('created_at', 'desc');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        // Filter by package
        if ($request->filled('package_id')) {
            if ($request->package_id === 'none') {
                $query->whereNull('package_id');
            } else {
                $query->where('package_id', $request->package_id);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        // Get filter options
        $packages = \App\Models\Package::select('id', 'name')->orderBy('name')->get();
        $roles = [
            User::ROLE_ADMIN => 'Admin',
            User::ROLE_USER => 'User'
        ];
        $activeStatuses = [
            User::ACTIVE_YES => 'Hoạt động',
            User::ACTIVE_NO => 'Không hoạt động'
        ];

        return view('admin.pages.users.index', compact(
            'users',
            'packages',
            'roles',
            'activeStatuses'
        ));
    }

    public function show($id)
    {
        $user = User::with(['package', 'favorites', 'purchasedSets.set'])
            ->findOrFail($id);
        
        // Get user's payment history
        $payments = PaymentCasso::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'payments_page');
        
        // Get user's purchase history
        $purchases = PurchaseSet::with('set')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'purchases_page');
        
        // Get user's coin transactions
        $coinTransactions = \App\Models\CoinTransaction::with('admin')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'coin_transactions_page');
        
        $monthlyBonuses = \App\Models\MonthlyBonus::with('package')
            ->whereJsonContains('user_ids', (string)$id)
            ->orderBy('processed_at', 'desc')
            ->paginate(10, ['*'], 'monthly_bonuses_page');
        
        // Get user's coin histories
        $coinHistories = \App\Models\CoinHistory::with('admin')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'coin_histories_page');
        
        return view('admin.pages.users.show', compact('user', 'payments', 'purchases', 'coinTransactions', 'monthlyBonuses', 'coinHistories'));
    }
}
