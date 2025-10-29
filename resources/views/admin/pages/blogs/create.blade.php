@extends('admin.layouts.sidebar')

@section('title', 'Thêm bài viết')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-newspaper icon-title"></i>
                    <h5>Thêm bài viết mới</h5>
                </div>
            </div>

            <div class="form-body">
                

                <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data"
                    class="category-form">
                    @csrf

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="title" class="form-label-custom">
                                Tiêu đề bài viết <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="title" name="title"
                                class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}"
                                value="{{ old('title') }}" required>
                            <div class="error-message" id="error-title">
                                @error('title')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subtitle" class="form-label-custom">
                                Tiêu đề phụ <span class="required-mark">*</span> <small>(Tối đa 3000 ký tự)</small>
                            </label>
                            <textarea id="subtitle" name="subtitle" rows="3"
                                class="custom-input {{ $errors->has('subtitle') ? 'input-error' : '' }}"
                                placeholder="Mô tả ngắn gọn về nội dung blog..."
                                required>{{ old('subtitle') }}</textarea>
                            <div class="error-message" id="error-subtitle">
                                @error('subtitle')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="category_id" class="form-label-custom">
                                Danh mục <span class="required-mark">*</span>
                            </label>
                            <select id="category_id" name="category_id"
                                class="custom-input {{ $errors->has('category_id') ? 'input-error' : '' }}" required>
                                <option value="">Chọn danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="error-message" id="error-category_id">
                                @error('category_id')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label for="is_featured" class="checkbox-label">Nổi bật</label>
                            </div>
                            <div class="error-message" id="error-is_featured">
                                @error('is_featured')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tag_ids" class="form-label-custom">
                                Tags (tùy chọn)
                            </label>
                            <div class="chip-select" data-select-id="tag_ids" style="position:relative;">
                                <div class="chip-select-toggle custom-input" tabindex="0">Chọn tags...</div>
                                <div class="chip-select-dropdown" style="position:absolute;left:0;right:0;z-index:20;background:#fff;border:1px solid #e9ecef;border-radius:6px;display:none;max-height:220px;overflow:auto;">
                                    @foreach($tags as $tag)
                                        <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;">
                                            <input type="checkbox" value="{{ $tag->id }}" data-text="{{ $tag->name }}" {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}>
                                            <span>{{ $tag->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div id="tag_ids_tags" class="selected-tags" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;"></div>
                                <select id="tag_ids" name="tag_ids[]" multiple style="display:none;">
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tag_ids', [])) ? 'selected' : '' }}>{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-message" id="error-tag_ids">
                                @error('tag_ids')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label-custom">
                                Ảnh chính <span class="required-mark">*</span>
                            </label>
                            <input type="file" id="image" name="image"
                                class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}"
                                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required>
                            <small class="form-text">Định dạng: jpeg, png, jpg, gif, webp. Tối đa 10MB.</small>
                            <div class="error-message" id="error-image">
                                @error('image')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image_left" class="form-label-custom">
                                Ảnh phụ (tùy chọn)
                            </label>
                            <input type="file" id="image_left" name="image_left"
                                class="custom-input {{ $errors->has('image_left') ? 'input-error' : '' }}"
                                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                            <small class="form-text">Định dạng: jpeg, png, jpg, gif, webp. Tối đa 10MB.</small>
                            <div class="error-message" id="error-image_left">
                                @error('image_left')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label-custom">
                                Nội dung <span class="required-mark">*</span>
                            </label>
                            <textarea id="content" name="content"
                                class="custom-input {{ $errors->has('content') ? 'input-error' : '' }}"
                                rows="10" required>{{ old('content') }}</textarea>
                            <div class="error-message" id="error-content">
                                @error('content')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.blogs.index') }}" class="back-button" onclick="cleanupTempImages(event)">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Tạo bài viết
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-text {
            display: block;
            margin-top: 5px;
            font-size: 13px;
            color: #6c757d;
        }

        select[multiple] {
            height: auto !important;
        }

        /* CKEditor Image2 Styles */
        .image-left {
            float: left;
            margin: 10px 20px 10px 0;
        }

        .image-right {
            float: right;
            margin: 10px 0 10px 20px;
        }

        .image-center {
            display: block;
            margin: 10px auto;
            text-align: center;
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
    <script src="/ckeditor/ckeditor.js"></script>
    <script>
        // Chip select dropdown functionality
        function initChipDropdown(wrapper) {
            const selectId = wrapper.getAttribute('data-select-id');
            const selectEl = document.getElementById(selectId);
            const toggle = wrapper.querySelector('.chip-select-toggle');
            const dropdown = wrapper.querySelector('.chip-select-dropdown');
            const tagContainer = wrapper.querySelector('.selected-tags');

            const syncFromCheckboxes = () => {
                const checked = Array.from(dropdown.querySelectorAll('input[type="checkbox"]:checked'));
                Array.from(selectEl.options).forEach(opt => { opt.selected = false; });
                checked.forEach(cb => {
                    const opt = Array.from(selectEl.options).find(o => o.value === cb.value);
                    if (opt) opt.selected = true;
                });
                renderTags();
            };

            const renderTags = () => {
                if (!tagContainer) return;
                tagContainer.innerHTML = '';
                Array.from(selectEl.options).forEach(opt => {
                    if (opt.selected) {
                        const chip = document.createElement('span');
                        chip.className = 'chip-tag';
                        chip.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:16px;background:#e3f2fd;color:#1976d2;font-size:13px;';
                        chip.innerHTML = `<span>${opt.text}</span>`;
                        const closeBtn = document.createElement('button');
                        closeBtn.type = 'button';
                        closeBtn.textContent = '×';
                        closeBtn.style.cssText = 'background:transparent;border:none;color:#1976d2;font-size:16px;line-height:1;cursor:pointer;';
                        closeBtn.addEventListener('click', () => {
                            const cb = Array.from(dropdown.querySelectorAll('input[type="checkbox"]')).find(c => c.value === opt.value);
                            if (cb) cb.checked = false;
                            opt.selected = false;
                            renderTags();
                        });
                        chip.appendChild(closeBtn);
                        tagContainer.appendChild(chip);
                    }
                });
            };

            toggle.addEventListener('click', () => {
                dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
            });
            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            dropdown.addEventListener('change', syncFromCheckboxes);

            Array.from(dropdown.querySelectorAll('input[type="checkbox"]')).forEach(cb => {
                const isSelected = Array.from(selectEl.options).some(o => o.value === cb.value && o.selected);
                cb.checked = isSelected;
            });
            renderTags();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.chip-select').forEach(initChipDropdown);
        });

        CKEDITOR.replace('content', {
            height: 400,
            filebrowserUploadUrl: "{{ route('admin.blogs.upload-image') }}?_token={{ csrf_token() }}",
            filebrowserUploadMethod: 'form',
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Preview'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                '/',
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                '/',
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
            ],
            removeButtons: '',
            removePlugins: 'image',
            extraPlugins: 'uploadimage,image2',
            uploadUrl: "{{ route('admin.blogs.upload-image') }}?_token={{ csrf_token() }}",
            imageUploadUrl: "{{ route('admin.blogs.upload-image') }}?_token={{ csrf_token() }}",
            filebrowserImageUploadUrl: "{{ route('admin.blogs.upload-image') }}?_token={{ csrf_token() }}",
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
            
            fetch("{{ route('admin.blogs.cleanup-temp-images') }}", {
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
            navigator.sendBeacon("{{ route('admin.blogs.cleanup-temp-images') }}", new FormData());
        });
    </script>
@endpush

