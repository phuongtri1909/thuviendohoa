@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết lịch sử xu')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-history icon-title"></i>
                    <h5>Chi tiết lịch sử xu #{{ $history->id }}</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.coin-histories.index') }}" class="action-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="transaction-details">
                            <h6 class="section-title">Thông tin giao dịch xu</h6>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <label>ID:</label>
                                    <span class="detail-value">{{ $history->id }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Loại giao dịch:</label>
                                    <span class="detail-value">
                                        <span class="type-badge type-{{ $history->type }}">
                                            {{ $history->type_label }}
                                        </span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Số xu:</label>
                                    <span class="detail-value">
                                        <span class="amount-badge {{ $history->amount > 0 ? 'positive' : 'negative' }}">
                                            {{ $history->formatted_amount }}
                                        </span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Lý do:</label>
                                    <span class="detail-value">{{ $history->reason }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Mô tả:</label>
                                    <span class="detail-value">{{ $history->description ?? 'Không có mô tả' }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Nguồn:</label>
                                    <span class="detail-value">{{ $history->source }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Thời gian tạo:</label>
                                    <span class="detail-value">{{ $history->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Cập nhật lần cuối:</label>
                                    <span class="detail-value">{{ $history->updated_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Metadata -->
                        @if($history->metadata)
                            <div class="transaction-details mt-4">
                                <h6 class="section-title">Thông tin bổ sung</h6>
                                <div class="metadata-container">
                                    <pre class="metadata-json">{{ json_encode($history->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <!-- User Info -->
                        <div class="user-details">
                            <h6 class="section-title">Thông tin người dùng</h6>
                            <div class="user-card">
                                <div class="user-info">
                                    <h6>{{ $history->user->full_name ?? 'N/A' }}</h6>
                                    <p class="text-muted">{{ $history->user->email ?? 'N/A' }}</p>
                                    <div class="user-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Xu hiện tại:</span>
                                            <span class="stat-value">{{ number_format($history->user->coins ?? 0) }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Gói hiện tại:</span>
                                            <span class="stat-value">
                                                @if($history->user->package_id)
                                                    {{ $history->user->package->name ?? 'N/A' }}
                                                @else
                                                    Chưa có gói
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Info -->
                        @if($history->admin)
                            <div class="user-details mt-4">
                                <h6 class="section-title">Thông tin admin</h6>
                                <div class="user-card">
                                    <div class="user-info">
                                        <h6>{{ $history->admin->full_name }}</h6>
                                        <p class="text-muted">{{ $history->admin->email }}</p>
                                        <div class="user-stats">
                                            <div class="stat-item">
                                                <span class="stat-label">Vai trò:</span>
                                                <span class="stat-value">{{ $history->admin->role === 'admin' ? 'Admin' : 'User' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
<style>
.type-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.type-badge.type-payment {
    background: #d4edda;
    color: #155724;
}

.type-badge.type-purchase {
    background: #f8d7da;
    color: #721c24;
}

.type-badge.type-manual {
    background: #fff3e0;
    color: #f57c00;
}

.type-badge.type-monthly_bonus {
    background: #e3f2fd;
    color: #1976d2;
}

.type-badge.type-getlink {
    background: #e3f2fd;
    color: #1976d2;
}

.metadata-container {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    max-height: 300px;
    overflow-y: auto;
}

.metadata-json {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px;
    margin: 0;
    font-size: 12px;
    color: #495057;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.transaction-details {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.detail-item label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.detail-value {
    color: #333;
    font-size: 14px;
}

.user-details {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.user-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
}

.user-info h6 {
    color: #333;
    margin-bottom: 5px;
}

.user-stats {
    margin-top: 15px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    font-weight: 500;
    color: #6c757d;
    font-size: 14px;
}

.stat-value {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}
</style>
@endpush
