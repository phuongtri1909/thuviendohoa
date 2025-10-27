@extends('admin.layouts.sidebar')

@section('title', 'Quản lý góp ý')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-comments icon-title"></i>
                    <h5>Quản lý góp ý</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.feedback.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Đã đọc</option>
                                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Đã phản hồi</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="date_from_filter">Từ ngày</label>
                            <input type="date" id="date_from_filter" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-3">
                            <label for="date_to_filter">Đến ngày</label>
                            <input type="date" id="date_to_filter" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-3">
                            <label for="search_input">Tìm kiếm</label>
                            <input type="text" id="search_input" name="search" class="filter-input" 
                                   placeholder="Tên, email, nội dung..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.feedback.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('status') || request('date_from') || request('date_to') || request('search'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('status'))
                            <span class="filter-tag">
                                <span>Trạng thái: {{ ucfirst(request('status')) }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('date_from'))
                            <span class="filter-tag">
                                <span>Từ: {{ request('date_from') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('date_from')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('date_to'))
                            <span class="filter-tag">
                                <span>Đến: {{ request('date_to') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('date_to')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('search'))
                            <span class="filter-tag">
                                <span>Tìm kiếm: {{ request('search') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('search')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($feedbacks->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        @if (request('status') || request('date_from') || request('date_to') || request('search'))
                            <h4>Không tìm thấy góp ý nào</h4>
                            <p>Không có góp ý nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.feedback.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có góp ý nào</h4>
                            <p>Chưa có góp ý nào được gửi từ người dùng.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Người gửi</th>
                                    <th class="column-large">Nội dung</th>
                                    <th class="column-small">Trạng thái</th>
                                    <th class="column-medium">Ngày gửi</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feedbacks as $index => $feedback)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($feedbacks->currentPage() - 1) * $feedbacks->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $feedback->name ?: 'Khách' }}</strong>
                                            @if($feedback->email)
                                                <br><small class="text-muted">{{ $feedback->email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="message-text">{{ Str::limit($feedback->message, 100) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ $feedback->status_color }}">
                                                {{ $feedback->status_label }}
                                            </span>
                                        </td>
                                        <td class="feedback-date">
                                            {{ $feedback->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.feedback.show', $feedback) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($feedback->status === 'pending')
                                                    <button type="button" class="action-icon edit-icon" 
                                                            onclick="markAsRead({{ $feedback->id }})" title="Đánh dấu đã đọc">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif

                                                @include('components.delete-form', [
                                                    'id' => $feedback->id,
                                                    'route' => route('admin.feedback.destroy', $feedback),
                                                    'message' => "Bạn có chắc chắn muốn xóa góp ý này?",
                                                ])

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $feedbacks->firstItem() ?? 0 }} đến {{ $feedbacks->lastItem() ?? 0 }} của
                            {{ $feedbacks->total() }} góp ý
                        </div>
                        <div class="pagination-controls">
                            {{ $feedbacks->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@include('components.sweetalert')

@push('scripts')
<script>
function markAsRead(feedbackId) {
    Swal.fire({
        title: 'Xác nhận',
        text: 'Bạn có chắc muốn đánh dấu góp ý này là đã đọc?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/feedback') }}/${feedbackId}/mark-read`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush

@push('styles')
<style>
    .message-text {
        font-size: 14px;
        color: #495057;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
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

    .feedback-date {
        font-size: 14px;
        color: #6c757d;
    }
</style>
@endpush
