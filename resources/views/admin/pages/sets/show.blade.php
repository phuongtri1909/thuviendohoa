@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết set')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-layer-group icon-title"></i>
                    <h5>Chi tiết set</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.sets.edit', $set) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.sets.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="category-details">
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">Tên set:</label>
                            <span class="detail-value">{{ $set->name }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Logo:</label>
                            <div class="detail-value">
                                @if ($set->image)
                                    <img src="{{ Storage::url($set->image) }}" alt="image" style="max-height: 120px; border-radius: 6px;">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Loại:</label>
                            <span class="detail-value">{{ $set->type }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Trạng thái:</label>
                            <span class="detail-value">{{ $set->status ? 'Kích hoạt' : 'Tắt' }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Kích thước:</label>
                            <span class="detail-value">{{ $set->size }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Giá:</label>
                            <span class="detail-value">{{ number_format($set->price) }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Nổi bật:</label>
                            <span class="detail-value">
                                @if($set->is_featured)
                                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Có</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Không</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Từ khóa:</label>
                            <span class="detail-value">{{ $set->keywords ? (is_string($set->keywords) ? $set->keywords : json_encode($set->keywords)) : 'Không có' }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Định dạng:</label>
                            <span class="detail-value">{{ $set->formats ? (is_string($set->formats) ? $set->formats : json_encode($set->formats)) : 'Không có' }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Mô tả:</label>
                            <span class="detail-value">{{ $set->description }}</span>
                        </div>
                    </div>

                    <!-- Photos Section -->
                    @if($set->photos && $set->photos->count())
                    <div class="detail-section">
                        <h6 class="section-title"><i class="fas fa-images"></i> Ảnh trong set ({{ $set->photos->count() }})</h6>
                        <div class="photos-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin-top: 15px;">
                            @foreach($set->photos as $photo)
                                <div class="photo-item" style="text-align: center;">
                                    <img src="{{ Storage::url($photo->path) }}" alt="Photo" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                    <small class="text-muted d-block mt-1">{{ $photo->created_at->format('d/m/Y') }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Albums Section -->
                    @if($set->albums && $set->albums->count())
                    <div class="detail-section">
                        <h6 class="section-title"><i class="fas fa-folder"></i> Albums ({{ $set->albums->count() }})</h6>
                        <div class="tags-container" style="margin-top: 15px;">
                            @foreach($set->albums as $albumSet)
                                <span class="tag-item" style="display: inline-block; background: #e3f2fd; color: #1976d2; padding: 6px 12px; border-radius: 16px; margin: 4px; font-size: 13px;">
                                    {{ $albumSet->album->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Categories Section -->
                    @if($set->categories && $set->categories->count())
                    <div class="detail-section">
                        <h6 class="section-title"><i class="fas fa-tags"></i> Danh mục ({{ $set->categories->count() }})</h6>
                        <div class="tags-container" style="margin-top: 15px;">
                            @foreach($set->categories as $categorySet)
                                <span class="tag-item" style="display: inline-block; background: #f3e5f5; color: #7b1fa2; padding: 6px 12px; border-radius: 16px; margin: 4px; font-size: 13px;">
                                    {{ $categorySet->category->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Colors Section -->
                    @if($set->colors && $set->colors->count())
                    <div class="detail-section">
                        <h6 class="section-title"><i class="fas fa-palette"></i> Màu sắc ({{ $set->colors->count() }})</h6>
                        <div class="tags-container" style="margin-top: 15px;">
                            @foreach($set->colors as $colorSet)
                                <span class="tag-item" style="display: inline-flex; align-items: center; gap: 8px; background: #fff3e0; color: #f57c00; padding: 6px 12px; border-radius: 16px; margin: 4px; font-size: 13px;">
                                    <span style="width: 16px; height: 16px; border-radius: 3px; border: 1px solid #ddd; background-color: {{ $colorSet->color->value }};"></span>
                                    {{ $colorSet->color->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Software Section -->
                    @if($set->software && $set->software->count())
                    <div class="detail-section">
                        <h6 class="section-title"><i class="fas fa-laptop-code"></i> Phần mềm ({{ $set->software->count() }})</h6>
                        <div class="tags-container" style="margin-top: 15px;">
                            @foreach($set->software as $softwareSet)
                                <span class="tag-item" style="display: inline-block; background: #e8f5e8; color: #2e7d32; padding: 6px 12px; border-radius: 16px; margin: 4px; font-size: 13px;">
                                    {{ $softwareSet->software->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Tags Section -->
                    @if($set->tags && $set->tags->count())
                    <div class="detail-section">
                        <h6 class="section-title"><i class="fas fa-hashtag"></i> Tags ({{ $set->tags->count() }})</h6>
                        <div class="tags-container" style="margin-top: 15px;">
                            @foreach($set->tags as $tagSet)
                                <span class="tag-item" style="display: inline-block; background: #fff8e1; color: #f9a825; padding: 6px 12px; border-radius: 16px; margin: 4px; font-size: 13px;">
                                    {{ $tagSet->tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">Ngày tạo:</label>
                            <span class="detail-value">{{ $set->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Cập nhật lần cuối:</label>
                            <span class="detail-value">{{ $set->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
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
    .section-title { font-weight: 600; color: #495057; margin-bottom: 15px; font-size: 16px; }
    .section-title i { margin-right: 8px; color: #6c757d; }
    .photos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; }
    .photo-item { text-align: center; }
    .tags-container { display: flex; flex-wrap: wrap; gap: 8px; }
    @media (max-width: 768px) { 
        .detail-item { flex-direction: column; gap: 5px; } 
        .detail-label { min-width: auto; margin-right: 0; }
        .photos-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; }
    }
</style>


