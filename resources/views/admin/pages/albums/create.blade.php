@extends('admin.layouts.sidebar')

@section('title', 'Thêm album mới')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-images icon-title"></i>
                <h5>Thêm album mới</h5>
            </div>
        </div>

        <div class="form-body">
            

            <form action="{{ route('admin.albums.store') }}" method="POST" class="category-form" id="album-form" enctype="multipart/form-data">
                @csrf

                <div class="form-tabs">
                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên album <span class="required-mark">*</span>
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
                            Ảnh album <span class="required-mark">*</span>
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}" required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Định dạng: jpeg, png, jpg, gif, webp, svg. Tối đa 10MB</span>
                        </div>
                        <div class="error-message" id="error-image">
                            @error('image')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="icon" class="form-label-custom">
                            Icon album (dùng cho header)
                        </label>
                        <input type="file" id="icon" name="icon" accept="image/*" class="custom-input {{ $errors->has('icon') ? 'input-error' : '' }}">
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Định dạng: jpeg, png, jpg, gif, webp, svg. Tối đa 2MB. Nếu không có, sẽ dùng ảnh album</span>
                        </div>
                        <div class="error-message" id="error-icon">
                            @error('icon')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="order" class="form-label-custom">Thứ tự</label>
                                <input type="number" id="order" name="order" class="custom-input {{ $errors->has('order') ? 'input-error' : '' }}" value="{{ old('order') }}" min="0" step="1">
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Thứ tự hiển thị (số nhỏ hơn sẽ hiển thị trước). Để trống sẽ tự động gán giá trị tiếp theo.</span>
                                </div>
                                <div class="error-message" id="error-order">@error('order') {{ $message }} @enderror</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Tùy chọn hiển thị</label>
                                <div class="d-flex align-items-center" style="gap:16px;">
                                    <label class="d-flex align-items-center" style="gap:6px;">
                                        <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}> Featured
                                    </label>
                                    <label class="d-flex align-items-center" style="gap:6px;">
                                        <input type="checkbox" name="trending" value="1" {{ old('trending') ? 'checked' : '' }}> Trending
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.albums.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Tạo album
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection


