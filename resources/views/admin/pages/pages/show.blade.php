@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết trang')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-file-alt icon-title"></i>
                    <h5>Chi tiết trang: {{ $page->title }}</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pages.edit', $page) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.pages.index') }}" class="action-button-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="detail-section">
                    <div class="detail-row">
                        <div class="detail-label">ID:</div>
                        <div class="detail-value">{{ $page->id }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Tiêu đề:</div>
                        <div class="detail-value"><strong>{{ $page->title }}</strong></div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Slug:</div>
                        <div class="detail-value">
                            <code>{{ $page->slug }}</code>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">URL:</div>
                        <div class="detail-value">
                            <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-decoration-none">
                                {{ route('page.show', $page->slug) }}
                                <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Trạng thái:</div>
                        <div class="detail-value">
                            @if ($page->status)
                                <span class="status-badge-active">Hoạt động</span>
                            @else
                                <span class="status-badge-inactive">Ẩn</span>
                            @endif
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Thứ tự:</div>
                        <div class="detail-value">
                            <span class="order-badge">{{ $page->order }}</span>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Ngày tạo:</div>
                        <div class="detail-value">{{ $page->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Cập nhật lần cuối:</div>
                        <div class="detail-value">{{ $page->updated_at->format('d/m/Y H:i:s') }}</div>
                    </div>

                    <div class="detail-row-full mt-4">
                        <div class="detail-label mb-3">Nội dung:</div>
                        <div class="page-content-preview">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .page-content-preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            line-height: 1.8;
        }

        .page-content-preview h1,
        .page-content-preview h2,
        .page-content-preview h3,
        .page-content-preview h4,
        .page-content-preview h5,
        .page-content-preview h6 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .page-content-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin: 1rem 0;
        }

        .page-content-preview table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .page-content-preview table th,
        .page-content-preview table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }

        .page-content-preview table th {
            background: #e9ecef;
            font-weight: 600;
        }
    </style>
@endpush

