@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết giao dịch xu')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.coins.index') }}">Quản lý xu</a></li>
                <li class="breadcrumb-item current">Chi tiết giao dịch xu</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-coins icon-title"></i>
                    <h5>Chi tiết giao dịch xu #{{ $transaction->id }}</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.coins.index') }}" class="action-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="transaction-details">
                            <h6 class="section-title">Thông tin giao dịch</h6>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <label>ID giao dịch:</label>
                                    <span class="detail-value">{{ $transaction->id }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Số xu:</label>
                                    <span class="detail-value coins-badge {{ $transaction->amount > 0 ? 'positive' : 'negative' }}">
                                        {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }} xu
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Loại giao dịch:</label>
                                    <span class="detail-value">
                                        <span class="type-badge type-{{ $transaction->type }}">
                                            @switch($transaction->type)
                                                @case('manual')
                                                    Thủ công
                                                    @break
                                                @case('package_bonus')
                                                    Thưởng gói
                                                    @break
                                                @case('refund')
                                                    Hoàn tiền
                                                    @break
                                                @case('penalty')
                                                    Phạt
                                                    @break
                                                @default
                                                    {{ $transaction->type }}
                                            @endswitch
                                        </span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Lý do:</label>
                                    <span class="detail-value">{{ $transaction->reason }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Ghi chú:</label>
                                    <span class="detail-value">{{ $transaction->note ?: 'Không có ghi chú' }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Thời gian:</label>
                                    <span class="detail-value">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            </div>
                        </div>

                        @if($transaction->target_data)
                            <div class="transaction-details mt-4">
                                <h6 class="section-title">Thông tin đích</h6>
                                <div class="target-data">
                                    @if(isset($transaction->target_data['package_id']))
                                        <div class="target-item">
                                            <label>Gói được chọn:</label>
                                            <span class="detail-value">
                                                @php
                                                    $package = \App\Models\Package::find($transaction->target_data['package_id']);
                                                @endphp
                                                {{ $package ? $package->name : 'Gói không tồn tại' }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    @if(isset($transaction->target_data['user_ids']))
                                        <div class="target-item">
                                            <label>Số người dùng được chọn:</label>
                                            <span class="detail-value">{{ count($transaction->target_data['user_ids']) }} người</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <div class="transaction-details">
                            <h6 class="section-title">Thông tin người dùng</h6>
                            @if($transaction->user)
                                <div class="user-card">
                                    <div class="user-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="user-info">
                                        <h6>{{ $transaction->user->full_name }}</h6>
                                        <p class="text-muted">{{ $transaction->user->email }}</p>
                                        <div class="user-stats">
                                            <div class="stat-item">
                                                <span class="stat-label">Xu hiện tại:</span>
                                                <span class="stat-value">{{ number_format($transaction->user->coins ?? 0) }}</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-label">Gói hiện tại:</span>
                                                <span class="stat-value">
                                                    @if($transaction->user->package_id)
                                                        {{ $transaction->user->package->name ?? 'N/A' }}
                                                    @else
                                                        Chưa có gói
                                                    @endif
                                                </span>
                                            </div>
                                            @if($transaction->user->package_expired_at)
                                                <div class="stat-item">
                                                    <span class="stat-label">Hết hạn:</span>
                                                    <span class="stat-value {{ \Carbon\Carbon::parse($transaction->user->package_expired_at)->isFuture() ? 'text-success' : 'text-danger' }}">
                                                        {{ \Carbon\Carbon::parse($transaction->user->package_expired_at)->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('admin.users.show', $transaction->user->id) }}" class="btn btn-sm btn-outline-info mt-2">
                                            <i class="fas fa-user-circle"></i> Xem chi tiết người dùng
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">Người dùng này không còn tồn tại.</div>
                            @endif
                        </div>

                        <div class="transaction-details mt-4">
                            <h6 class="section-title">Thông tin admin</h6>
                            @if($transaction->admin)
                                <div class="admin-card">
                                    <div class="admin-avatar">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div class="admin-info">
                                        <h6>{{ $transaction->admin->full_name }}</h6>
                                        <p class="text-muted">{{ $transaction->admin->email }}</p>
                                        <div class="admin-stats">
                                            <div class="stat-item">
                                                <span class="stat-label">Vai trò:</span>
                                                <span class="stat-value">{{ $transaction->admin->role === 'admin' ? 'Admin' : 'User' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">Admin này không còn tồn tại.</div>
                            @endif
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

        .coins-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .coins-badge.positive {
            background: #d4edda;
            color: #155724;
        }

        .coins-badge.negative {
            background: #f8d7da;
            color: #721c24;
        }

        .type-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .type-badge.type-manual {
            background: #e3f2fd;
            color: #1976d2;
        }

        .type-badge.type-package_bonus {
            background: #fff3e0;
            color: #f57c00;
        }

        .type-badge.type-refund {
            background: #d4edda;
            color: #155724;
        }

        .type-badge.type-penalty {
            background: #f8d7da;
            color: #721c24;
        }

        .target-data {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }

        .target-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .target-item:last-child {
            border-bottom: none;
        }

        .target-item label {
            font-weight: 600;
            color: #6c757d;
            margin: 0;
        }

        .user-card, .admin-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .user-avatar, .admin-avatar {
            width: 50px;
            height: 50px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .admin-avatar {
            background: #28a745;
        }

        .user-info h6, .admin-info h6 {
            margin: 0;
            color: #333;
        }

        .user-stats, .admin-stats {
            margin-top: 10px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .stat-label {
            color: #6c757d;
        }

        .stat-value {
            font-weight: 600;
            color: #333;
        }

        .action-button.danger {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .action-button.danger:hover {
            background: #c82333;
            border-color: #bd2130;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
