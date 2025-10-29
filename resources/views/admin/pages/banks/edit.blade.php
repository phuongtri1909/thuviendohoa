@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa ngân hàng')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-university icon-title"></i>
                    <h5>Chỉnh sửa ngân hàng</h5>
                </div>
                <div class="category-meta">
                    <div class="category-badge name">
                        <i class="fas fa-university"></i>
                        <span>{{ $bank->name }}</span>
                    </div>
                    <div class="category-badge slug">
                        <i class="fas fa-code"></i>
                        <span>{{ $bank->code }}</span>
                    </div>
                    <div class="category-badge stories-count">
                        <i class="fas fa-credit-card"></i>
                        <span>{{ $bank->account_number }}</span>
                    </div>
                    <div class="category-badge created">
                        <i class="fas fa-calendar"></i>
                        <span>Ngày tạo: {{ $bank->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-body">
                

                <form action="{{ route('admin.banks.update', $bank) }}" method="POST" class="category-form" id="bank-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên ngân hàng <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" 
                                           class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                           value="{{ old('name', $bank->name) }}" required>
                                    <div class="error-message" id="error-name">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label-custom">
                                        Mã ngân hàng <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" id="code" name="code" 
                                           class="custom-input {{ $errors->has('code') ? 'input-error' : '' }}"
                                           value="{{ old('code', $bank->code) }}" required>
                                    <div class="error-message" id="error-code">
                                        @error('code')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="account_number" class="form-label-custom">
                                        Số tài khoản <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" id="account_number" name="account_number" 
                                           class="custom-input {{ $errors->has('account_number') ? 'input-error' : '' }}"
                                           value="{{ old('account_number', $bank->account_number) }}" required>
                                    <div class="error-message" id="error-account_number">
                                        @error('account_number')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="account_name" class="form-label-custom">
                                        Tên tài khoản <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" id="account_name" name="account_name" 
                                           class="custom-input {{ $errors->has('account_name') ? 'input-error' : '' }}"
                                           value="{{ old('account_name', $bank->account_name) }}" required>
                                    <div class="error-message" id="error-account_name">
                                        @error('account_name')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="logo" class="form-label-custom">
                                        Logo ngân hàng
                                    </label>
                                    <input type="file" id="logo" name="logo" accept="image/*" 
                                           class="custom-input {{ $errors->has('logo') ? 'input-error' : '' }}">
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Định dạng: jpeg, png, jpg, gif. Tối đa 2MB</span>
                                    </div>
                                    <div class="error-message" id="error-logo">
                                        @error('logo')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    
                                    @if($bank->logo)
                                        <div class="current-image-preview">
                                            <label class="form-label-custom">Logo hiện tại:</label>
                                            <div class="image-preview-container">
                                                <img src="{{ Storage::url($bank->logo) }}" alt="{{ $bank->name }}" class="preview-image">
                                                <div class="preview-overlay">
                                                    <span class="preview-text">Chọn file mới để thay thế</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="qr_code" class="form-label-custom">
                                        QR Code
                                    </label>
                                    <input type="file" id="qr_code" name="qr_code" accept="image/*" 
                                           class="custom-input {{ $errors->has('qr_code') ? 'input-error' : '' }}">
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Định dạng: jpeg, png, jpg, gif. Tối đa 2MB</span>
                                    </div>
                                    <div class="error-message" id="error-qr_code">
                                        @error('qr_code')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    
                                    @if($bank->qr_code)
                                        <div class="current-image-preview">
                                            <label class="form-label-custom">QR Code hiện tại:</label>
                                            <div class="image-preview-container">
                                                <img src="{{ Storage::url($bank->qr_code) }}" alt="QR Code" class="preview-image">
                                                <div class="preview-overlay">
                                                    <span class="preview-text">Chọn file mới để thay thế</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.banks.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .current-image-preview {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .image-preview-container {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }

        .preview-image {
            max-width: 120px;
            max-height: 120px;
            border-radius: 6px;
            border: 2px solid #dee2e6;
        }

        .preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-preview-container:hover .preview-overlay {
            opacity: 1;
        }

        .preview-text {
            font-size: 12px;
            text-align: center;
            padding: 5px;
        }
    </style>
@endpush
