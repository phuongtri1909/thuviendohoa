@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa màu')

@section('main-content')
    <div class="color-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa màu</h5>
                </div>
            </div>

            <div class="form-body">
                

                <form action="{{ route('admin.colors.update', $color) }}" method="POST" class="color-form" id="color-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên màu <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                               value="{{ old('name', $color->name) }}" required>
                        <div class="error-message" id="error-name">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="value" class="form-label-custom">
                                    Mã màu <span class="required-mark">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="color" id="value" name="value"
                                           class="custom-input {{ $errors->has('value') ? 'input-error' : '' }}"
                                           value="{{ old('value', $color->value) }}" required>
                                    <input type="text" id="value-text"
                                           class="custom-input {{ $errors->has('value') ? 'input-error' : '' }}"
                                           value="{{ old('value', $color->value) }}" readonly>
                                </div>
                                <div class="error-message" id="error-value">
                                    @error('value')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="order" class="form-label-custom">Thứ tự</label>
                                <input type="number" id="order" name="order" class="custom-input {{ $errors->has('order') ? 'input-error' : '' }}" value="{{ old('order', $color->order) }}" min="0" step="1">
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Thứ tự hiển thị (số nhỏ hơn sẽ hiển thị trước).</span>
                                </div>
                                <div class="error-message" id="error-order">@error('order') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.colors.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('value').addEventListener('input', function(e) {
    document.getElementById('value-text').value = e.target.value;
});
</script>
@endpush
