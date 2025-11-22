@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa About Content')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa About Content</h5>
                </div>
            </div>

            <div class="form-body">
                <form action="{{ route('admin.about-contents.update', $aboutContent) }}" method="POST" class="category-form">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="key" class="form-label-custom">
                                Key
                            </label>
                            <input type="text" id="key" name="key" 
                                   class="custom-input" 
                                   value="{{ $aboutContent->key }}" 
                                   disabled>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Key không thể thay đổi. Key này được dùng để phân biệt các content khác nhau.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="title" class="form-label-custom">
                                Tiêu đề
                            </label>
                            <input type="text" id="title" name="title" 
                                   class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}"
                                   value="{{ old('title', $aboutContent->title) }}" 
                                   placeholder="VD: HƯỚNG DẪN TẢI FILE:">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Tiêu đề là tùy chọn. Để trống nếu không cần.
                            </small>
                            <div class="error-message" id="error-title">
                                @error('title')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label-custom">
                                Nội dung <span class="required-mark">*</span>
                            </label>
                            <textarea id="content" name="content" 
                                      class="custom-input {{ $errors->has('content') ? 'input-error' : '' }}"
                                      rows="10"
                                      placeholder="Nhập nội dung..."
                                      required>{{ old('content', $aboutContent->content) }}</textarea>
                            <div class="error-message" id="error-content">
                                @error('content')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.about-contents.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

