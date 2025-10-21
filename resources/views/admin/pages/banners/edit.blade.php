@extends('Admin.layouts.sidebar')

@section('title', 'Chỉnh sửa Banner')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa Banner</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.banners.update', $banner) }}" method="POST" class="category-form"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image" class="form-label-custom">Ảnh Banner</label>
                                    <input type="file" id="image" name="image" accept="image/*"
                                        class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}">
                                    <div class="form-hint"><i class="fas fa-info-circle"></i><span>Định dạng: jpeg, png,
                                            jpg, gif, webp. Tối đa 10MB</span></div>
                                    <div class="error-message" id="error-image">
                                        @error('image')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    <div id="image-preview" class="mt-2">
                                        @if ($banner->image)
                                            <img src="{{ Storage::url($banner->image) }}" alt="Current banner"
                                                style="max-width: 300px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="key_page" class="form-label-custom">Trang <span
                                            class="required-mark">*</span></label>
                                    <select id="key_page" name="key_page"
                                        class="custom-input {{ $errors->has('key_page') ? 'input-error' : '' }}" required>
                                        <option value="">-- Chọn trang --</option>
                                        <option value="{{ \App\Models\Banner::PAGE_HOME }}"
                                            {{ old('key_page', $banner->key_page) === \App\Models\Banner::PAGE_HOME ? 'selected' : '' }}>
                                            Home</option>
                                        <option value="{{ \App\Models\Banner::PAGE_SEARCH }}"
                                            {{ old('key_page', $banner->key_page) === \App\Models\Banner::PAGE_SEARCH ? 'selected' : '' }}>
                                            Search</option>
                                    </select>
                                    <div class="error-message" id="error-key_page">
                                        @error('key_page')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="order" class="form-label-custom">Thứ tự <span
                                            class="required-mark">*</span></label>
                                    <input type="number" id="order" name="order"
                                        class="custom-input {{ $errors->has('order') ? 'input-error' : '' }}"
                                        value="{{ old('order', $banner->order) }}" min="0" required>
                                    <div class="form-hint"><i class="fas fa-info-circle"></i><span>Số nhỏ hơn sẽ hiển thị
                                            trước</span></div>
                                    <div class="error-message" id="error-order">
                                        @error('order')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">Trạng thái</label>
                                    <label class="d-flex align-items-center" style="gap:6px;">
                                        <input type="checkbox" name="status" value="1"
                                            {{ old('status', $banner->status) ? 'checked' : '' }}> Kích hoạt
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.banners.index') }}" class="back-button"><i class="fas fa-arrow-left"></i>
                            Quay lại</a>
                        <button type="submit" class="save-button"><i class="fas fa-save"></i> Cập nhật Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview
            document.getElementById('image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('image-preview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML =
                            `<img src="${e.target.result}" alt="Banner preview" style="max-width: 300px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
