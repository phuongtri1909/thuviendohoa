@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết tag blog')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-tag icon-title"></i>
                    <h5>Chi tiết tag blog</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.tag-blogs.edit', $tagBlog) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.tag-blogs.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="category-details">
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">Tên tag:</label>
                            <span class="detail-value">{{ $tagBlog->name }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Slug:</label>
                            <span class="detail-value slug-text">{{ $tagBlog->slug }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Ngày tạo:</label>
                            <span class="detail-value">{{ $tagBlog->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Cập nhật lần cuối:</label>
                            <span class="detail-value">{{ $tagBlog->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .category-details {
        padding: 20px;
    }

    .detail-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .detail-item {
        display: flex;
        margin-bottom: 15px;
        align-items: flex-start;
    }

    .detail-item:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
        min-width: 150px;
        margin-right: 15px;
    }

    .detail-value {
        color: #333;
        flex: 1;
    }

    .slug-text {
        font-family: 'Courier New', monospace;
        font-size: 14px;
        color: #6c757d;
        background: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .detail-item {
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            min-width: auto;
            margin-right: 0;
        }
    }
</style>
@endpush

