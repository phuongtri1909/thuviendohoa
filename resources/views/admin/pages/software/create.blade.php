@extends('admin.layouts.sidebar')

@section('title', 'Thêm phần mềm')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-code icon-title"></i>
                    <h5>Thêm phần mềm mới</h5>
                </div>
            </div>

            <div class="form-body">
                

                <form action="{{ route('admin.software.store') }}" method="POST" class="category-form" enctype="multipart/form-data">
                    @csrf

                    <div class="form-tabs">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">Tên phần mềm <span class="required-mark">*</span></label>
                                    <input type="text" id="name" name="name" class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}" value="{{ old('name') }}" required>
                                    <div class="error-message">@error('name') {{ $message }} @enderror</div>
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
                                    <div class="error-message">@error('order') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="logo" class="form-label-custom">Logo (bắt buộc)</label>
                            <input type="file" id="logo" name="logo" accept="image/*" class="custom-input {{ $errors->has('logo') ? 'input-error' : '' }}" required>
                            <div class="form-hint"><i class="fas fa-info-circle"></i><span>Định dạng: jpeg, png, jpg, gif, webp, svg. Tối đa 10MB</span></div>
                            <div class="error-message">@error('logo') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <label for="logo_hover" class="form-label-custom">Logo hover</label>
                            <input type="file" id="logo_hover" name="logo_hover" accept="image/*" class="custom-input {{ $errors->has('logo_hover') ? 'input-error' : '' }}">
                            <div class="error-message">@error('logo_hover') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <label for="logo_active" class="form-label-custom">Logo active</label>
                            <input type="file" id="logo_active" name="logo_active" accept="image/*" class="custom-input {{ $errors->has('logo_active') ? 'input-error' : '' }}">
                            <div class="error-message">@error('logo_active') {{ $message }} @enderror</div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.software.index') }}" class="back-button"><i class="fas fa-arrow-left"></i> Quay lại</a>
                        <button type="submit" class="save-button"><i class="fas fa-save"></i> Tạo phần mềm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


