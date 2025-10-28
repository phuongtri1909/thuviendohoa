@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết bài viết')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-newspaper icon-title"></i>
                    <h5>Chi tiết bài viết</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.blogs.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="blog-details">
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">Tiêu đề:</label>
                            <span class="detail-value">{{ $blog->title }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Slug:</label>
                            <span class="detail-value slug-text">{{ $blog->slug }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Danh mục:</label>
                            <span class="detail-value">
                                <span class="category-badge">{{ $blog->category->name ?? 'N/A' }}</span>
                            </span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Tags:</label>
                            <span class="detail-value">
                                @if($blog->tags->isNotEmpty())
                                    @foreach($blog->tags as $blogTag)
                                        <span class="tag-badge">{{ $blogTag->tag->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Không có tag</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Nổi bật:</label>
                            <span class="detail-value">
                                @if($blog->is_featured)
                                    <span class="featured-badge">
                                        <i class="fas fa-star"></i> Nổi bật
                                    </span>
                                @else
                                    <span class="not-featured-text">Không</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Tác giả:</label>
                            <span class="detail-value">{{ $blog->user->name ?? $blog->create_by }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Ngày tạo:</label>
                            <span class="detail-value">{{ $blog->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Cập nhật lần cuối:</label>
                            <span class="detail-value">{{ $blog->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h6 class="section-title">Ảnh chính</h6>
                        @if($blog->image)
                            <div class="blog-image">
                                <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}">
                            </div>
                        @else
                            <p class="text-muted">Không có ảnh chính</p>
                        @endif
                    </div>

                    @if($blog->image_left)
                        <div class="detail-section">
                            <h6 class="section-title">Ảnh phụ</h6>
                            <div class="blog-image">
                                <img src="{{ asset('storage/' . $blog->image_left) }}" alt="{{ $blog->title }} - Left">
                            </div>
                        </div>
                    @endif

                    <div class="detail-section">
                        <h6 class="section-title">Nội dung</h6>
                        <div class="blog-content">
                            {!! $blog->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .blog-details {
        padding: 20px;
    }

    .detail-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .section-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 15px;
        font-size: 16px;
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

    .category-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    .tag-badge {
        display: inline-block;
        background: #f3e5f5;
        color: #7b1fa2;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .blog-image {
        text-align: center;
    }

    .blog-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        border: 2px solid #dee2e6;
    }

    .blog-content {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        line-height: 1.8;
    }

    .blog-content img {
        max-width: 100%;
        height: auto;
        margin: 15px 0;
        border-radius: 8px;
    }

    .blog-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }

    .blog-content table td,
    .blog-content table th {
        border: 1px solid #dee2e6;
        padding: 8px;
    }

    .text-muted {
        color: #6c757d;
    }

    .featured-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff3cd;
        color: #ff9800;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    .not-featured-text {
        color: #6c757d;
        font-size: 14px;
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

