@extends('Admin.layouts.sidebar')

@section('title', 'Thêm màu mới')

@section('main-content')
    <div class="color-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-palette icon-title"></i>
                    <h5>Thêm màu mới</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.colors.store') }}" method="POST" class="color-form" id="color-form">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên màu <span class="required-mark">*</span>
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
                        <label for="value" class="form-label-custom">
                            Mã màu <span class="required-mark">*</span>
                        </label>
                        <div class="input-group">
                            <input type="color" id="value" name="value"
                                   class="custom-input {{ $errors->has('value') ? 'input-error' : '' }}"
                                   value="{{ old('value', '#000000') }}" required>
                            <input type="text" id="value-text"
                                   class="custom-input {{ $errors->has('value') ? 'input-error' : '' }}"
                                   value="{{ old('value', '#000000') }}" readonly>
                        </div>
                        <div class="error-message" id="error-value">
                            @error('value')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.colors.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Tạo màu
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
