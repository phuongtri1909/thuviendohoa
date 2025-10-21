@extends('admin.layouts.sidebar')

@section('title', 'Thêm danh mục')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-folder-plus icon-title"></i>
                <h5>Thêm danh mục mới</h5>
            </div>
        </div>

        <div class="form-body">
            @include('components.alert', ['alertType' => 'alert'])

            <form action="{{ route('admin.categories.store') }}" method="POST" class="category-form" id="category-form" enctype="multipart/form-data">
                @csrf

                <div class="form-tabs">
                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên danh mục <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                               value="{{ old('name') }}" required>
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
                        <label for="order" class="form-label-custom">
                            Thứ tự
                            <span class="required-mark">*</span>
                        </label>
                        <input type="number" id="order" name="order" class="custom-input {{ $errors->has('order') ? 'input-error' : '' }}" value="{{ old('order', 0) }}" min="0" required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Số nhỏ hơn sẽ hiển thị trước</span>
                        </div>
                        <div class="error-message" id="error-order">
                            @error('order')
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
                        <i class="fas fa-save"></i> Tạo danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection
