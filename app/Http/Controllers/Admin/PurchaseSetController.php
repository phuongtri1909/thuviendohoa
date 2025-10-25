<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseSet;
use App\Models\User;
use App\Models\Set;
use Illuminate\Http\Request;

class PurchaseSetController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseSet::with(['user', 'set'])
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by set
        if ($request->filled('set_id')) {
            $query->where('set_id', $request->set_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by download status
        if ($request->filled('downloaded')) {
            if ($request->downloaded === 'yes') {
                $query->whereNotNull('downloaded_at');
            } elseif ($request->downloaded === 'no') {
                $query->whereNull('downloaded_at');
            }
        }

        $purchases = $query->paginate(15)->withQueryString();

        // Get filter options
        $users = User::select('id', 'full_name', 'email')->orderBy('full_name')->get();
        $sets = Set::select('id', 'name')->orderBy('name')->get();

        return view('admin.pages.purchase-sets.index', compact(
            'purchases',
            'users',
            'sets'
        ));
    }

    public function show($id)
    {
        $purchase = PurchaseSet::with(['user', 'set'])->findOrFail($id);
        
        return view('admin.pages.purchase-sets.show', compact('purchase'));
    }
}
