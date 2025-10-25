<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyBonus;
use App\Models\Package;
use Illuminate\Http\Request;

class MonthlyBonusController extends Controller
{
    public function index(Request $request)
    {
        $query = MonthlyBonus::with('package');

        // Filter by package
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('package', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $monthlyBonuses = $query->orderBy('processed_at', 'desc')->paginate(15)->withQueryString();

        // Get filter options
        $packages = Package::select('id', 'name')->orderBy('name')->get();
        
        // Get available months
        $months = MonthlyBonus::select('month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month')
            ->map(function($month) {
                return [
                    'value' => $month,
                    'label' => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('m/Y')
                ];
            });

        return view('admin.pages.monthly-bonuses.index', compact(
            'monthlyBonuses',
            'packages',
            'months'
        ));
    }

    public function show($id)
    {
        $monthlyBonus = MonthlyBonus::with('package')->findOrFail($id);

        // Get user details for the user_ids in this monthly bonus
        $users = \App\Models\User::whereIn('id', $monthlyBonus->user_ids)
            ->select('id', 'full_name', 'email', 'coins', 'package_expired_at')
            ->get();

        return view('admin.pages.monthly-bonuses.show', compact('monthlyBonus', 'users'));
    }
}