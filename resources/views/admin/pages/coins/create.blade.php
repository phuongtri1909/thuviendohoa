@extends('admin.layouts.sidebar')

@section('title', 'Cộng xu cho người dùng')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-coins icon-title"></i>
                    <h5>Cộng xu cho người dùng</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.coins.store') }}" method="POST" class="category-form" id="coin-form">
                    @csrf

                    <div class="form-tabs">
                        <!-- Operation Type Selection -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label-custom">Loại thao tác <span class="required-mark">*</span></label>
                                    <div class="operation-type-selector">
                                        <label class="operation-type-option">
                                            <input type="radio" name="operation_type" value="individual" {{ old('operation_type', 'individual') === 'individual' ? 'checked' : '' }}>
                                            <div class="operation-card">
                                                <i class="fas fa-user"></i>
                                                <h6>Chọn từng người dùng</h6>
                                                <p>Cộng xu cho những người dùng được chọn cụ thể</p>
                                            </div>
                                        </label>
                                        <label class="operation-type-option">
                                            <input type="radio" name="operation_type" value="package" {{ old('operation_type') === 'package' ? 'checked' : '' }}>
                                            <div class="operation-card">
                                                <i class="fas fa-gift"></i>
                                                <h6>Chọn theo gói</h6>
                                                <p>Cộng xu cho tất cả người dùng có gói được chọn</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="error-message" id="error-operation_type">@error('operation_type') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Type and Amount -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">Loại thao tác xu <span class="required-mark">*</span></label>
                                    <div class="amount-type-selector">
                                        <label class="amount-type-option">
                                            <input type="radio" name="amount_type" value="add" {{ old('amount_type', request('subtract_user') ? 'subtract' : 'add') === 'add' ? 'checked' : '' }}>
                                            <div class="amount-type-card add">
                                                <i class="fas fa-plus"></i>
                                                <h6>Cộng xu</h6>
                                                <p>Thêm xu vào tài khoản</p>
                                            </div>
                                        </label>
                                        <label class="amount-type-option">
                                            <input type="radio" name="amount_type" value="subtract" {{ old('amount_type', request('subtract_user') ? 'subtract' : 'add') === 'subtract' ? 'checked' : '' }}>
                                            <div class="amount-type-card subtract">
                                                <i class="fas fa-minus"></i>
                                                <h6>Trừ xu</h6>
                                                <p>Trừ xu khỏi tài khoản</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="error-message" id="error-amount_type">@error('amount_type') {{ $message }} @enderror</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount" class="form-label-custom">Số xu <span class="required-mark">*</span></label>
                                    <input type="number" id="amount" name="amount" class="custom-input {{ $errors->has('amount') ? 'input-error' : '' }}" 
                                           value="{{ old('amount', request('amount')) }}" min="1" required>
                                    <div class="form-hint"><i class="fas fa-info-circle"></i><span id="amount-hint">Nhập số xu muốn cộng cho người dùng</span></div>
                                    <div class="error-message" id="error-amount">@error('amount') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reason" class="form-label-custom">Lý do <span class="required-mark">*</span></label>
                                    <input type="text" id="reason" name="reason" class="custom-input {{ $errors->has('reason') ? 'input-error' : '' }}" 
                                           value="{{ old('reason') }}" placeholder="Ví dụ: Thưởng hoạt động, Bù lỗi hệ thống..." required>
                                    <div class="error-message" id="error-reason">@error('reason') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="note" class="form-label-custom">Ghi chú thêm</label>
                                    <textarea id="note" name="note" rows="3" class="custom-input {{ $errors->has('note') ? 'input-error' : '' }}" 
                                              placeholder="Ghi chú chi tiết về việc cộng xu này...">{{ old('note') }}</textarea>
                                    <div class="error-message" id="error-note">@error('note') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        </div>

                        <!-- Individual User Selection -->
                        <div id="individual-selection" class="selection-section" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="user_ids" class="form-label-custom">Chọn người dùng <span class="required-mark">*</span></label>
                                        
                                        <!-- Search input -->
                                        <div class="user-search-container">
                                            <input type="text" id="user-search" class="custom-input" placeholder="Tìm kiếm theo tên hoặc email..." style="margin-bottom: 10px;">
                                        </div>
                                        
                                        <div class="chip-select" data-select-id="user_ids" style="position:relative;">
                                            <div class="chip-select-toggle custom-input" tabindex="0">Chọn người dùng...</div>
                                            <div class="chip-select-dropdown" style="position:absolute;left:0;right:0;z-index:20;background:#fff;border:1px solid #e9ecef;border-radius:6px;display:none;max-height:220px;overflow:auto;">
                                                <div id="user-list-container">
                                                    @foreach($users as $user)
                                                        <label class="user-option" data-user-name="{{ strtolower($user->full_name) }}" data-user-email="{{ strtolower($user->email) }}" style="display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;">
                                                            <input type="checkbox" value="{{ $user->id }}" data-text="{{ $user->full_name }} ({{ $user->email }})" {{ collect(old('user_ids', []))->contains($user->id) ? 'checked' : '' }}>
                                                            <span style="display:flex;align-items:center;gap:8px;flex:1;">
                                                                <span>{{ $user->full_name }}</span>
                                                                <small class="text-muted">({{ $user->email }})</small>
                                                                @if($user->package)
                                                                    <span class="package-badge">{{ $user->package->name }}</span>
                                                                @endif
                                                                <span class="coins-info">{{ number_format($user->coins) }} xu</span>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div id="user_ids_tags" class="selected-tags" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;"></div>
                                            <select id="user_ids" name="user_ids[]" multiple style="display:none;">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ collect(old('user_ids', []))->contains($user->id) ? 'selected' : '' }}>{{ $user->full_name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="error-message" id="error-user_ids">@error('user_ids') {{ $message }} @enderror</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Package Selection -->
                        <div id="package-selection" class="selection-section" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="package_id" class="form-label-custom">Chọn gói <span class="required-mark">*</span></label>
                                        <select id="package_id" name="package_id" class="custom-input {{ $errors->has('package_id') ? 'input-error' : '' }}">
                                            <option value="">Chọn gói...</option>
                                            @foreach($packages as $package)
                                                <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                                    {{ $package->name }} ({{ $package->plan }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-hint"><i class="fas fa-info-circle"></i><span>Tất cả người dùng có gói này sẽ được cộng xu</span></div>
                                        <div class="error-message" id="error-package_id">@error('package_id') {{ $message }} @enderror</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Package Users Preview -->
                            <div id="package-users-preview" style="display: none;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Người dùng sẽ được cộng xu</label>
                                            <div id="package-users-list" class="package-users-list">
                                                <!-- Users will be loaded here via AJAX -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.coins.index') }}" class="back-button"><i class="fas fa-arrow-left"></i> Quay lại</a>
                        <button type="submit" class="save-button"><i class="fas fa-coins"></i> Cộng xu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .operation-type-selector {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .operation-type-option {
            flex: 1;
            cursor: pointer;
        }

        .operation-type-option input[type="radio"] {
            display: none;
        }

        .operation-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            background: #fff;
        }

        .operation-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .operation-type-option input[type="radio"]:checked + .operation-card {
            border-color: var(--primary-color);
            background: #f8f9ff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .operation-card i {
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .operation-card h6 {
            margin: 10px 0 5px 0;
            color: #333;
            font-weight: 600;
        }

        .operation-card p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .amount-type-selector {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .amount-type-option {
            flex: 1;
            cursor: pointer;
        }

        .amount-type-option input[type="radio"] {
            display: none;
        }

        .amount-type-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            background: #fff;
        }

        .amount-type-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .amount-type-option input[type="radio"]:checked + .amount-type-card {
            border-color: var(--primary-color);
            background: #f8f9ff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .amount-type-card i {
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .amount-type-card.add i {
            color: #28a745;
        }

        .amount-type-card.subtract i {
            color: #dc3545;
        }

        .amount-type-option input[type="radio"]:checked + .amount-type-card.add {
            border-color: #28a745;
            background: #f8fff9;
        }

        .amount-type-option input[type="radio"]:checked + .amount-type-card.subtract {
            border-color: #dc3545;
            background: #fff8f8;
        }

        .amount-type-card h6 {
            margin: 10px 0 5px 0;
            color: #333;
            font-weight: 600;
        }

        .amount-type-card p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .selection-section {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .package-badge {
            background: #e3f2fd;
            color: var(--primary-color);
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .coins-info {
            color: #f57c00;
            font-weight: 600;
            font-size: 12px;
        }

        .package-users-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            background: #fff;
        }

        .package-user-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .package-user-item:last-child {
            border-bottom: none;
        }

        .package-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .package-user-name {
            font-weight: 600;
            color: #333;
        }

        .package-user-email {
            color: #6c757d;
            font-size: 12px;
        }

        .package-user-coins {
            color: #f57c00;
            font-weight: 600;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .operation-type-selector {
                flex-direction: column;
            }
        }
    </style>
@endpush

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
    Array.from(dropdown.querySelectorAll('input[type="checkbox"]')).forEach(cb => {
        const isSelected = Array.from(selectEl.options).some(o => o.value === cb.value && o.selected);
        cb.checked = isSelected;
    });
    renderTags();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize chip dropdown
    document.querySelectorAll('.chip-select').forEach(initChipDropdown);
    
    // Handle operation type change
    const operationTypeRadios = document.querySelectorAll('input[name="operation_type"]');
    const individualSection = document.getElementById('individual-selection');
    const packageSection = document.getElementById('package-selection');
    
    function toggleSections() {
        const selectedType = document.querySelector('input[name="operation_type"]:checked').value;
        
        if (selectedType === 'individual') {
            individualSection.style.display = 'block';
            packageSection.style.display = 'none';
        } else {
            individualSection.style.display = 'none';
            packageSection.style.display = 'block';
        }
    }
    
    operationTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleSections);
    });
    
    // Initial state
    toggleSections();
    
    // User search functionality
    const userSearchInput = document.getElementById('user-search');
    const userOptions = document.querySelectorAll('.user-option');
    
    userSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        userOptions.forEach(option => {
            const userName = option.getAttribute('data-user-name');
            const userEmail = option.getAttribute('data-user-email');
            
            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                option.style.display = 'flex';
            } else {
                option.style.display = 'none';
            }
        });
    });
    
    // Handle package selection change
    const packageSelect = document.getElementById('package_id');
    const packagePreview = document.getElementById('package-users-preview');
    const packageUsersList = document.getElementById('package-users-list');
    
    packageSelect.addEventListener('change', function() {
        const packageId = this.value;
        
        if (packageId) {
            // Show loading
            packageUsersList.innerHTML = '<div style="padding: 20px; text-align: center; color: #6c757d;"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
            packagePreview.style.display = 'block';
            
            // Fetch users with this package
            fetch(`/admin/coins/package-users/${packageId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.users.length > 0) {
                        packageUsersList.innerHTML = data.users.map(user => `
                            <div class="package-user-item">
                                <div class="package-user-info">
                                    <div>
                                        <div class="package-user-name">${user.full_name}</div>
                                        <div class="package-user-email">${user.email}</div>
                                    </div>
                                </div>
                                <div class="package-user-coins">${user.coins.toLocaleString()} xu</div>
                            </div>
                        `).join('');
                    } else {
                        packageUsersList.innerHTML = '<div style="padding: 20px; text-align: center; color: #6c757d;">Không có người dùng nào có gói này</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    packageUsersList.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;">Có lỗi xảy ra khi tải dữ liệu</div>';
                });
        } else {
            packagePreview.style.display = 'none';
        }
    });
    
    // Amount type change handler
    const amountTypeRadios = document.querySelectorAll('input[name="amount_type"]');
    const amountHint = document.getElementById('amount-hint');
    
    function updateAmountHint() {
        const checkedRadio = document.querySelector('input[name="amount_type"]:checked');
        if (checkedRadio) {
            if (checkedRadio.value === 'add') {
                amountHint.textContent = 'Nhập số xu muốn cộng cho người dùng';
            } else {
                amountHint.textContent = 'Nhập số xu muốn trừ khỏi tài khoản';
            }
        }
    }
    
    // Initialize hint text
    updateAmountHint();
    
    amountTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateAmountHint);
    });

    // Form validation
    document.getElementById('coin-form').addEventListener('submit', function(e) {
        // Let server handle validation
    });
});
</script>
@endpush
