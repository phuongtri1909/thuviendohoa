@extends('Admin.layouts.sidebar')

@section('title', 'Chi tiết album')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-images icon-title"></i>
                    <h5>Chi tiết album</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.albums.edit', $album) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.albums.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="category-details">
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">Tên album:</label>
                            <span class="detail-value">{{ $album->name }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Ảnh:</label>
                            <div class="detail-value">
                                @if ($album->image)
                                    <img src="{{ Storage::url($album->image) }}" alt="image" style="max-height: 120px; border-radius: 6px;">
                                @else
                                    <span class="text-muted">Không có ảnh</span>
                                @endif
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Featured:</label>
                            <span class="detail-value">{{ $album->featuredType ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Trending:</label>
                            <span class="detail-value">{{ $album->trendingType ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Ngày tạo:</label>
                            <span class="detail-value">{{ $album->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Cập nhật lần cuối:</label>
                            <span class="detail-value">{{ $album->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .category-details { padding: 20px; }
    .detail-section { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
    .detail-item { display: flex; margin-bottom: 15px; align-items: flex-start; }
    .detail-label { font-weight: 600; color: #495057; min-width: 150px; margin-right: 15px; }
    .detail-value { color: #333; flex: 1; }
    .stories-count { background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600; }
    @media (max-width: 768px) { .detail-item { flex-direction: column; gap: 5px; } .detail-label { min-width: auto; margin-right: 0; } }
</style>


