@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết mua file')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-shopping-cart icon-title"></i>
                    <h5>Chi tiết mua file #{{ $purchase->id }}</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.purchase-sets.index') }}" class="action-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="purchase-details">
                            <h6 class="section-title">Thông tin mua file</h6>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <label>File:</label>
                                    <span class="detail-value">{{ $purchase->set->name ?? 'File đã bị xóa' }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Loại file:</label>
                                    <span class="detail-value">
                                        @if($purchase->set)
                                            <span class="type-badge {{ $purchase->set->type === 'free' ? 'free' : 'premium' }}">
                                                {{ $purchase->set->type === 'free' ? 'Miễn phí' : 'Premium' }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Số xu đã trả:</label>
                                    <span class="detail-value coins-badge">{{ number_format($purchase->coins) }} xu</span>
                                </div>
                                <div class="detail-item">
                                    <label>Trạng thái tải:</label>
                                    <span class="download-status {{ $purchase->downloaded_at ? 'downloaded' : 'not-downloaded' }}">
                                        @if($purchase->downloaded_at)
                                            <i class="fas fa-check-circle"></i> Đã tải
                                        @else
                                            <i class="fas fa-clock"></i> Chưa tải
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Ngày mua:</label>
                                    <span class="detail-value">{{ $purchase->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                @if($purchase->downloaded_at)
                                    <div class="detail-item">
                                        <label>Ngày tải:</label>
                                        <span class="detail-value">{{ $purchase->downloaded_at->format('d/m/Y H:i:s') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($purchase->set)
                            <div class="purchase-details mt-4">
                                <h6 class="section-title">Thông tin file</h6>
                                <div class="set-info-card">
                                    <div class="set-image">
                                        @if($purchase->set->image)
                                            <img src="{{ Storage::url($purchase->set->image) }}" alt="{{ $purchase->set->name }}" style="max-width: 200px; border-radius: 8px;">
                                        @else
                                            <div class="no-image">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="set-details">
                                        <h6>{{ $purchase->set->name }}</h6>
                                        <p class="text-muted">{{ $purchase->set->description ?? 'Không có mô tả' }}</p>
                                        <div class="set-meta">
                                            <span class="meta-item">
                                                <i class="fas fa-tag"></i> {{ $purchase->set->type === 'free' ? 'Miễn phí' : 'Premium' }}
                                            </span>
                                            @if($purchase->set->price)
                                                <span class="meta-item">
                                                    <i class="fas fa-coins"></i> {{ number_format($purchase->set->price) }} xu
                                                </span>
                                            @endif
                                            @if($purchase->set->size)
                                                <span class="meta-item">
                                                    <i class="fas fa-weight"></i> {{ $purchase->set->size }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <div class="purchase-details">
                            <h6 class="section-title">Thông tin người dùng</h6>
                            <div class="user-card">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-info">
                                    <h6>{{ $purchase->user->full_name ?? 'N/A' }}</h6>
                                    <p class="text-muted">{{ $purchase->user->email ?? 'N/A' }}</p>
                                    <div class="user-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Xu hiện tại:</span>
                                            <span class="stat-value">{{ number_format($purchase->user->coins ?? 0) }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Lượt tải miễn phí:</span>
                                            <span class="stat-value">{{ $purchase->user->free_downloads ?? 0 }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Gói hiện tại:</span>
                                            <span class="stat-value">
                                                @if($purchase->user->package_id)
                                                    {{ $purchase->user->package->name ?? 'N/A' }}
                                                @else
                                                    Chưa có gói
                                                @endif
                                            </span>
                                        </div>
                                        @if($purchase->user->package_expired_at)
                                            <div class="stat-item">
                                                <span class="stat-label">Hết hạn:</span>
                                                <span class="stat-value {{ $purchase->user->package_expired_at->isFuture() ? 'text-success' : 'text-danger' }}">
                                                    {{ $purchase->user->package_expired_at->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        @endif
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

        .coins-badge {
            background: #fff3e0;
            color: #f57c00;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
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

        .set-info-card {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .set-image img {
            border-radius: 8px;
        }

        .no-image {
            width: 200px;
            height: 150px;
            background: #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .set-details h6 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .set-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #6c757d;
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

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .set-info-card {
                flex-direction: column;
            }
        }
    </style>
@endpush
