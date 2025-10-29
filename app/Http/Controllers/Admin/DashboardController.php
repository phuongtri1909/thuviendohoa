<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentCasso;
use App\Models\PurchaseSet;
use App\Models\MonthlyBonus;
use App\Models\GetLinkHistory;
use App\Models\CoinHistory;
use App\Models\CoinTransaction;
use App\Models\User;
use App\Models\Set;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $revenueStats = $this->getRevenueStats();
        $coinStats = $this->getCoinStats();
        $transactionStats = $this->getTransactionStats();
        $userStats = $this->getUserStats();

        // Biểu đồ 7 ngày gần nhất
        $chartData = $this->getWeeklyChartData();

        // Biểu đồ doanh thu 30 ngày
        $revenueChartData = $this->getMonthlyRevenueChart();

        // Thống kê chi tiết giao dịch
        $transactionDetails = $this->getTransactionDetails();

        // Top sets bán chạy nhất
        $topSets = $this->getTopSets();

        // Top packages
        $topPackages = $this->getTopPackages();

        // Thống kê theo loại giao dịch
        $coinTransactionsByType = $this->getCoinTransactionsByType();

        return view('admin.pages.dashboard', compact(
            'revenueStats',
            'coinStats',
            'transactionStats',
            'userStats',
            'chartData',
            'revenueChartData',
            'transactionDetails',
            'topSets',
            'topPackages',
            'coinTransactionsByType'
        ));
    }

    private function getRevenueStats()
    {
        $now = Carbon::now();
        
        return [
            'total' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)->sum('amount'),
            'today' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                ->whereDate('created_at', $now->toDateString())
                ->sum('amount'),
            'this_week' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                ->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])
                ->sum('amount'),
            'this_month' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->sum('amount'),
            'last_month' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                ->whereMonth('created_at', $now->subMonth()->month)
                ->whereYear('created_at', $now->year)
                ->sum('amount'),
        ];
    }

    private function getCoinStats()
    {
        $now = Carbon::now();
        
        // Tổng xu đã phát hành (từ payment + manual)
        $totalIssued = CoinHistory::where('amount', '>', 0)->sum('amount');
        
        // Tổng xu đã tiêu
        $totalSpent = CoinHistory::where('amount', '<', 0)->sum('amount');
        
        // Xu trong hệ thống (tổng coins của users)
        $totalInSystem = User::sum('coins');

        return [
            'total_issued' => $totalIssued,
            'total_spent' => abs($totalSpent),
            'total_in_system' => $totalInSystem,
            'today_issued' => CoinHistory::where('amount', '>', 0)
                ->whereDate('created_at', $now->toDateString())
                ->sum('amount'),
            'today_spent' => abs(CoinHistory::where('amount', '<', 0)
                ->whereDate('created_at', $now->toDateString())
                ->sum('amount')),
        ];
    }

    private function getTransactionStats()
    {
        $now = Carbon::now();
        
        return [
            'total_payments' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)->count(),
            'total_purchases' => PurchaseSet::count(),
            'total_getlinks' => GetLinkHistory::count(),
            'today_payments' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                ->whereDate('created_at', $now->toDateString())
                ->count(),
            'today_purchases' => PurchaseSet::whereDate('created_at', $now->toDateString())->count(),
            'today_getlinks' => GetLinkHistory::whereDate('created_at', $now->toDateString())->count(),
        ];
    }

    private function getUserStats()
    {
        $now = Carbon::now();
        
        return [
            'total_users' => User::where('role', User::ROLE_USER)->count(),
            'active_package_users' => User::where('role', User::ROLE_USER)
                ->whereNotNull('package_id')
                ->where('package_expired_at', '>', $now)
                ->count(),
            'new_today' => User::where('role', User::ROLE_USER)
                ->whereDate('created_at', $now->toDateString())
                ->count(),
            'new_this_month' => User::where('role', User::ROLE_USER)
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(),
        ];
    }

    private function getWeeklyChartData()
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $data[] = [
                'date' => $date->format('d/m'),
                'revenue' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('amount'),
                'purchases' => PurchaseSet::whereDate('created_at', $date->toDateString())->count(),
                'users' => User::where('role', User::ROLE_USER)
                    ->whereDate('created_at', $date->toDateString())
                    ->count(),
            ];
        }
        
        return $data;
    }

    private function getMonthlyRevenueChart()
    {
        $data = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $revenue = PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                ->whereDate('created_at', $date->toDateString())
                ->sum('amount');
            
            $data[] = $revenue;
        }
        
        return $data;
    }

    private function getTransactionDetails()
    {
        $now = Carbon::now();
        
        return [
            'payments' => [
                'today' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                    ->whereDate('created_at', $now->toDateString())
                    ->count(),
                'this_week' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                    ->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
                    ->count(),
                'this_month' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->count(),
                'total' => PaymentCasso::where('status', PaymentCasso::STATUS_SUCCESS)->count(),
            ],
            'purchases' => [
                'today' => PurchaseSet::whereDate('created_at', $now->toDateString())->count(),
                'this_week' => PurchaseSet::whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                'this_month' => PurchaseSet::whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->count(),
                'total' => PurchaseSet::count(),
            ],
            'getlinks' => [
                'today' => GetLinkHistory::whereDate('created_at', $now->toDateString())->count(),
                'this_week' => GetLinkHistory::whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
                'this_month' => GetLinkHistory::whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->count(),
                'total' => GetLinkHistory::count(),
            ],
            'monthly_bonus' => [
                'total_distributed' => MonthlyBonus::sum('total_coins'),
                'this_month' => MonthlyBonus::where('month', $now->format('Y-m'))->sum('total_coins'),
                'total_events' => MonthlyBonus::count(),
            ],
        ];
    }

    private function getTopSets($limit = 10)
    {
        return Set::select('sets.*', DB::raw('COUNT(purchase_sets.id) as purchase_count'), DB::raw('SUM(purchase_sets.coins) as total_coins'))
            ->leftJoin('purchase_sets', 'sets.id', '=', 'purchase_sets.set_id')
            ->groupBy('sets.id')
            ->orderByDesc('purchase_count')
            ->limit($limit)
            ->get();
    }

    private function getTopPackages()
    {
        return Package::select('packages.*', DB::raw('COUNT(payment_cassos.id) as payment_count'), DB::raw('SUM(payment_cassos.amount) as total_amount'))
            ->leftJoin('payment_cassos', function($join) {
                $join->on('packages.plan', '=', 'payment_cassos.package_plan')
                    ->where('payment_cassos.status', '=', PaymentCasso::STATUS_SUCCESS);
            })
            ->groupBy('packages.id')
            ->orderByDesc('payment_count')
            ->get();
    }

    private function getCoinTransactionsByType()
    {
        return CoinHistory::select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => [
                    'count' => $item->count,
                    'total' => $item->total
                ]];
            });
    }
}
