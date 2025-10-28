@extends('admin.layouts.sidebar')

@section('title', 'Cài đặt Sidebar Blog')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-cog icon-title"></i>
                <h5>Cài đặt Sidebar Blog</h5>
            </div>
        </div>

        <div class="form-body">

            <form action="{{ route('admin.blog-sidebar-setting.update') }}" method="POST" class="category-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-tabs">
                    <div class="form-group">
                        <label for="section_title" class="form-label-custom">
                            Tiêu đề phần <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="section_title" name="section_title" 
                               class="custom-input {{ $errors->has('section_title') ? 'input-error' : '' }}"
                               value="{{ old('section_title', $setting->section_title) }}" 
                               placeholder="VD: CẬP NHẬT XU HƯỚNG THIẾT KẾ"
                               required>
                        <div class="error-message" id="error-section_title">
                            @error('section_title')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category_id" class="form-label-custom">
                            Danh mục (Tên danh mục sẽ là tiêu đề phụ)
                        </label>
                        <select id="category_id" name="category_id" 
                                class="custom-input {{ $errors->has('category_id') ? 'input-error' : '' }}">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id', $setting->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Khi chọn danh mục, tiêu đề phụ sẽ tự động lấy tên danh mục. 
                            Sidebar sẽ hiển thị 3 bài viết mới nhất của danh mục này.
                        </small>
                        <div class="error-message" id="error-category_id">
                            @error('category_id')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="extra_link_title" class="form-label-custom">
                            Tiêu đề link phụ
                        </label>
                        <input type="text" id="extra_link_title" name="extra_link_title" 
                               class="custom-input {{ $errors->has('extra_link_title') ? 'input-error' : '' }}"
                               value="{{ old('extra_link_title', $setting->extra_link_title) }}" 
                               placeholder="VD: Part chia sẻ file miễn phí">
                        <div class="error-message" id="error-extra_link_title">
                            @error('extra_link_title')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="extra_link_url" class="form-label-custom">
                            URL link phụ
                        </label>
                        <input type="url" id="extra_link_url" name="extra_link_url" 
                               class="custom-input {{ $errors->has('extra_link_url') ? 'input-error' : '' }}"
                               value="{{ old('extra_link_url', $setting->extra_link_url) }}" 
                               placeholder="https://example.com">
                        <div class="error-message" id="error-extra_link_url">
                            @error('extra_link_url')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">
                            Banner Sidebar
                        </label>
                        
                        @if($setting->banner_images && count($setting->banner_images) > 0)
                            <div class="banner-list mb-3">
                                @foreach($setting->banner_images as $index => $banner)
                                    <div class="banner-item" data-index="{{ $index }}">
                                        <img src="{{ asset('storage/' . $banner) }}" alt="Banner {{ $index + 1 }}">
                                        <button type="button" class="btn-delete-banner" onclick="deleteBanner({{ $index }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <input type="file" id="banner_images" name="banner_images[]" 
                               class="custom-input {{ $errors->has('banner_images.*') ? 'input-error' : '' }}"
                               accept="image/*" multiple>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Chọn nhiều hình ảnh để upload. Định dạng: jpeg, jpg, png, gif, webp. Tối đa 10MB/ảnh.
                        </small>
                        <div class="error-message" id="error-banner_images">
                            @error('banner_images.*')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    @if($setting->category_id)
                        <div class="preview-box">
                            <h6><i class="fas fa-eye"></i> Xem trước</h6>
                            <div class="preview-content">
                                <p class="preview-title">{{ $setting->section_title }}</p>
                                <p class="preview-subtitle">{{ $setting->section_subtitle }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.blogs.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

@endsection

@push('styles')
    <style>
    .preview-box {
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        border-radius: 4px;
    }

    .preview-box h6 {
        margin-bottom: 10px;
        color: #007bff;
        font-weight: 600;
    }

    .preview-content {
        background: white;
        padding: 15px;
        border-radius: 4px;
    }

    .preview-title {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .preview-subtitle {
        font-size: 18px;
        font-weight: 600;
        color: #495057;
        margin: 0;
    }

    .text-muted {
        display: block;
        margin-top: 8px;
        font-size: 13px;
        color: #6c757d;
    }

    .text-muted i {
        color: #17a2b8;
    }

    .banner-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .banner-item {
        position: relative;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        aspect-ratio: 1;
    }

    .banner-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .btn-delete-banner {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .btn-delete-banner:hover {
        background: #dc3545;
        transform: scale(1.1);
    }
    </style>
@endpush

@push('scripts')
    <script>
        function deleteBanner(index) {
            if (!confirm('Bạn có chắc chắn muốn xóa banner này?')) {
                return;
            }

            fetch('{{ route('admin.blog-sidebar-setting.delete-banner') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ index: index })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa banner!');
            });
        }
    </script>
@endpush

