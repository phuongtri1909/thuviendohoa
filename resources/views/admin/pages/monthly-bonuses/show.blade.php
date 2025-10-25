@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết cộng xu hàng tháng')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.monthly-bonuses.index') }}">Lịch sử cộng xu</a></li>
                <li class="breadcrumb-item current">Chi tiết cộng xu hàng tháng</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-calendar-alt icon-title"></i>
                    <h5>Chi tiết cộng xu hàng tháng #{{ $monthlyBonus->id }}</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.monthly-bonuses.index') }}" class="action-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="transaction-details">
                            <h6 class="section-title">Thông tin cộng xu hàng tháng</h6>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <label>ID:</label>
                                    <span class="detail-value">{{ $monthlyBonus->id }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Gói:</label>
                                    <span class="detail-value">
                                        <span class="package-badge">{{ $monthlyBonus->package->name }}</span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Tháng:</label>
                                    <span class="detail-value">
                                        <span class="month-badge">{{ $monthlyBonus->formatted_month }}</span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Số user:</label>
                                    <span class="detail-value">
                                        <span class="users-badge">{{ number_format($monthlyBonus->total_users) }}</span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Tổng xu:</label>
                                    <span class="detail-value">
                                        <span class="coins-badge positive">{{ $monthlyBonus->total_coins_formatted }}</span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Xu/user:</label>
                                    <span class="detail-value">{{ number_format($monthlyBonus->bonus_per_user) }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Thời gian xử lý:</label>
                                    <span class="detail-value">{{ $monthlyBonus->processed_at_formatted }}</span>
                                </div>
                                @if($monthlyBonus->notes)
                                    <div class="detail-item full-width">
                                        <label>Ghi chú:</label>
                                        <span class="detail-value">{{ $monthlyBonus->notes }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Danh sách user được cộng xu -->
                        <div class="transaction-details mt-4">
                            <h6 class="section-title">Danh sách user được cộng xu ({{ $users->count() }})</h6>
                            
                            @if($users->count() > 0)
                                <div class="data-table-container">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th class="column-small">STT</th>
                                                <th class="column-medium">Tên</th>
                                                <th class="column-medium">Email</th>
                                                <th class="column-small">Xu hiện tại</th>
                                                <th class="column-medium">Hết hạn gói</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $index => $user)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>
                                                        <div class="user-info">
                                                            <strong>{{ $user->full_name }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="user-info">
                                                            <small class="text-muted">{{ $user->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="coins-badge positive">{{ number_format($user->coins) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($user->package_expired_at)
                                                            <span class="{{ \Carbon\Carbon::parse($user->package_expired_at)->isFuture() ? 'text-success' : 'text-danger' }}">
                                                                {{ \Carbon\Carbon::parse($user->package_expired_at)->format('d/m/Y') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h4>Không có user nào</h4>
                                    <p>Không có user nào được cộng xu trong lần này.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="transaction-details">
                            <h6 class="section-title">Thông tin gói</h6>
                            <div class="package-card">
                                <div class="package-avatar">
                                    <i class="fas fa-gem"></i>
                                </div>
                                <div class="package-info">
                                    <h6>{{ $monthlyBonus->package->name }}</h6>
                                    <p class="text-muted">{{ $monthlyBonus->package->plan }}</p>
                                    <div class="package-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Bonus xu:</span>
                                            <span class="stat-value">{{ number_format($monthlyBonus->package->bonus_coins) }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Giá gói:</span>
                                            <span class="stat-value">{{ number_format($monthlyBonus->package->amount) }} VNĐ</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Xu gói:</span>
                                            <span class="stat-value">{{ number_format($monthlyBonus->package->coins) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="transaction-details mt-4">
                            <h6 class="section-title">Thống kê</h6>
                            <div class="stats-card">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ $monthlyBonus->formatted_month }}</div>
                                        <div class="stat-label">Tháng thực hiện</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ number_format($monthlyBonus->total_users) }}</div>
                                        <div class="stat-label">Số user</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ $monthlyBonus->total_coins_formatted }}</div>
                                        <div class="stat-label">Tổng xu</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ $monthlyBonus->processed_at->format('H:i') }}</div>
                                        <div class="stat-label">Thời gian xử lý</div>
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
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-item label {
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .detail-value {
        font-weight: 500;
        color: #555;
    }

    .package-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .month-badge {
        background: #e8f5e8;
        color: #2e7d32;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .users-badge {
        background: #f3e5f5;
        color: #7b1fa2;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .coins-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .coins-badge.positive {
        background: #d4edda;
        color: #155724;
    }

    .package-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }

    .package-avatar {
        width: 50px;
        height: 50px;
        background: #e3f2fd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1976d2;
        font-size: 20px;
    }

    .package-info h6 {
        margin: 0 0 5px 0;
        color: #333;
    }

    .package-info p {
        margin: 0 0 10px 0;
        color: #6c757d;
    }

    .package-stats {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
    }

    .stat-label {
        color: #6c757d;
    }

    .stat-value {
        font-weight: 600;
        color: #333;
    }

    .stats-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
    }

    .stats-card .stat-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .stats-card .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        background: #e3f2fd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1976d2;
    }

    .stat-content {
        flex: 1;
    }

    .stat-number {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .stat-label {
        font-size: 12px;
        color: #6c757d;
        margin: 0;
    }
</style>
@endpush