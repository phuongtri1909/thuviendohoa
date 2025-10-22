@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết ngân hàng')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-university icon-title"></i>
                    <h5>Chi tiết ngân hàng</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.banks.edit', $bank) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>

                    <a href="{{ route('admin.banks.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="category-details">
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">ID:</label>
                            <span class="detail-value">{{ $bank->id }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Tên ngân hàng:</label>
                            <span class="detail-value">{{ $bank->name }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Mã ngân hàng:</label>
                            <span class="detail-value bank-code-badge">{{ $bank->code }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Số tài khoản:</label>
                            <span class="detail-value account-number">{{ $bank->account_number }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Tên tài khoản:</label>
                            <span class="detail-value">{{ $bank->account_name }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Ngày tạo:</label>
                            <span class="detail-value">{{ $bank->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Cập nhật lần cuối:</label>
                            <span class="detail-value">{{ $bank->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">Logo ngân hàng:</label>
                            <div class="detail-value">
                                @if ($bank->logo)
                                    <img src="{{ Storage::url($bank->logo) }}" alt="{{ $bank->name }}"
                                        style="max-height: 120px; border-radius: 6px;">
                                @else
                                    <span class="text-muted">Không có logo</span>
                                @endif
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">QR Code:</label>
                            <div class="detail-value">
                                @if ($bank->qr_code)
                                    <img src="{{ Storage::url($bank->qr_code) }}" alt="QR Code"
                                        style="max-height: 120px; border-radius: 6px;">
                                @else
                                    <span class="text-muted">Không có QR Code</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-actions">
                    <a href="{{ route('admin.banks.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <a href="{{ route('admin.banks.edit', $bank) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    @include('components.delete-form', [
                        'id' => $bank->id,
                        'route' => route('admin.banks.destroy', $bank),
                        'message' => "Bạn có chắc chắn muốn xóa ngân hàng '{$bank->name}'?",
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bank-code-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .account-number {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
@endpush
