@extends('Admin.layouts.sidebar')

@section('title', 'Chỉnh sửa tag')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-tag icon-title"></i>
                <h5>Chỉnh sửa tag</h5>
            </div>
            <div class="category-meta">
                <div class="category-badge name">
                    <i class="fas fa-tag"></i>
                    <span>{{ $tag->name }}</span>
                </div>
                <div class="category-badge slug">
                    <i class="fas fa-link"></i>
                    <span>{{ $tag->slug }}</span>
                </div>
                <div class="category-badge stories-count">
                    <i class="fas fa-book"></i>
                    <span>{{ $tag->sets_count }} bộ</span>
                </div>
                <div class="category-badge created">
                    <i class="fas fa-calendar"></i>
                    <span>Ngày tạo: {{ $tag->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="form-body">
            @include('components.alert', ['alertType' => 'alert'])

            <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="category-form" id="tag-form">
                @csrf
                @method('PUT')

                <div class="form-tabs">
                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên tag <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                               value="{{ old('name', $tag->name) }}" required>
                        <div class="error-message" id="error-name">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.tags.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Cập nhật tag
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
    .category-badge.slug { background: #f3e5f5; color: #7b1fa2; }
    .category-badge.stories-count { background: #e8f5e9; color: #2e7d32; }
    @media (max-width: 768px) { .category-meta { flex-direction: column; } }
    </style>
@endpush


