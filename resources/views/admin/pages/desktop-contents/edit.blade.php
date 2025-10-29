@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa Desktop Content')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-desktop icon-title"></i>
                <h5>Chỉnh sửa Desktop Content</h5>
            </div>
        </div>

        <div class="form-body">

            <form action="{{ route('admin.desktop-contents.update', $desktopContent) }}" method="POST" class="category-form" id="desktop-content-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-tabs">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="key" class="form-label-custom">Key (Không thể thay đổi)</label>
                                <input type="text" id="key" name="key" class="custom-input" value="{{ $desktopContent->key }}" disabled readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Key là duy nhất và không thể thay đổi</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label-custom">Tên hiển thị <span class="required-mark">*</span></label>
                                <input type="text" id="name" name="name" class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}" value="{{ old('name', $desktopContent->name) }}" required>
                                <div class="error-message" id="error-name">@error('name') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="logo" class="form-label-custom">Logo</label>
                                <input type="file" id="logo" name="logo" accept="image/*,.svg" class="custom-input {{ $errors->has('logo') ? 'input-error' : '' }}">
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Định dạng: jpeg, png, jpg, gif, webp, svg. Tối đa 10MB</span></div>
                                <div class="error-message" id="error-logo">@error('logo') {{ $message }} @enderror</div>
                                <div id="logo-preview" class="mt-2">
                                    @if($desktopContent->logo)
                                        <div style="position: relative; display: inline-block;">
                                            @if (str_starts_with($desktopContent->logo, 'desktop-content/'))
                                                <img src="{{ Storage::url($desktopContent->logo) }}" alt="Current logo" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                                                <button type="button" id="delete-logo-btn" class="btn btn-danger btn-sm" style="position: absolute; top: 5px; right: 5px;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <img src="{{ asset($desktopContent->logo) }}" alt="Current logo" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                                                <div class="form-hint mt-2"><i class="fas fa-info-circle"></i><span>Đây là ảnh từ public folder</span></div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title" class="form-label-custom">Tiêu đề chính <span class="required-mark">*</span></label>
                                <input type="text" id="title" name="title" class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}" value="{{ old('title', $desktopContent->title) }}" required>
                                <div class="error-message" id="error-title">@error('title') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description" class="form-label-custom">Mô tả <span class="required-mark">*</span></label>
                                <textarea id="description" name="description" rows="4" class="custom-input {{ $errors->has('description') ? 'input-error' : '' }}" required>{{ old('description', $desktopContent->description) }}</textarea>
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Có thể sử dụng HTML tags như &lt;span&gt;, &lt;strong&gt;, &lt;b&gt;</span></div>
                                <div class="error-message" id="error-description">@error('description') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">Các tính năng <span class="required-mark">*</span></label>
                                <div class="form-hint mb-3"><i class="fas fa-info-circle"></i><span>Quản lý các tính năng hiển thị trong desktop section</span></div>
                                
                                <div id="features-container">
                                    @php $features = old('features', $desktopContent->features); @endphp
                                    @foreach($features as $index => $feature)
                                    <div class="feature-item card mb-3" data-index="{{ $index }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Tính năng #{{ $index + 1 }}</h6>
                                                @if($index > 0)
                                                <button type="button" class="btn btn-sm btn-danger remove-feature">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                                @endif
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label-custom">Icon hiện tại</label>
                                                    <div class="mb-2">
                                                        @if(!empty($feature['icon']))
                                                            <div style="position: relative; display: inline-block;">
                                                                @if (str_starts_with($feature['icon'], 'desktop-content/'))
                                                                    <img src="{{ Storage::url($feature['icon']) }}" alt="icon" style="max-width: 60px; max-height: 60px;">
                                                                    <button type="button" class="btn btn-danger btn-sm delete-feature-icon" data-index="{{ $index }}" style="position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; padding: 0; font-size: 10px;">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                @else
                                                                    <img src="{{ asset($feature['icon']) }}" alt="icon" style="max-width: 60px; max-height: 60px;">
                                                                @endif
                                                            </div>
                                                            <input type="hidden" name="features[{{ $index }}][icon]" value="{{ $feature['icon'] }}">
                                                        @else
                                                            <span class="text-muted">Chưa có icon</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <label class="form-label-custom">Upload icon mới</label>
                                                    <input type="file" name="features[{{ $index }}][icon_file]" accept="image/*,.svg" class="custom-input">
                                                    <div class="form-hint"><i class="fas fa-info-circle"></i><span>SVG hoặc ảnh thường, tối đa 5MB</span></div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label class="form-label-custom">Tiêu đề <span class="required-mark">*</span></label>
                                                    <input type="text" name="features[{{ $index }}][title]" class="custom-input" value="{{ $feature['title'] ?? '' }}" required>
                                                    
                                                    <label class="form-label-custom mt-2">Mô tả <span class="required-mark">*</span></label>
                                                    <textarea name="features[{{ $index }}][description]" rows="2" class="custom-input" required>{{ $feature['description'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <button type="button" id="add-feature-btn" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Thêm tính năng
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">Trạng thái</label>
                                <label class="d-flex align-items-center" style="gap:6px;">
                                    <input type="checkbox" name="status" value="1" {{ old('status', $desktopContent->status) ? 'checked' : '' }}> Kích hoạt
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.desktop-contents.index') }}" class="back-button"><i class="fas fa-arrow-left"></i> Quay lại</a>
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
    let featureIndex = {{ count($features) }};

    // Logo preview
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('logo-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Logo preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Delete logo
    const deleteLogoBtn = document.getElementById('delete-logo-btn');
    if (deleteLogoBtn) {
        deleteLogoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Bạn có chắc chắn muốn xóa logo này?')) {
                return;
            }

            fetch('{{ route('admin.desktop-contents.delete-logo', $desktopContent) }}', {
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
                    document.getElementById('logo-preview').innerHTML = '<p class="text-success">Đã xóa logo thành công!</p>';
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa logo!');
            });
        });
    }

    // Delete feature icon
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-feature-icon')) {
            const btn = e.target.closest('.delete-feature-icon');
            const index = btn.getAttribute('data-index');
            
            if (!confirm('Bạn có chắc chắn muốn xóa icon này?')) {
                return;
            }

            fetch('{{ route('admin.desktop-contents.delete-feature-icon', $desktopContent) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ feature_index: parseInt(index) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa icon!');
            });
        }
    });

    // Add new feature
    document.getElementById('add-feature-btn').addEventListener('click', function() {
        const container = document.getElementById('features-container');
        const newFeature = `
            <div class="feature-item card mb-3" data-index="${featureIndex}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Tính năng #${featureIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-feature">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label-custom">Upload icon</label>
                            <input type="file" name="features[${featureIndex}][icon_file]" accept="image/*,.svg" class="custom-input">
                            <div class="form-hint"><i class="fas fa-info-circle"></i><span>SVG hoặc ảnh thường, tối đa 5MB</span></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label-custom">Tiêu đề <span class="required-mark">*</span></label>
                            <input type="text" name="features[${featureIndex}][title]" class="custom-input" required>
                            
                            <label class="form-label-custom mt-2">Mô tả <span class="required-mark">*</span></label>
                            <textarea name="features[${featureIndex}][description]" rows="2" class="custom-input" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', newFeature);
        featureIndex++;
        updateFeatureNumbers();
    });

    // Remove feature
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            if (confirm('Bạn có chắc chắn muốn xóa tính năng này?')) {
                e.target.closest('.feature-item').remove();
                updateFeatureNumbers();
            }
        }
    });

    function updateFeatureNumbers() {
        const features = document.querySelectorAll('.feature-item');
        features.forEach((feature, index) => {
            feature.querySelector('h6').textContent = `Tính năng #${index + 1}`;
        });
    }
});
</script>
@endpush

