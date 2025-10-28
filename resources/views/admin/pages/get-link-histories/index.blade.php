@extends('admin.layouts.sidebar')

@section('title', 'Lịch sử Get Link')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-link icon-title"></i>
                    <h5>Lịch sử Get Link</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.get-link-histories.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="search_input">Tìm kiếm</label>
                            <input type="text" id="search_input" name="search" class="filter-input" 
                                   placeholder="Tìm theo URL, title, tên user, email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.get-link-histories.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('search'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        <span class="filter-tag">
                            <span>Tìm kiếm: {{ request('search') }}</span>
                            <a href="{{ route('admin.get-link-histories.index') }}" class="remove-filter">×</a>
                        </span>
                    </div>
                @endif

                @if ($histories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        @if (request('search'))
                            <h4>Không tìm thấy lịch sử nào</h4>
                            <p>Không có lịch sử get link nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.get-link-histories.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có lịch sử get link</h4>
                            <p>Chưa có lịch sử get link nào được thực hiện trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">User</th>
                                    <th class="column-large">URL</th>
                                    <th class="column-medium">Title</th>
                                    <th class="column-small">Xu đã trừ</th>
                                    <th class="column-medium">Thời gian</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($histories as $index => $history)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($histories->currentPage() - 1) * $histories->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <strong>{{ $history->user->full_name }}</strong>
                                                <small class="text-muted d-block">{{ $history->user->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="url-info">
                                                <a href="{{ $history->url }}" target="_blank" class="text-decoration-none" title="{{ $history->url }}">
                                                    {{ \Str::limit($history->url, 50) }}
                                                    <i class="fas fa-external-link-alt ms-1"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="title-info">
                                                @if($history->favicon)
                                                    <img src="{{ $history->favicon }}" alt="favicon" class="favicon-img">
                                                @endif
                                                <span>{{ $history->title ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="coins-badge negative">
                                                -{{ number_format($history->coins_spent) }} xu
                                            </span>
                                        </td>
                                        <td>
                                            <div class="time-info">
                                                <strong>{{ $history->created_at->format('d/m/Y H:i') }}</strong>
                                                <small class="text-muted d-block">{{ $history->created_at->diffForHumans() }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.get-link-histories.show', $history->id) }}" 
                                                   class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $history->id,
                                                    'route' => route('admin.get-link-histories.destroy', $history->id),
                                                    'message' => 'Bạn có chắc muốn xóa lịch sử get link này?',
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
                            Hiển thị {{ $histories->firstItem() ?? 0 }} đến {{ $histories->lastItem() ?? 0 }} của
                            {{ $histories->total() }} bản ghi
                        </div>
                        <div class="pagination-controls">
                            {{ $histories->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .user-info strong {
        color: #333;
    }

    .url-info a {
        color: #667eea;
    }

    .url-info a:hover {
        color: #764ba2;
        text-decoration: underline !important;
    }

    .title-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .favicon-img {
        width: 16px;
        height: 16px;
        object-fit: contain;
    }

    .time-info strong {
        color: #333;
    }
</style>
@endpush

