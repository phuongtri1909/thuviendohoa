@extends('admin.layouts.sidebar')

@section('title', 'Dashboard')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item current">Dashboard</li>
            </ol>
        </div>

        <!-- Stats Cards -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-chart-line icon-title"></i>
                    <h5>Thống kê tổng quan</h5>
                </div>
            </div>
            <div class="card-content">
                <div class="stats-grid">
                    <!-- Total Revenue -->
                    <div class="stat-card highlight">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($revenueStats['total']) }} ₫</h3>
                            <p>Tổng doanh thu</p>
                        </div>
                    </div>

                    <!-- Today Revenue -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($revenueStats['today']) }} ₫</h3>
                            <p>Doanh thu hôm nay</p>
                        </div>
                    </div>

                    <!-- This Month Revenue -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($revenueStats['this_month']) }} ₫</h3>
                            <p>Doanh thu tháng này</p>
                        </div>
                    </div>

                    <!-- Total Coins Issued -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($coinStats['total_issued']) }}</h3>
                            <p>Tổng xu đã phát hành</p>
                        </div>
                    </div>

                    <!-- Coins in System -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($coinStats['total_in_system']) }}</h3>
                            <p>Xu trong hệ thống</p>
                        </div>
                    </div>

                    <!-- Coins Spent -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($coinStats['total_spent']) }}</h3>
                            <p>Tổng xu đã tiêu</p>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($userStats['total_users']) }}</h3>
                            <p>Tổng người dùng</p>
                        </div>
                    </div>

                    <!-- Active Package Users -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($userStats['active_package_users']) }}</h3>
                            <p>Người dùng có gói VIP</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-chart-bar icon-title"></i>
                    <h5>Biểu đồ thống kê</h5>
                </div>
            </div>
            <div class="card-content">
                <div class="charts-grid">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h6>Thống kê 7 ngày gần nhất</h6>
                        </div>
                        <div class="chart-content">
                            <canvas id="dashboardChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-header">
                            <h6>Doanh thu 30 ngày gần nhất</h6>
                        </div>
                        <div class="chart-content">
                            <canvas id="revenueChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-info-circle icon-title"></i>
                    <h5>Thống kê chi tiết giao dịch</h5>
                </div>
            </div>
            <div class="card-content">
                <div class="content-stats-grid">
                    <!-- Payment Stats -->
                    <div class="content-stat-card">
                        <div class="stat-card-header">
                            <h6><i class="fas fa-credit-card"></i> Nạp tiền/Mua gói</h6>
                        </div>
                        <div class="stat-card-content">
                            <div class="stat-item">
                                <span class="label">Hôm nay:</span>
                                <span class="value">{{ $transactionDetails['payments']['today'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tuần này:</span>
                                <span class="value">{{ $transactionDetails['payments']['this_week'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tháng này:</span>
                                <span class="value">{{ $transactionDetails['payments']['this_month'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tổng:</span>
                                <span class="value">{{ $transactionDetails['payments']['total'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Stats -->
                    <div class="content-stat-card">
                        <div class="stat-card-header">
                            <h6><i class="fas fa-shopping-bag"></i> Mua Sets</h6>
                        </div>
                        <div class="stat-card-content">
                            <div class="stat-item">
                                <span class="label">Hôm nay:</span>
                                <span class="value">{{ $transactionDetails['purchases']['today'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tuần này:</span>
                                <span class="value">{{ $transactionDetails['purchases']['this_week'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tháng này:</span>
                                <span class="value">{{ $transactionDetails['purchases']['this_month'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tổng:</span>
                                <span class="value">{{ $transactionDetails['purchases']['total'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- GetLink Stats -->
                    <div class="content-stat-card">
                        <div class="stat-card-header">
                            <h6><i class="fas fa-link"></i> Get Link</h6>
                        </div>
                        <div class="stat-card-content">
                            <div class="stat-item">
                                <span class="label">Hôm nay:</span>
                                <span class="value">{{ $transactionDetails['getlinks']['today'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tuần này:</span>
                                <span class="value">{{ $transactionDetails['getlinks']['this_week'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tháng này:</span>
                                <span class="value">{{ $transactionDetails['getlinks']['this_month'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Tổng:</span>
                                <span class="value">{{ $transactionDetails['getlinks']['total'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Bonus Stats -->
                    <div class="content-stat-card">
                        <div class="stat-card-header">
                            <h6><i class="fas fa-gift"></i> Thưởng hàng tháng</h6>
                        </div>
                        <div class="stat-card-content">
                            <div class="stat-item">
                                <span class="label">Tổng xu đã tặng:</span>
                                <span class="value">{{ number_format($transactionDetails['monthly_bonus']['total_distributed']) }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Xu tặng tháng này:</span>
                                <span class="value">{{ number_format($transactionDetails['monthly_bonus']['this_month']) }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Số lần tặng:</span>
                                <span class="value">{{ $transactionDetails['monthly_bonus']['total_events'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Sets -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-trophy icon-title"></i>
                    <h5>Top Sets bán chạy nhất</h5>
                </div>
            </div>
            <div class="card-content">
                @if($topSets->count() > 0)
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">Thứ hạng</th>
                                    <th class="column-large">Tên Set</th>
                                    <th class="column-medium">Lượt mua</th>
                                    <th class="column-medium">Tổng xu</th>
                                    <th class="column-medium">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSets as $index => $set)
                                    <tr>
                                        <td class="text-center">
                                            <span class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $set->name }}</strong>
                                            <small class="text-muted">{{ $set->type === 'premium' ? 'Premium' : 'Free' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ number_format($set->purchase_count) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ number_format($set->total_coins) }}</span>
                                        </td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $topSets->first()->purchase_count > 0 ? ($set->purchase_count / $topSets->first()->purchase_count) * 100 : 0 }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h4>Chưa có dữ liệu</h4>
                        <p>Chưa có giao dịch mua sets nào.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Packages -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-box icon-title"></i>
                    <h5>Thống kê gói nạp</h5>
                </div>
            </div>
            <div class="card-content">
                @if($topPackages->count() > 0)
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-medium">Gói</th>
                                    <th class="column-medium">Giá</th>
                                    <th class="column-medium">Lượt mua</th>
                                    <th class="column-medium">Doanh thu</th>
                                    <th class="column-medium">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPackages as $package)
                                    <tr>
                                        <td>
                                            <span class="package-badge {{ $package->getPlanColor() }}">
                                                {{ $package->getPlanPluralName() }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($package->amount) }} ₫</td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ number_format($package->payment_count) }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($package->total_amount) }} ₫</strong>
                                        </td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $topPackages->max('payment_count') > 0 ? ($package->payment_count / $topPackages->max('payment_count')) * 100 : 0 }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <h4>Chưa có dữ liệu</h4>
                        <p>Chưa có giao dịch mua gói nào.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Coin Transactions by Type -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-exchange-alt icon-title"></i>
                    <h5>Giao dịch xu theo loại</h5>
                </div>
            </div>
            <div class="card-content">
                @if(count($coinTransactionsByType) > 0)
                    <div class="coin-transactions-grid">
                        @foreach($coinTransactionsByType as $type => $data)
                            <div class="coin-transaction-card">
                                <div class="coin-transaction-icon">
                                    @switch($type)
                                        @case('payment')
                                            <i class="fas fa-credit-card"></i>
                                            @break
                                        @case('purchase')
                                            <i class="fas fa-shopping-cart"></i>
                                            @break
                                        @case('manual')
                                            <i class="fas fa-hand-holding-usd"></i>
                                            @break
                                        @case('monthly_bonus')
                                            <i class="fas fa-gift"></i>
                                            @break
                                        @case('getlink')
                                            <i class="fas fa-link"></i>
                                            @break
                                        @case('free_download')
                                            <i class="fas fa-download"></i>
                                            @break
                                        @default
                                            <i class="fas fa-coins"></i>
                                    @endswitch
                                </div>
                                <div class="coin-transaction-content">
                                    <h6>
                                        @switch($type)
                                            @case('payment')
                                                Nạp tiền
                                                @break
                                            @case('purchase')
                                                Mua file
                                                @break
                                            @case('manual')
                                                Thủ công
                                                @break
                                            @case('monthly_bonus')
                                                Thưởng tháng
                                                @break
                                            @case('getlink')
                                                Get link
                                                @break
                                            @case('free_download')
                                                Dùng lượt miễn phí
                                                @break
                                            @default
                                                {{ ucfirst($type) }}
                                        @endswitch
                                    </h6>
                                    <p class="count">{{ number_format($data['count']) }} giao dịch</p>
                                    <p class="total {{ $data['total'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $data['total'] >= 0 ? '+' : '' }}{{ number_format($data['total']) }} xu
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h4>Chưa có dữ liệu</h4>
                        <p>Chưa có giao dịch xu nào.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-card.highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: #007bff;
            color: white;
        }

        .stat-card.highlight .stat-icon {
            background: rgba(255,255,255,0.2);
        }

        .stat-content h3 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .stat-card.highlight .stat-content h3 {
            color: white;
        }

        .stat-content p {
            margin: 5px 0 0 0;
            color: #6c757d;
            font-size: 13px;
        }

        .stat-card.highlight .stat-content p {
            color: rgba(255,255,255,0.8);
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .chart-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e9ecef;
        }

        .chart-header h6 {
            margin: 0 0 15px 0;
            color: #333;
            font-weight: 600;
        }

        .content-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .content-stat-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e9ecef;
        }

        .stat-card-header h6 {
            margin: 0 0 15px 0;
            color: #333;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-card-content .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .stat-card-content .stat-item:last-child {
            border-bottom: none;
        }

        .stat-item .label {
            color: #6c757d;
            font-size: 14px;
        }

        .stat-item .value {
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }

        .rank-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            font-size: 14px;
        }

        .rank-badge.rank-1 {
            background: #ffd700;
            color: #333;
        }

        .rank-badge.rank-2 {
            background: #c0c0c0;
            color: #333;
        }

        .rank-badge.rank-3 {
            background: #cd7f32;
            color: white;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #007bff, #0056b3);
            transition: width 0.3s ease;
        }

        .package-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
        }

        .color-bronze {
            background: linear-gradient(180deg, #e6a07a 0%, #b4633e 100%);
            color: white;
        }

        .color-silver {
            background: linear-gradient(180deg, #bfbfbf 0%, #666666 100%);
            color: #333;
        }

        .color-gold {
            background: linear-gradient(180deg, #FFD964 0%, #ECB818 100%);
            color: #333;
        }

        .color-platinum {
            background: linear-gradient(180deg, #d680f7 0%, #a832db 100%);
            color: #333;
        }

        .coin-transactions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .coin-transaction-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e9ecef;
            text-align: center;
        }

        .coin-transaction-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 20px;
        }

        .coin-transaction-content h6 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .coin-transaction-content .count {
            margin: 0;
            font-size: 12px;
            color: #6c757d;
        }

        .coin-transaction-content .total {
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: bold;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state h4 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .empty-state p {
            margin: 0;
            font-size: 14px;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-muted {
            color: #6c757d !important;
            display: block;
            font-size: 11px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .bg-primary {
            background-color: #007bff;
            color: white;
        }

        .bg-success {
            background-color: #28a745;
            color: white;
        }

        .bg-info {
            background-color: #17a2b8;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .chart-container {
                padding: 15px;
            }
            
            .content-stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .stat-content h3 {
                font-size: 20px;
            }

            .coin-transactions-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .stat-content h3 {
                font-size: 18px;
            }
            
            .stat-content p {
                font-size: 12px;
            }
            
            .chart-header h6 {
                font-size: 14px;
            }
            
            .stat-card-header h6 {
                font-size: 14px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart data
        const chartData = @json($chartData);
        const revenueChartData = @json($revenueChartData);
        
        // Create main dashboard chart
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(item => item.date),
                datasets: [{
                    label: 'Doanh thu',
                    data: chartData.map(item => item.revenue),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                }, {
                    label: 'Số lượt mua',
                    data: chartData.map(item => item.purchases),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }, {
                    label: 'User mới',
                    data: chartData.map(item => item.users),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                                } else {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Doanh thu (₫)'
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Create revenue chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        
        // Generate labels for 30 days
        const revenueLabels = [];
        for (let i = 29; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            revenueLabels.push(date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' }));
        }
        
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revenueChartData,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: '#007bff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Ngày'
                        },
                        ticks: {
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Doanh thu (₫)'
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
