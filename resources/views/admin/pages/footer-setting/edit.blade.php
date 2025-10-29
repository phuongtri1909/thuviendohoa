@extends('admin.layouts.sidebar')

@section('title', 'Cài đặt Footer')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-cogs icon-title"></i>
                    <h5>Cài đặt Footer</h5>
                    <small class="text-muted">Quản lý thông tin hiển thị ở Footer trang web</small>
                </div>
            </div>

            <div class="form-body">
                

                <form action="{{ route('admin.footer-setting.update') }}" method="POST" class="category-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <h6 class="section-title">
                            <i class="fab fa-facebook"></i> Thông tin Facebook
                        </h6>
                        <div class="form-group">
                            <label for="facebook_url" class="form-label-custom">
                                URL Trang Facebook
                            </label>
                            <input type="url" id="facebook_url" name="facebook_url" 
                                   class="custom-input {{ $errors->has('facebook_url') ? 'input-error' : '' }}"
                                   value="{{ old('facebook_url', $setting->facebook_url) }}"
                                   placeholder="https://www.facebook.com/...">
                            <div class="error-message">
                                @error('facebook_url')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="section-title">
                            <i class="fas fa-headset"></i> Thông tin hỗ trợ
                        </h6>
                        <div class="form-group">
                            <label for="support_hotline" class="form-label-custom">
                                Hotline/Zalo
                            </label>
                            <input type="text" id="support_hotline" name="support_hotline" 
                                   class="custom-input {{ $errors->has('support_hotline') ? 'input-error' : '' }}"
                                   value="{{ old('support_hotline', $setting->support_hotline) }}"
                                   placeholder="0944 133 994">
                            <div class="error-message">
                                @error('support_hotline')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="support_email" class="form-label-custom">
                                Email
                            </label>
                            <input type="email" id="support_email" name="support_email" 
                                   class="custom-input {{ $errors->has('support_email') ? 'input-error' : '' }}"
                                   value="{{ old('support_email', $setting->support_email) }}"
                                   placeholder="printon.hcm@gmail.com">
                            <div class="error-message">
                                @error('support_email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="support_fanpage" class="form-label-custom">
                                Tên Fanpage
                            </label>
                            <input type="text" id="support_fanpage" name="support_fanpage" 
                                   class="custom-input {{ $errors->has('support_fanpage') ? 'input-error' : '' }}"
                                   value="{{ old('support_fanpage', $setting->support_fanpage) }}"
                                   placeholder="Printon">
                            <div class="error-message">
                                @error('support_fanpage')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="support_fanpage_url" class="form-label-custom">
                                URL Fanpage
                            </label>
                            <input type="url" id="support_fanpage_url" name="support_fanpage_url" 
                                   class="custom-input {{ $errors->has('support_fanpage_url') ? 'input-error' : '' }}"
                                   value="{{ old('support_fanpage_url', $setting->support_fanpage_url) }}"
                                   placeholder="https://www.facebook.com/...">
                            <div class="error-message">
                                @error('support_fanpage_url')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="section-title">
                            <i class="fas fa-handshake"></i> Đối tác
                        </h6>
                        
                        <div id="partners-container">
                            @if($setting->partners && count($setting->partners) > 0)
                                @foreach($setting->partners as $index => $partner)
                                    <div class="partner-item" data-index="{{ $index }}">
                                        <div class="partner-header">
                                            <span class="partner-number">Đối tác #{{ $index + 1 }}</span>
                                            <button type="button" class="btn-remove-partner" onclick="removePartner({{ $index }})">
                                                <i class="fas fa-times"></i> Xóa
                                            </button>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label-custom">Tên đối tác</label>
                                                    <input type="text" name="partners[{{ $index }}][name]" 
                                                           class="custom-input"
                                                           value="{{ old('partners.'.$index.'.name', $partner['name'] ?? '') }}"
                                                           placeholder="TheGioiInAn">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label-custom">Logo đối tác</label>
                                                    <input type="file" name="partners[{{ $index }}][image]" 
                                                           class="custom-input" accept="image/*">
                                                    <input type="hidden" name="partners[{{ $index }}][existing_image]" 
                                                           value="{{ $partner['image'] ?? '' }}">
                                                    @if(!empty($partner['image']))
                                                        <div class="mt-2">
                                                            <img src="{{ Storage::url($partner['image']) }}" 
                                                                 alt="Partner" class="partner-preview">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label-custom">URL liên kết (không bắt buộc)</label>
                                                    <input type="url" name="partners[{{ $index }}][url]" 
                                                           class="custom-input"
                                                           value="{{ old('partners.'.$index.'.url', $partner['url'] ?? '') }}"
                                                           placeholder="https://...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button type="button" class="action-button" onclick="addPartner()">
                            <i class="fas fa-plus"></i> Thêm đối tác
                        </button>
                    </div>

                    <div class="form-actions">
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
        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .partner-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .partner-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .partner-number {
            font-weight: 600;
            color: #495057;
        }

        .btn-remove-partner {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-remove-partner:hover {
            background: #c82333;
        }

        .partner-preview {
            max-width: 150px;
            max-height: 100px;
            object-fit: contain;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let partnerIndex = {{ $setting->partners ? count($setting->partners) : 0 }};

        function addPartner() {
            const container = document.getElementById('partners-container');
            const partnerItem = `
                <div class="partner-item" data-index="${partnerIndex}">
                    <div class="partner-header">
                        <span class="partner-number">Đối tác #${partnerIndex + 1}</span>
                        <button type="button" class="btn-remove-partner" onclick="removePartnerElement(this)">
                            <i class="fas fa-times"></i> Xóa
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label-custom">Tên đối tác</label>
                                <input type="text" name="partners[${partnerIndex}][name]" 
                                       class="custom-input"
                                       placeholder="TheGioiInAn">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label-custom">Logo đối tác</label>
                                <input type="file" name="partners[${partnerIndex}][image]" 
                                       class="custom-input" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label-custom">URL liên kết (không bắt buộc)</label>
                                <input type="url" name="partners[${partnerIndex}][url]" 
                                       class="custom-input"
                                       placeholder="https://...">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', partnerItem);
            partnerIndex++;
        }

        function removePartnerElement(button) {
            const partnerItem = button.closest('.partner-item');
            partnerItem.remove();
        }

        function removePartner(index) {
            if (confirm('Bạn có chắc chắn muốn xóa đối tác này?')) {
                const partnerItem = document.querySelector(`.partner-item[data-index="${index}"]`);
                partnerItem.remove();
            }
        }
    </script>
@endpush

