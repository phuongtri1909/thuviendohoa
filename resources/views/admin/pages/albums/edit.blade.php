@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa album')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-images icon-title"></i>
                <h5>Chỉnh sửa album</h5>
            </div>
            <div class="category-meta">
                <div class="category-badge name">
                    <i class="fas fa-image"></i>
                    <span>{{ $album->name }}</span>
                </div>
                <div class="category-badge created">
                    <i class="fas fa-calendar"></i>
                    <span>Ngày tạo: {{ $album->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="form-body">
            @include('components.alert', ['alertType' => 'alert'])

            <form action="{{ route('admin.albums.update', $album) }}" method="POST" class="category-form" id="album-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-tabs">
                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên album <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                               value="{{ old('name', $album->name) }}" required>
                        <div class="error-message" id="error-name">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label-custom">
                            Ảnh album
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}">
                        @if($album->image)
                            <div class="mt-2">
                                <img src="{{ Storage::url($album->image) }}" alt="album image" style="max-height: 80px; border-radius: 6px;">
                            </div>
                        @endif
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Định dạng: jpeg, png, jpg, gif, webp. Tối đa 10MB</span>
                        </div>
                        <div class="error-message" id="error-image">
                            @error('image')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">Tùy chọn hiển thị</label>
                        <div class="d-flex align-items-center" style="gap:16px;">
                            <label class="d-flex align-items-center" style="gap:6px;">
                                <input type="checkbox" name="featured" value="1" {{ old('featured', $album->featuredType ? 1 : 0) ? 'checked' : '' }}> Featured
                            </label>
                            <label class="d-flex align-items-center" style="gap:6px;">
                                <input type="checkbox" name="trending" value="1" {{ old('trending', $album->trendingType ? 1 : 0) ? 'checked' : '' }}> Trending
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.albums.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Cập nhật album
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

@endsection

@push('styles')
    <style>
    .category-meta { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px; }
    .category-badge { display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: #f8f9fa; border-radius: 20px; font-size: 14px; color: #495057; }
    .category-badge i { color: #007bff; }
    .category-badge.name { background: #e3f2fd; color: #1976d2; }
    .category-badge.created { background: #f3e5f5; color: #7b1fa2; }
    @media (max-width: 768px) { .category-meta { flex-direction: column; } }
    </style>
@endpush


