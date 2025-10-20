@extends('Admin.layouts.sidebar')

@section('title', 'Chỉnh sửa danh mục')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-folder-edit icon-title"></i>
                <h5>Chỉnh sửa danh mục</h5>
            </div>
            <div class="category-meta">
                <div class="category-badge name">
                    <i class="fas fa-folder"></i>
                    <span>{{ $category->name }}</span>
                </div>
                <div class="category-badge slug">
                    <i class="fas fa-link"></i>
                    <span>{{ $category->slug }}</span>
                </div>
                <div class="category-badge stories-count">
                    <i class="fas fa-book"></i>
                    <span>{{ $category->sets_count }} bộ</span>
                </div>
                <div class="category-badge created">
                    <i class="fas fa-calendar"></i>
                    <span>Ngày tạo: {{ $category->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="form-body">
            @include('components.alert', ['alertType' => 'alert'])

            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="category-form" id="category-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-tabs">
                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên danh mục <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                               value="{{ old('name', $category->name) }}" required>
                        <div class="error-message" id="error-name">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label-custom">
                            Ảnh danh mục
                            <span class="required-mark">*</span>
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}">
                        @if($category->image)
                            <div class="mt-2">
                                <img src="{{ Storage::url($category->image) }}" alt="category image" style="max-height: 80px; border-radius: 6px;">
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
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.categories.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Cập nhật danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

@endsection

@push('styles')
    <style>
    .category-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }

    .category-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 20px;
        font-size: 14px;
        color: #495057;
    }

    .category-badge i {
        color: #007bff;
    }

    .category-badge.name {
        background: #e3f2fd;
        color: #1976d2;
    }

    .category-badge.slug {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .category-badge.stories-count {
        background: #e8f5e9;
        color: #2e7d32;
    }

    @media (max-width: 768px) {
        .category-meta {
            flex-direction: column;
        }
    }
    </style>
@endpush
