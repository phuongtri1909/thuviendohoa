@extends('admin.layouts.sidebar')

@section('title', 'Thêm tag mới')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-tag icon-title"></i>
                <h5>Thêm tag mới</h5>
            </div>
        </div>

        <div class="form-body">
            

            <form action="{{ route('admin.tags.store') }}" method="POST" class="category-form" id="tag-form">
                @csrf

                <div class="form-tabs">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label-custom">
                                    Tên tag <span class="required-mark">*</span>
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
                        </div>
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
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.tags.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Tạo tag
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection


