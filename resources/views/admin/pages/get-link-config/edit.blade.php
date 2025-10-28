@extends('admin.layouts.sidebar')

@section('title', 'Cấu hình Get Link')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-cog icon-title"></i>
                    <h5>Cấu hình Get Link</h5>
                </div>
            </div>

            <div class="form-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.get-link-config.update') }}" method="POST" class="category-form">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="coins" class="form-label-custom">
                                Số xu cần trừ mỗi lần get link <span class="required-mark">*</span>
                            </label>
                            <input type="number" id="coins" name="coins" 
                                   class="custom-input {{ $errors->has('coins') ? 'input-error' : '' }}"
                                   value="{{ old('coins', $config->coins) }}" 
                                   placeholder="VD: 5"
                                   min="1"
                                   max="1000"
                                   required>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Số xu sẽ được trừ từ tài khoản người dùng mỗi khi họ thực hiện get link thành công.
                            </small>
                            <div class="error-message" id="error-coins">
                                @error('coins')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn-cancel">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Lưu cấu hình
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.5;
        }

        .btn-close:hover {
            opacity: 1;
        }
    </style>
@endsection

