@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết góp ý')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-comment-dots icon-title"></i>
                    <h5>Chi tiết góp ý</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.feedback.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="category-details">
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">ID:</label>
                            <span class="detail-value">{{ $feedback->id }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Người gửi:</label>
                            <span class="detail-value">{{ $feedback->name ?: 'Khách' }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Email:</label>
                            <span class="detail-value">{{ $feedback->email ?: 'Không có' }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">IP Address:</label>
                            <span class="detail-value">{{ $feedback->ip_address }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Trạng thái:</label>
                            <span class="detail-value">
                                <span class="status-badge status-{{ $feedback->status_color }}">
                                    {{ $feedback->status_label }}
                                </span>
                            </span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Ngày gửi:</label>
                            <span class="detail-value">{{ $feedback->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Cập nhật lần cuối:</label>
                            <span class="detail-value">{{ $feedback->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>

                    <div class="message-section">
                        <h6 class="section-title">
                            <i class="fas fa-comment"></i>
                            Nội dung góp ý
                        </h6>
                        <div class="message-content">
                            {{ $feedback->message }}
                        </div>
                    </div>

                    @if($feedback->admin_reply)
                        <div class="reply-section">
                            <h6 class="section-title">
                                <i class="fas fa-reply"></i>
                                Phản hồi từ admin
                            </h6>
                            <div class="reply-content">
                                <div class="reply-header">
                                    <strong>{{ $feedback->admin->full_name }}</strong>
                                    <span class="reply-date">{{ $feedback->replied_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <div class="reply-message">
                                    {{ $feedback->admin_reply }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="reply-section">
                            <h6 class="section-title">
                                <i class="fas fa-reply"></i>
                                Gửi phản hồi
                            </h6>
                            <form method="POST" action="{{ route('admin.feedback.reply', $feedback->id) }}" class="reply-form">
                                @csrf
                                <div class="form-group">
                                    <textarea name="admin_reply" id="admin_reply" class="form-control @error('admin_reply') is-invalid @enderror" rows="6" 
                                              placeholder="Nhập nội dung phản hồi (tối thiểu 10 ký tự)..." required>{{ old('admin_reply') }}</textarea>
                                    @error('admin_reply')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <button type="submit" class="action-button">
                                    <i class="fas fa-paper-plane"></i> Gửi phản hồi
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="action-section">
                        <h6 class="section-title">
                            <i class="fas fa-cog"></i>
                            Thao tác
                        </h6>
                        <div class="action-buttons">
                            @if($feedback->status === 'pending')
                                <form method="POST" action="{{ route('admin.feedback.mark-read', $feedback->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-button mark-read-btn">
                                        <i class="fas fa-check"></i> Đánh dấu đã đọc
                                    </button>
                                </form>
                            @endif
                            
                            @include('components.delete-form', [
                                'id' => $feedback->id,
                                'route' => route('admin.feedback.destroy', $feedback),
                                'message' => "Bạn có chắc chắn muốn xóa góp ý này?",
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .category-details {
        padding: 20px;
    }


    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .detail-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .detail-item {
        display: flex;
        margin-bottom: 15px;
        align-items: flex-start;
    }

    .detail-item:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
        min-width: 150px;
        margin-right: 15px;
    }

    .detail-value {
        color: #333;
        flex: 1;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    .status-warning {
        background: #fff3cd;
        color: #856404;
    }

    .status-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-success {
        background: #d4edda;
        color: #155724;
    }

    .message-section,
    .reply-section,
    .action-section {
        margin-top: 30px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
        color: #495057;
    }

    .section-title i {
        color: #007bff;
    }

    .message-content {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        line-height: 1.6;
        color: #333;
    }

    .reply-content {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
    }

    .reply-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .reply-header strong {
        color: #333;
        font-size: 16px;
    }

    .reply-date {
        color: #6c757d;
        font-size: 14px;
    }

    .reply-message {
        line-height: 1.6;
        color: #333;
    }

    .reply-form {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
    }

    .reply-form .form-group {
        margin-bottom: 15px;
    }

    .reply-form .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 14px;
        line-height: 1.6;
        transition: border-color 0.3s ease;
    }

    .reply-form .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-buttons .action-button,
    .action-buttons .delete-icon {
        margin: 0;
    }

    .mark-read-btn {
        background: #17a2b8;
    }

    .mark-read-btn:hover {
        background: #138496;
    }

    @media (max-width: 768px) {
        .detail-item {
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            min-width: auto;
            margin-right: 0;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .action-button,
        .action-buttons .delete-icon {
            width: 100%;
        }
    }
</style>
@endpush
