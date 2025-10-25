@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết người dùng')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                <li class="breadcrumb-item current">Chi tiết người dùng</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-user icon-title"></i>
                    <h5>Chi tiết người dùng #{{ $user->id }}</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.users.index') }}" class="action-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="user-details">
                            <h6 class="section-title">Thông tin người dùng</h6>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <label>Họ tên:</label>
                                    <span class="detail-value">{{ $user->full_name ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Email:</label>
                                    <span class="detail-value">{{ $user->email ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Vai trò:</label>
                                    <span class="detail-value">
                                        <span class="role-badge role-{{ $user->role }}">
                                            {{ $user->role === 'admin' ? 'Admin' : 'User' }}
                                        </span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Trạng thái:</label>
                                    <span class="detail-value">
                                        <span class="status-badge status-{{ $user->active ? 'active' : 'inactive' }}">
                                            {{ $user->active ? 'Hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Xu hiện tại:</label>
                                    <span class="detail-value coins-badge">{{ number_format($user->coins ?? 0) }} xu</span>
                                </div>
                                <div class="detail-item">
                                    <label>Lượt tải miễn phí:</label>
                                    <span class="detail-value">{{ $user->free_downloads ?? 0 }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Gói hiện tại:</label>
                                    <span class="detail-value">
                                        @if($user->package_id)
                                            <span class="package-badge">{{ $user->package->name ?? 'N/A' }}</span>
                                        @else
                                            <span class="text-muted">Chưa có gói</span>
                                        @endif
                                    </span>
                                </div>
                                @if($user->package_expired_at)
                                    <div class="detail-item">
                                        <label>Hết hạn gói:</label>
                                        <span class="detail-value {{ $user->package_expired_at->isFuture() ? 'text-success' : 'text-danger' }}">
                                            {{ $user->package_expired_at->format('d/m/Y H:i:s') }}
                                        </span>
                                    </div>
                                @endif
                                <div class="detail-item">
                                    <label>Ngày tạo:</label>
                                    <span class="detail-value">{{ $user->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History -->
                        <div class="user-details mt-4">
                            <h6 class="section-title">Lịch sử thanh toán</h6>
                            @if($payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Mã giao dịch</th>
                                                <th>Gói</th>
                                                <th>Số xu</th>
                                                <th>Số tiền</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->transaction_code }}</td>
                                                    <td>{{ ucfirst($payment->package_plan) }}</td>
                                                    <td>{{ number_format($payment->coins) }}</td>
                                                    <td>{{ number_format($payment->amount) }} VNĐ</td>
                                                    <td>
                                                        <span class="status-badge status-{{ $payment->status }}">
                                                            @switch($payment->status)
                                                                @case('pending')
                                                                    <i class="fas fa-clock"></i> Chờ xử lý
                                                                    @break
                                                                @case('success')
                                                                    <i class="fas fa-check"></i> Thành công
                                                                    @break
                                                                @case('failed')
                                                                    <i class="fas fa-times"></i> Thất bại
                                                                    @break
                                                                @case('cancelled')
                                                                    <i class="fas fa-ban"></i> Đã hủy
                                                                    @break
                                                            @endswitch
                                                        </span>
                                                    </td>
                                                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $payments->links('components.paginate') }}
                                </div>
                            @else
                                <p class="text-muted">Chưa có giao dịch thanh toán nào.</p>
                            @endif
                        </div>

                        <!-- Purchase History -->
                        <div class="user-details mt-4">
                            <h6 class="section-title">Lịch sử mua file</h6>
                            @if($purchases->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>File</th>
                                                <th>Loại</th>
                                                <th>Số xu</th>
                                                <th>Trạng thái tải</th>
                                                <th>Ngày mua</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchases as $purchase)
                                                <tr>
                                                    <td>{{ $purchase->set->name ?? 'File đã bị xóa' }}</td>
                                                    <td>
                                                        @if($purchase->set)
                                                            <span class="type-badge {{ $purchase->set->type === 'free' ? 'free' : 'premium' }}">
                                                                {{ $purchase->set->type === 'free' ? 'Miễn phí' : 'Premium' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($purchase->coins) }}</td>
                                                    <td>
                                                        <span class="download-status {{ $purchase->downloaded_at ? 'downloaded' : 'not-downloaded' }}">
                                                            @if($purchase->downloaded_at)
                                                                <i class="fas fa-check-circle"></i> Đã tải
                                                            @else
                                                                <i class="fas fa-clock"></i> Chưa tải
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $purchases->links('components.paginate') }}
                                </div>
                            @else
                                <p class="text-muted">Chưa mua file nào.</p>
                            @endif
                        </div>

                        <!-- Coin Transactions -->
                        <div class="user-details mt-4">
                            <h6 class="section-title">Lịch sử cộng/trừ xu thủ công</h6>
                            @if($coinTransactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Loại</th>
                                                <th>Số xu</th>
                                                <th>Lý do</th>
                                                <th>Admin</th>
                                                <th>Ngày</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($coinTransactions as $transaction)
                                                <tr>
                                                    <td>
                                                        <span class="transaction-type {{ $transaction->amount > 0 ? 'add' : 'subtract' }}">
                                                            @if($transaction->amount > 0)
                                                                <i class="fas fa-plus"></i> Cộng xu
                                                            @else
                                                                <i class="fas fa-minus"></i> Trừ xu
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge {{ $transaction->amount > 0 ? 'positive' : 'negative' }}">
                                                            {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $transaction->reason }}</td>
                                                    <td>{{ $transaction->admin->full_name ?? 'N/A' }}</td>
                                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $coinTransactions->links('components.paginate') }}
                                </div>
                            @else
                                <p class="text-muted">Chưa có giao dịch cộng/trừ xu thủ công nào.</p>
                            @endif
                        </div>

                        <!-- Monthly Bonuses -->
                        <div class="user-details mt-4">
                            <h6 class="section-title">Lịch sử cộng xu hàng tháng</h6>
                            @if($monthlyBonuses->count() > 0)
                                <div class="table-container">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Tháng</th>
                                                <th>Gói</th>
                                                <th>Số xu</th>
                                                <th>Thời gian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyBonuses as $bonus)
                                                <tr>
                                                    <td>{{ $bonus->formatted_month }}</td>
                                                    <td>
                                                        <span class="package-badge">{{ $bonus->package->name }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge positive">+{{ number_format($bonus->bonus_per_user) }}</span>
                                                    </td>
                                                    <td>{{ $bonus->processed_at_formatted }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pagination-wrapper">
                                    {{ $monthlyBonuses->links('components.paginate') }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <p class="text-muted">Chưa có lịch sử cộng xu hàng tháng nào.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="user-details">
                            <h6 class="section-title">Thống kê</h6>
                            <div class="stats-card">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ $payments->total() }}</div>
                                        <div class="stat-label">Giao dịch thanh toán</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ $purchases->total() }}</div>
                                        <div class="stat-label">File đã mua</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ $user->favorites->count() }}</div>
                                        <div class="stat-label">File yêu thích</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ number_format($user->coins ?? 0) }}</div>
                                        <div class="stat-label">Xu hiện tại</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ $coinTransactions->total() }}</div>
                                        <div class="stat-label">Giao dịch xu thủ công</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .section-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-item label {
            font-weight: 600;
            color: #6c757d;
            font-size: 14px;
        }

        .detail-value {
            font-size: 16px;
            color: #333;
        }

        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-badge.role-admin {
            background: #f8d7da;
            color: #721c24;
        }

        .role-badge.role-user {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge.status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.status-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.status-cancelled {
            background: #d1ecf1;
            color: #0c5460;
        }

        .package-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .coins-badge {
            background: #fff3e0;
            color: #f57c00;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .type-badge.free {
            background: #d4edda;
            color: #155724;
        }

        .type-badge.premium {
            background: #fff3e0;
            color: #f57c00;
        }

        .download-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .download-status.downloaded {
            background: #d4edda;
            color: #155724;
        }

        .download-status.not-downloaded {
            background: #fff3cd;
            color: #856404;
        }

        .transaction-type {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .transaction-type.add {
            background: #d4edda;
            color: #155724;
        }

        .transaction-type.subtract {
            background: #f8d7da;
            color: #721c24;
        }

        .amount-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .amount-badge.positive {
            background: #d4edda;
            color: #155724;
        }

        .amount-badge.negative {
            background: #f8d7da;
            color: #721c24;
        }

        .stats-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-item:last-child {
            margin-bottom: 0;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
