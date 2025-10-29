@extends('admin.layouts.sidebar')

@section('title', 'Thêm ngân hàng')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-university icon-title"></i>
                    <h5>Thêm ngân hàng mới</h5>
                </div>
            </div>

            <div class="form-body">
                

                <form action="{{ route('admin.banks.store') }}" method="POST" class="category-form" id="bank-form" enctype="multipart/form-data">
                    @csrf

                    <div class="form-tabs">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên ngân hàng <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" 
                                           class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                           value="{{ old('name') }}" required>
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
                                           value="{{ old('code') }}" required>
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
                                           value="{{ old('account_number') }}" required>
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
                                           value="{{ old('account_name') }}" required>
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.banks.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Tạo ngân hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
