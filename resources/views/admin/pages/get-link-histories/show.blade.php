@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết Get Link')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-link icon-title"></i>
                    <h5>Chi tiết Get Link #{{ $history->id }}</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.get-link-histories.index') }}" class="action-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="transaction-details">
                            <h6 class="section-title">Thông tin Get Link</h6>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <label>ID:</label>
                                    <span class="detail-value">{{ $history->id }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>User:</label>
                                    <span class="detail-value">
                                        <strong>{{ $history->user->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $history->user->email }}</small>
                                    </span>
                                </div>
                                <div class="detail-item full-width">
                                    <label>URL:</label>
                                    <span class="detail-value">
                                        <a href="{{ $history->url }}" target="_blank" class="text-decoration-none">
                                            {{ $history->url }}
                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Title:</label>
                                    <span class="detail-value">
                                        @if($history->favicon)
                                            <img src="{{ $history->favicon }}" alt="favicon" class="favicon-img me-2">
                                        @endif
                                        {{ $history->title ?? 'N/A' }}
                                    </span>
                                </div>
                                @if($history->favicon)
                                    <div class="detail-item">
                                        <label>Favicon:</label>
                                        <span class="detail-value">
                                            <img src="{{ $history->favicon }}" alt="favicon" class="favicon-preview">
                                        </span>
                                    </div>
                                @endif
                                <div class="detail-item">
                                    <label>Xu đã trừ:</label>
                                    <span class="detail-value">
                                        <span class="coins-badge negative">-{{ number_format($history->coins_spent) }} xu</span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Thời gian:</label>
                                    <span class="detail-value">
                                        {{ $history->created_at->format('d/m/Y H:i:s') }}
                                        <br>
                                        <small class="text-muted">({{ $history->created_at->diffForHumans() }})</small>
                                    </span>
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
    .transaction-details {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
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
        color: #666;
        font-size: 14px;
    }

    .detail-item .detail-value {
        color: #333;
        font-size: 14px;
    }

    .favicon-img {
        width: 16px;
        height: 16px;
        object-fit: contain;
        vertical-align: middle;
    }

    .favicon-preview {
        width: 32px;
        height: 32px;
        object-fit: contain;
        padding: 4px;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        background: white;
    }

    .detail-value a {
        color: #667eea;
        word-break: break-all;
    }

    .detail-value a:hover {
        color: #764ba2;
        text-decoration: underline !important;
    }
</style>
@endpush

