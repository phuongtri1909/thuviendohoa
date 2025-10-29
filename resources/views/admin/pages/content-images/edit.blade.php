@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa Content Image')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-images icon-title"></i>
                <h5>Chỉnh sửa Content Image</h5>
            </div>
        </div>

        <div class="form-body">
            

            <form action="{{ route('admin.content-images.update', $contentImage) }}" method="POST" class="category-form" id="content-image-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-tabs">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="key" class="form-label-custom">Key (Không thể thay đổi)</label>
                                <input type="text" id="key" name="key" class="custom-input" value="{{ $contentImage->key }}" disabled readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Key là duy nhất và không thể thay đổi</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label-custom">Tên hiển thị <span class="required-mark">*</span></label>
                                <input type="text" id="name" name="name" class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}" value="{{ old('name', $contentImage->name) }}" required>
                                <div class="error-message" id="error-name">@error('name') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image" class="form-label-custom">Hình ảnh</label>
                                <input type="file" id="image" name="image" accept="image/*" class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}">
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Định dạng: jpeg, png, jpg, gif, webp. Tối đa 10MB</span></div>
                                <div class="error-message" id="error-image">@error('image') {{ $message }} @enderror</div>
                                <div id="image-preview" class="mt-2">
                                    @if($contentImage->image)
                                        @if (str_starts_with($contentImage->image, 'content-images/'))
                                            <div style="position: relative; display: inline-block;">
                                                <img src="{{ Storage::url($contentImage->image) }}" alt="Current image" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                                                <button type="button" id="delete-image-btn" class="btn btn-danger btn-sm" style="position: absolute; top: 5px; right: 5px;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @else
                                            <img src="{{ asset($contentImage->image) }}" alt="Current image" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                                            <div class="form-hint mt-2"><i class="fas fa-info-circle"></i><span>Đây là ảnh từ public folder, không thể xóa</span></div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="url" class="form-label-custom">URL (tùy chọn)</label>
                                <input type="url" id="url" name="url" class="custom-input {{ $errors->has('url') ? 'input-error' : '' }}" value="{{ old('url', $contentImage->url) }}" placeholder="https://example.com">
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>URL để chuyển hướng khi click vào content</span></div>
                                <div class="error-message" id="error-url">@error('url') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">Cài đặt Button Overlay (tùy chọn)</label>
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Nếu muốn hiển thị button overlay trên hình, hãy điền các thông tin bên dưới</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="button_text" class="form-label-custom">Text button</label>
                                <input type="text" id="button_text" name="button_text" class="custom-input {{ $errors->has('button_text') ? 'input-error' : '' }}" value="{{ old('button_text', $contentImage->button_text) }}" placeholder="Ví dụ: > Xem thêm">
                                <div class="error-message" id="error-button_text">@error('button_text') {{ $message }} @enderror</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="button_position_x" class="form-label-custom">Vị trí X (%)</label>
                                <input type="text" id="button_position_x" name="button_position_x" class="custom-input {{ $errors->has('button_position_x') ? 'input-error' : '' }}" value="{{ old('button_position_x', $contentImage->button_position_x) }}" placeholder="Ví dụ: 31%">
                                <div class="error-message" id="error-button_position_x">@error('button_position_x') {{ $message }} @enderror</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="button_position_y" class="form-label-custom">Vị trí Y (%)</label>
                                <input type="text" id="button_position_y" name="button_position_y" class="custom-input {{ $errors->has('button_position_y') ? 'input-error' : '' }}" value="{{ old('button_position_y', $contentImage->button_position_y) }}" placeholder="Ví dụ: 80%">
                                <div class="error-message" id="error-button_position_y">@error('button_position_y') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">Trạng thái</label>
                                <label class="d-flex align-items-center" style="gap:6px;">
                                    <input type="checkbox" name="status" value="1" {{ old('status', $contentImage->status) ? 'checked' : '' }}> Kích hoạt
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.content-images.index') }}" class="back-button"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    <button type="submit" class="save-button"><i class="fas fa-save"></i> Cập nhật</button>
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
                preview.innerHTML = `<img src="${e.target.result}" alt="Image preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Delete existing image
    const deleteBtn = document.getElementById('delete-image-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Bạn có chắc chắn muốn xóa hình ảnh này?')) {
                return;
            }

            fetch('{{ route('admin.content-images.delete-image', $contentImage) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('image-preview').innerHTML = '<p class="text-success">Đã xóa hình ảnh thành công!</p>';
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa hình ảnh!');
            });
        });
    }
});
</script>
@endpush

