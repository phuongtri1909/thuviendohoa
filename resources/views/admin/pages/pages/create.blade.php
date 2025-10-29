@extends('admin.layouts.sidebar')

@section('title', 'Thêm trang')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-file-plus icon-title"></i>
                    <h5>Thêm trang mới</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.pages.store') }}" method="POST" class="category-form">
                    @csrf

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="title" class="form-label-custom">
                                Tiêu đề <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="title" name="title" 
                                   class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}"
                                   value="{{ old('title') }}" required>
                            <div class="error-message">
                                @error('title')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="slug" class="form-label-custom">
                                Slug <small>(Để trống để tự động tạo)</small>
                            </label>
                            <input type="text" id="slug" name="slug" 
                                   class="custom-input {{ $errors->has('slug') ? 'input-error' : '' }}"
                                   value="{{ old('slug') }}">
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                <span>VD: gioi-thieu-printon, dieu-khoan-chung</span>
                            </div>
                            <div class="error-message">
                                @error('slug')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label-custom">
                                Nội dung <span class="required-mark">*</span>
                            </label>
                            <textarea id="content" name="content" class="custom-input {{ $errors->has('content') ? 'input-error' : '' }}">{{ old('content') }}</textarea>
                            <div class="error-message">
                                @error('content')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="form-label-custom">
                                Trạng thái <span class="required-mark">*</span>
                            </label>
                            <select id="status" name="status" class="custom-input {{ $errors->has('status') ? 'input-error' : '' }}" required>
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Ẩn</option>
                            </select>
                            <div class="error-message">
                                @error('status')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="order" class="form-label-custom">
                                Thứ tự <span class="required-mark">*</span>
                            </label>
                            <input type="number" id="order" name="order" 
                                   class="custom-input {{ $errors->has('order') ? 'input-error' : '' }}" 
                                   value="{{ old('order', 0) }}" min="0" required>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                <span>Số nhỏ hơn sẽ hiển thị trước</span>
                            </div>
                            <div class="error-message">
                                @error('order')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.pages.index') }}" class="back-button" onclick="cleanupTempImages(event)">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Tạo trang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .image-left {
            float: left;
            margin: 10px 20px 10px 0;
        }

        .image-center {
            display: block;
            margin: 10px auto;
        }

        .image-right {
            float: right;
            margin: 10px 0 10px 20px;
        }

        .image-captioned {
            display: table;
            max-width: 100%;
        }

        .image-captioned figcaption {
            display: table-caption;
            caption-side: bottom;
            padding: 5px;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
            font-style: italic;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('content', {
            height: 400,
            filebrowserUploadUrl: "{{ route('admin.pages.upload-image') }}?_token={{ csrf_token() }}",
            filebrowserUploadMethod: 'form',
            removePlugins: 'image',
            extraPlugins: 'uploadimage,image2',
            uploadUrl: "{{ route('admin.pages.upload-image') }}?_token={{ csrf_token() }}",
            imageUploadUrl: "{{ route('admin.pages.upload-image') }}?_token={{ csrf_token() }}",
            filebrowserImageUploadUrl: "{{ route('admin.pages.upload-image') }}?_token={{ csrf_token() }}",
            fileTools_requestHeaders: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            image2_alignClasses: ['image-left', 'image-center', 'image-right'],
            image2_captionedClass: 'image-captioned',
            image2_disableResizer: false
        });

        // Cleanup temp images when leaving page without saving
        function cleanupTempImages(event) {
            event.preventDefault();
            const url = event.currentTarget.href;
            
            fetch("{{ route('admin.pages.cleanup-temp-images') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).finally(() => {
                window.location.href = url;
            });
        }

        // Also cleanup on browser back/close
        window.addEventListener('beforeunload', function() {
            navigator.sendBeacon("{{ route('admin.pages.cleanup-temp-images') }}", new FormData());
        });
    </script>
@endpush

