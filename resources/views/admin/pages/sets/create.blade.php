@extends('admin.layouts.sidebar')

@section('title', 'Thêm set mới')

@section('main-content')
    <div class="category-form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-layer-group icon-title"></i>
                <h5>Thêm set mới</h5>
            </div>
        </div>

        <div class="form-body">
            

            <form action="{{ route('admin.sets.store') }}" method="POST" class="category-form" id="set-form" enctype="multipart/form-data">
                @csrf

                <div class="form-tabs">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label-custom">Tên set <span class="required-mark">*</span></label>
                                <input type="text" id="name" name="name" class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}" value="{{ old('name') }}" required>
                                <div class="error-message" id="error-name">@error('name') {{ $message }} @enderror</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="form-label-custom">Loại <span class="required-mark">*</span></label>
                                <select id="type" name="type" class="custom-input {{ $errors->has('type') ? 'input-error' : '' }}" required>
                                    <option value="{{ \App\Models\Set::TYPE_FREE }}" {{ old('type', \App\Models\Set::TYPE_FREE) === \App\Models\Set::TYPE_FREE ? 'selected' : '' }}>Miễn phí</option>
                                    <option value="{{ \App\Models\Set::TYPE_PREMIUM }}" {{ old('type') === \App\Models\Set::TYPE_PREMIUM ? 'selected' : '' }}>Premium</option>
                                </select>
                                <div class="error-message" id="error-type">@error('type') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="size" class="form-label-custom">Kích thước (MB) <span class="required-mark">*</span></label>
                                <input type="number" step="0.01" id="size" name="size" class="custom-input {{ $errors->has('size') ? 'input-error' : '' }}" value="{{ old('size') }}" required>
                                <div class="error-message" id="error-size">@error('size') {{ $message }} @enderror</div>
                            </div>
                        </div>
                        <div class="col-md-6" id="price-field" style="display: none;">
                            <div class="form-group">
                                <label for="price" class="form-label-custom">Giá (Xu) <span class="required-mark">*</span></label>
                                <input type="number" id="price" name="price" class="custom-input {{ $errors->has('price') ? 'input-error' : '' }}" value="{{ old('price') }}">
                                <div class="error-message" id="error-price">@error('price') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label for="is_featured" class="checkbox-label">Nổi bật</label>
                                </div>
                                <div class="error-message" id="error-is_featured">@error('is_featured') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image" class="form-label-custom">Logo (ảnh) <span class="required-mark">*</span></label>
                                <input type="file" id="image" name="image" accept="image/*" class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}" required>
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Định dạng: jpeg, png, jpg, gif, webp. Tối đa 10MB</span></div>
                                <div class="error-message" id="error-image">@error('image') {{ $message }} @enderror</div>
                                <div id="logo-preview" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="photos" class="form-label-custom">Ảnh trong set (tối thiểu 1 ảnh) <span class="required-mark">*</span></label>
                                <input type="file" id="photos" name="photos[]" accept="image/*" multiple class="custom-input {{ $errors->has('photos') || $errors->has('photos.*') ? 'input-error' : '' }}">
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Có thể chọn nhiều ảnh. Mỗi ảnh tối đa 10MB</span></div>
                                <div class="error-message" id="error-photos">
                                    @error('photos') {{ $message }} @enderror
                                    @error('photos.*') {{ $message }} @enderror
                                </div>
                                <div id="photo-preview" class="mt-2" style="display:flex;flex-wrap:wrap;gap:10px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="keywords" class="form-label-custom">Từ khóa (JSON)</label>
                                <textarea id="keywords" name="keywords" rows="2" class="custom-input {{ $errors->has('keywords') ? 'input-error' : '' }}">{{ old('keywords') }}</textarea>
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Ví dụ: ["logo","branding"]</span></div>
                                <div class="error-message" id="error-keywords">@error('keywords') {{ $message }} @enderror</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="formats" class="form-label-custom">Định dạng (JSON)</label>
                                <textarea id="formats" name="formats" rows="2" class="custom-input {{ $errors->has('formats') ? 'input-error' : '' }}">{{ old('formats') }}</textarea>
                                <div class="form-hint"><i class="fas fa-info-circle"></i><span>Ví dụ: ["AI","PSD","PNG"]</span></div>
                                <div class="error-message" id="error-formats">@error('formats') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description" class="form-label-custom">Mô tả <span class="required-mark">*</span></label>
                                <textarea id="description" name="description" rows="4" class="custom-input {{ $errors->has('description') ? 'input-error' : '' }}" required>{{ old('description') }}</textarea>
                                <div class="error-message" id="error-description">@error('description') {{ $message }} @enderror</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="drive_url" class="form-label-custom">URL Drive <span class="required-mark">*</span></label>
                                <input type="url" id="drive_url" name="drive_url" class="custom-input {{ $errors->has('drive_url') ? 'input-error' : '' }}" value="{{ old('drive_url') }}" required>
                                <div class="error-message" id="error-drive_url">@error('drive_url') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">Trạng thái</label>
                                <label class="d-flex align-items-center" style="gap:6px;">
                                    <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}> Kích hoạt
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            @isset($albums)
                            <div class="form-group">
                                <label for="album_ids" class="form-label-custom">Albums (tùy chọn)</label>
                                <div class="chip-select" data-select-id="album_ids" style="position:relative;">
                                    <div class="chip-select-toggle custom-input" tabindex="0">Chọn albums...</div>
                                    <div class="chip-select-dropdown" style="position:absolute;left:0;right:0;z-index:20;background:#fff;border:1px solid #e9ecef;border-radius:6px;display:none;max-height:220px;overflow:auto;">
                                        @foreach($albums as $al)
                                            <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;">
                                                <input type="checkbox" value="{{ $al->id }}" data-text="{{ $al->name }}" {{ collect(old('album_ids', []))->contains($al->id) ? 'checked' : '' }}>
                                                <span>{{ $al->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div id="album_ids_tags" class="selected-tags" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;"></div>
                                    <select id="album_ids" name="album_ids[]" multiple style="display:none;">
                                        @foreach($albums as $al)
                                            <option value="{{ $al->id }}" {{ collect(old('album_ids', []))->contains($al->id) ? 'selected' : '' }}>{{ $al->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endisset
                        </div>
                        <div class="col-md-6">
                            @isset($categories)
                            <div class="form-group">
                                <label for="category_ids" class="form-label-custom">Danh mục (tùy chọn)</label>
                                <div class="chip-select" data-select-id="category_ids" style="position:relative;">
                                    <div class="chip-select-toggle custom-input" tabindex="0">Chọn danh mục...</div>
                                    <div class="chip-select-dropdown" style="position:absolute;left:0;right:0;z-index:20;background:#fff;border:1px solid #e9ecef;border-radius:6px;display:none;max-height:220px;overflow:auto;">
                                        @foreach($categories as $ct)
                                            <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;">
                                                <input type="checkbox" value="{{ $ct->id }}" data-text="{{ $ct->name }}" {{ collect(old('category_ids', []))->contains($ct->id) ? 'checked' : '' }}>
                                                <span>{{ $ct->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div id="category_ids_tags" class="selected-tags" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;"></div>
                                    <select id="category_ids" name="category_ids[]" multiple style="display:none;">
                                        @foreach($categories as $ct)
                                            <option value="{{ $ct->id }}" {{ collect(old('category_ids', []))->contains($ct->id) ? 'selected' : '' }}>{{ $ct->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endisset
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            @isset($colors)
                            <div class="form-group">
                                <label for="color_ids" class="form-label-custom">Màu sắc (tùy chọn)</label>
                                <div class="chip-select" data-select-id="color_ids" style="position:relative;">
                                    <div class="chip-select-toggle custom-input" tabindex="0">Chọn màu sắc...</div>
                                    <div class="chip-select-dropdown" style="position:absolute;left:0;right:0;z-index:20;background:#fff;border:1px solid #e9ecef;border-radius:6px;display:none;max-height:220px;overflow:auto;">
                                        @foreach($colors as $cl)
                                       
                                            <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;">
                                                <input type="checkbox" value="{{ $cl->id }}" data-text="{{ $cl->name }}" {{ collect(old('color_ids', []))->contains($cl->id) ? 'checked' : '' }}>
                                                <span style="display:flex;align-items:center;gap:6px;">
                                                    <span style="width:16px;height:16px;border-radius:3px;border:1px solid #ddd;background-color:{{ $cl->value }};"></span>
                                                    <span>{{ $cl->name }}</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div id="color_ids_tags" class="selected-tags" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;"></div>
                                    <select id="color_ids" name="color_ids[]" multiple style="display:none;">
                                        @foreach($colors as $cl)
                                            <option value="{{ $cl->id }}" {{ collect(old('color_ids', []))->contains($cl->id) ? 'selected' : '' }}>{{ $cl->name }} ({{ $cl->value }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endisset
                        </div>
                        <div class="col-md-6">
                            @isset($software)
                            <div class="form-group">
                                <label for="software_ids" class="form-label-custom">Phần mềm (tùy chọn)</label>
                                <div class="chip-select" data-select-id="software_ids" style="position:relative;">
                                    <div class="chip-select-toggle custom-input" tabindex="0">Chọn phần mềm...</div>
                                    <div class="chip-select-dropdown" style="position:absolute;left:0;right:0;z-index:20;background:#fff;border:1px solid #e9ecef;border-radius:6px;display:none;max-height:220px;overflow:auto;">
                                        @foreach($software as $sw)
                                            <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;">
                                                <input type="checkbox" value="{{ $sw->id }}" data-text="{{ $sw->name }}" {{ collect(old('software_ids', []))->contains($sw->id) ? 'checked' : '' }}>
                                                <span>{{ $sw->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div id="software_ids_tags" class="selected-tags" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;"></div>
                                    <select id="software_ids" name="software_ids[]" multiple style="display:none;">
                                        @foreach($software as $sw)
                                            <option value="{{ $sw->id }}" {{ collect(old('software_ids', []))->contains($sw->id) ? 'selected' : '' }}>{{ $sw->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endisset
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            @isset($tags)
                            <div class="form-group">
                                <label for="tag_ids" class="form-label-custom">Tags (tùy chọn)</label>
                                <div class="chip-select" data-select-id="tag_ids" style="position:relative;">
                                    <div class="chip-select-toggle custom-input" tabindex="0">Chọn tags...</div>
                                    <div class="chip-select-dropdown" style="position:absolute;left:0;right:0;z-index:20;background:#fff;border:1px solid #e9ecef;border-radius:6px;display:none;max-height:220px;overflow:auto;">
                                        @foreach($tags as $tg)
                                            <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;">
                                                <input type="checkbox" value="{{ $tg->id }}" data-text="{{ $tg->name }}" {{ collect(old('tag_ids', []))->contains($tg->id) ? 'checked' : '' }}>
                                                <span>{{ $tg->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div id="tag_ids_tags" class="selected-tags" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;"></div>
                                    <select id="tag_ids" name="tag_ids[]" multiple style="display:none;">
                                        @foreach($tags as $tg)
                                            <option value="{{ $tg->id }}" {{ collect(old('tag_ids', []))->contains($tg->id) ? 'selected' : '' }}>{{ $tg->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endisset
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.sets.index') }}" class="back-button"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    <button type="submit" class="save-button"><i class="fas fa-save"></i> Tạo set</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
<script>
function initChipDropdown(wrapper) {
    const selectId = wrapper.getAttribute('data-select-id');
    const selectEl = document.getElementById(selectId);
    const toggle = wrapper.querySelector('.chip-select-toggle');
    const dropdown = wrapper.querySelector('.chip-select-dropdown');
    const tagContainer = wrapper.querySelector('.selected-tags');

    const syncFromCheckboxes = () => {
        const checked = Array.from(dropdown.querySelectorAll('input[type="checkbox"]:checked'));
        // update select options selected state
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
                    // uncheck in dropdown and deselect option
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

    // Toggle dropdown
    toggle.addEventListener('click', () => {
        dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
    });
    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Handle checkbox changes
    dropdown.addEventListener('change', syncFromCheckboxes);

    // Initial render (preserve old input)
    // ensure checkboxes reflect old selected options
    Array.from(dropdown.querySelectorAll('input[type="checkbox"]')).forEach(cb => {
        const isSelected = Array.from(selectEl.options).some(o => o.value === cb.value && o.selected);
        cb.checked = isSelected;
    });
    renderTags();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.chip-select').forEach(initChipDropdown);
    
    // Logo preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('logo-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Logo preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">`;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });
    
    // Toggle price field based on type
    document.getElementById('type').addEventListener('change', function(e) {
        const priceField = document.getElementById('price-field');
        const priceInput = document.getElementById('price');
        
        if (e.target.value === 'premium') {
            priceField.style.display = 'block';
            priceInput.required = true;
        } else {
            priceField.style.display = 'none';
            priceInput.required = false;
            priceInput.value = '';
        }
    });
    
    // Initialize price field visibility
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const priceField = document.getElementById('price-field');
        const priceInput = document.getElementById('price');
        
        if (typeSelect.value === 'premium') {
            priceField.style.display = 'block';
            priceInput.required = true;
        }
    });
    
    // Photos preview with remove functionality
    let selectedPhotos = [];
    
    document.getElementById('photos').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const preview = document.getElementById('photo-preview');
        
        // Add new files to existing selection
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const photoId = 'photo_' + Date.now() + '_' + index;
                const photoData = {
                    id: photoId,
                    file: file,
                    url: e.target.result
                };
                selectedPhotos.push(photoData);
                
                const photoContainer = document.createElement('div');
                photoContainer.id = photoId;
                photoContainer.style.cssText = 'position: relative; display: inline-block; margin: 5px;';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = `Photo ${selectedPhotos.length}`;
                img.style.cssText = 'width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '×';
                removeBtn.style.cssText = 'position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; border-radius: 50%; background: #dc3545; color: white; border: none; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center;';
                removeBtn.addEventListener('click', function() {
                    // Remove from selectedPhotos array
                    selectedPhotos = selectedPhotos.filter(p => p.id !== photoId);
                    
                    // Remove from DOM with smooth animation
                    photoContainer.style.transition = 'all 0.3s ease';
                    photoContainer.style.opacity = '0';
                    photoContainer.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        photoContainer.remove();
                        // Update file input
                        updateFileInput();
                    }, 300);
                });
                
                photoContainer.appendChild(img);
                photoContainer.appendChild(removeBtn);
                preview.appendChild(photoContainer);
            };
            reader.readAsDataURL(file);
        });
        
        // Clear the file input so same files can be selected again
        e.target.value = '';
    });
    
    function updateFileInput() {
        // Create a new FileList with remaining files
        const dt = new DataTransfer();
        selectedPhotos.forEach(photo => {
            dt.items.add(photo.file);
        });
        document.getElementById('photos').files = dt.files;
        
        // Update required attribute based on selection
        const photosInput = document.getElementById('photos');
        if (selectedPhotos.length > 0) {
            photosInput.required = false;
        } else {
            photosInput.required = true;
        }
    }
    
    // Custom validation before form submit
    document.getElementById('set-form').addEventListener('submit', function(e) {
        const photosInput = document.getElementById('photos');
        
        // Check if we have selected photos
        if (selectedPhotos.length === 0) {
            e.preventDefault();
            // Show error message in the photos error div
            const errorDiv = document.getElementById('error-photos');
            if (errorDiv) {
                errorDiv.innerHTML = '<span style="color: #dc3545;">Phải tải lên ít nhất 1 ảnh cho set</span>';
            }
            return false;
        }
        
        // Ensure photos are properly set in the input
        const dt = new DataTransfer();
        selectedPhotos.forEach(photo => {
            dt.items.add(photo.file);
        });
        photosInput.files = dt.files;
    });
});
</script>
@endpush


