@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa gói xu')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-coins icon-title"></i>
                    <h5>Chỉnh sửa gói xu</h5>
                </div>
                <div class="category-meta">
                    <div class="category-badge name">
                        <i class="fas fa-coins"></i>
                        <span>{{ $package->name }}</span>
                    </div>
                    <div class="category-badge slug">
                        <i class="fas fa-tag"></i>
                        <span class="package-plan-badge package-plan-{{ $package->plan }}">
                            {{ ucfirst($package->plan) }}
                        </span>
                    </div>
                    <div class="category-badge stories-count">
                        <i class="fas fa-money-bill"></i>
                        <span>{{ number_format($package->amount) }} VNĐ</span>
                    </div>
                    <div class="category-badge created">
                        <i class="fas fa-calendar"></i>
                        <span>Ngày tạo: {{ $package->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-body">
                

                <form action="{{ route('admin.packages.update', $package) }}" method="POST" class="category-form" id="package-form">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên gói xu <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" 
                                           class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                           value="{{ old('name', $package->name) }}" required>
                                    <div class="error-message" id="error-name">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">Loại gói</label>
                                    <div class="package-plan-display">
                                        <span class="package-plan-badge package-plan-{{ $package->plan }}">
                                            {{ ucfirst($package->plan) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="amount" class="form-label-custom">
                                        Giá (VNĐ) <span class="required-mark">*</span>
                                    </label>
                                    <input type="number" id="amount" name="amount" 
                                           class="custom-input {{ $errors->has('amount') ? 'input-error' : '' }}"
                                           value="{{ old('amount', $package->amount) }}" min="0" required>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Giá bán của gói xu (đơn vị: VNĐ)</span>
                                    </div>
                                    <div class="error-message" id="error-amount">
                                        @error('amount')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="coins" class="form-label-custom">
                                        Số xu <span class="required-mark">*</span>
                                    </label>
                                    <input type="number" id="coins" name="coins" 
                                           class="custom-input {{ $errors->has('coins') ? 'input-error' : '' }}"
                                           value="{{ old('coins', $package->coins) }}" min="0" required>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Số xu người dùng nhận được</span>
                                    </div>
                                    <div class="error-message" id="error-coins">
                                        @error('coins')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="bonus_coins" class="form-label-custom">
                                        Xu thưởng <span class="required-mark">*</span>
                                    </label>
                                    <input type="number" id="bonus_coins" name="bonus_coins" 
                                           class="custom-input {{ $errors->has('bonus_coins') ? 'input-error' : '' }}"
                                           value="{{ old('bonus_coins', $package->bonus_coins) }}" min="0" required>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Số xu thưởng thêm (có thể = 0)</span>
                                    </div>
                                    <div class="error-message" id="error-bonus_coins">
                                        @error('bonus_coins')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="expiry" class="form-label-custom">
                                        Hạn sử dụng (tháng) <span class="required-mark">*</span>
                                    </label>
                                    <input type="number" id="expiry" name="expiry" 
                                           class="custom-input {{ $errors->has('expiry') ? 'input-error' : '' }}"
                                           value="{{ old('expiry', $package->expiry) }}" min="1" required>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Số tháng xu có hiệu lực (tối thiểu 1 tháng)</span>
                                    </div>
                                    <div class="error-message" id="error-expiry">
                                        @error('expiry')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.packages.index') }}" class="back-button">
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
        .package-plan-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .package-plan-bronze {
            background: #cd7f32;
            color: white;
        }

        .package-plan-silver {
            background: #c0c0c0;
            color: #333;
        }

        .package-plan-gold {
            background: #ffd700;
            color: #333;
        }

        .package-plan-platinum {
            background: #e5e4e2;
            color: #333;
        }

        .package-plan-display {
            padding: 8px 0;
        }

        .package-plan-badge {
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
@endpush
