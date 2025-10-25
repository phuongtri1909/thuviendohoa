@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết giao dịch')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Giao dịch</a></li>
                <li class="breadcrumb-item current">Chi tiết giao dịch</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-money-bill-wave icon-title"></i>
                    <h5>Chi tiết giao dịch #{{ $payment->id }}</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.payments.index') }}" class="action-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="payment-details">
                            <h6 class="section-title">Thông tin giao dịch</h6>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <label>Mã giao dịch:</label>
                                    <span class="detail-value">{{ $payment->transaction_code }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Gói đăng ký:</label>
                                    <span class="detail-value package-badge">{{ ucfirst($payment->package_plan) }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Số xu nhận được:</label>
                                    <span class="detail-value coins-badge">{{ number_format($payment->coins) }} xu</span>
                                </div>
                                <div class="detail-item">
                                    <label>Số tiền:</label>
                                    <span class="detail-value amount-badge">{{ number_format($payment->amount) }} VNĐ</span>
                                </div>
                                <div class="detail-item">
                                    <label>Thời hạn gói:</label>
                                    <span class="detail-value">{{ $payment->expiry }} tháng</span>
                                </div>
                                <div class="detail-item">
                                    <label>Trạng thái:</label>
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
                                </div>
                                <div class="detail-item">
                                    <label>Ngày tạo:</label>
                                    <span class="detail-value">{{ $payment->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                @if($payment->processed_at)
                                    <div class="detail-item">
                                        <label>Ngày xử lý:</label>
                                        <span class="detail-value">{{ $payment->processed_at->format('d/m/Y H:i:s') }}</span>
                                    </div>
                                @endif
                                @if($payment->note)
                                    <div class="detail-item full-width">
                                        <label>Ghi chú:</label>
                                        <span class="detail-value">{{ $payment->note }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($payment->casso_response)
                            <div class="payment-details mt-4">
                                <h6 class="section-title">Phản hồi từ Casso</h6>
                                <div class="casso-response">
                                    <pre>{{ json_encode(json_decode($payment->casso_response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <div class="payment-details">
                            <h6 class="section-title">Thông tin người dùng</h6>
                            <div class="user-card">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-info">
                                    <h6>{{ $payment->user->name ?? 'N/A' }}</h6>
                                    <p class="text-muted">{{ $payment->user->email ?? 'N/A' }}</p>
                                    <div class="user-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Xu hiện tại:</span>
                                            <span class="stat-value">{{ number_format($payment->user->coins ?? 0) }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Gói hiện tại:</span>
                                            <span class="stat-value">
                                                @if($payment->user->package_id)
                                                    {{ $payment->user->package->name ?? 'N/A' }}
                                                @else
                                                    Chưa có gói
                                                @endif
                                            </span>
                                        </div>
                                        @if($payment->user->package_expired_at)
                                            <div class="stat-item">
                                                <span class="stat-label">Hết hạn:</span>
                                                <span class="stat-value {{ \Carbon\Carbon::parse($payment->user->package_expired_at)->isFuture() ? 'text-success' : 'text-danger' }}">
                                                    {{ \Carbon\Carbon::parse($payment->user->package_expired_at)->format('d/m/Y') }} 
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="payment-details mt-4">
                            <h6 class="section-title">Thông tin ngân hàng</h6>
                            <div class="bank-card">
                                <div class="bank-info">
                                    <h6>{{ $payment->bank->name ?? 'N/A' }}</h6>
                                    <p class="text-muted">{{ $payment->bank->account_number ?? 'N/A' }}</p>
                                    <p class="text-muted">{{ $payment->bank->account_name ?? 'N/A' }}</p>
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

        .detail-item.full-width {
            grid-column: 1 / -1;
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

        .package-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
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

        .amount-badge {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
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

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .status-cancelled {
            background: #d1ecf1;
            color: #0c5460;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .user-avatar {
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

        .user-info h6 {
            margin: 0;
            color: #333;
        }

        .user-stats {
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

        .bank-card {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .bank-info h6 {
            margin: 0;
            color: #333;
        }

        .casso-response {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
        }

        .casso-response pre {
            margin: 0;
            font-size: 12px;
            color: #333;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
@endpush
