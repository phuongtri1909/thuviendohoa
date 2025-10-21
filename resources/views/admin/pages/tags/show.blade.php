@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết tag')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-tag icon-title"></i>
                    <h5>Chi tiết tag</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.tags.edit', $tag) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.tags.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="category-details">
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">Tên tag:</label>
                            <span class="detail-value">{{ $tag->name }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Slug:</label>
                            <span class="detail-value slug-text">{{ $tag->slug }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Số bộ:</label>
                            <span class="detail-value stories-count">{{ $tag->sets_count }} bộ</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Ngày tạo:</label>
                            <span class="detail-value">{{ $tag->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Cập nhật lần cuối:</label>
                            <span class="detail-value">{{ $tag->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>

                    @if ($tag->sets->count() > 0)
                        <div class="stories-section">
                            <h6 class="section-title">
                                <i class="fas fa-book"></i>
                                Danh sách bộ có tag này ({{ $tag->sets->count() }})
                            </h6>
                            <div class="stories-list">
                                @foreach ($tag->sets as $set)
                                    <div class="story-item">
                                        <div class="story-info">
                                            <h6 class="story-title">{{ $set->name }}</h6>
                                            <p class="story-desc">{{ $set->desc ?? '' }}</p>
                                            <div class="story-meta">
                                                <span class="story-status">
                                                    <i class="fas fa-info-circle"></i>
                                                    <span class="text-muted">Bộ sưu tập</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="empty-stories">
                            <div class="empty-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h6>Chưa có bộ nào</h6>
                            <p>Chưa có bộ nào gán tag này.</p>
                        </div>
                    @endif
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

        .stories-count {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .stories-section {
            margin-top: 30px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            color: #495057;
        }

        .section-title i {
            color: #007bff;
        }

        .stories-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .story-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .story-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
        }

        .story-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .story-desc {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .story-meta {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #6c757d;
        }

        .story-meta i {
            color: #007bff;
        }

        .empty-stories {
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }

        .empty-icon {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
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

            .stories-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
