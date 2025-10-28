@extends('admin.layouts.sidebar')

@section('title', 'Lịch sử xu')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-history icon-title"></i>
                    <h5>Lịch sử cộng/trừ xu</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.coin-histories.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="type_filter">Loại</label>
                            <select id="type_filter" name="type" class="filter-input">
                                <option value="">Tất cả loại</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="admin_filter">Admin</label>
                            <select id="admin_filter" name="admin_id" class="filter-input">
                                <option value="">Tất cả admin</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="user_filter">Người dùng</label>
                            <select id="user_filter" name="user_id" class="filter-input">
                                <option value="">Tất cả người dùng</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <label for="date_from">Từ ngày</label>
                            <input type="date" id="date_from" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-3">
                            <label for="date_to">Đến ngày</label>
                            <input type="date" id="date_to" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-3">
                            <label for="search_input">Tìm kiếm</label>
                            <input type="text" id="search_input" name="search" class="filter-input" 
                                   placeholder="Tìm kiếm theo lý do..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.coin-histories.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('type') || request('admin_id') || request('user_id') || request('date_from') || request('date_to') || request('search'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('type'))
                            <span class="filter-tag">
                                <span>Loại: {{ $types[request('type')] }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('type')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('admin_id'))
                            <span class="filter-tag">
                                <span>Admin: {{ $admins->where('id', request('admin_id'))->first()->full_name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('admin_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('user_id'))
                            <span class="filter-tag">
                                <span>User: {{ $users->where('id', request('user_id'))->first()->full_name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('user_id')) }}"
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

                @if ($histories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        @if (request('type') || request('admin_id') || request('user_id') || request('date_from') || request('date_to') || request('search'))
                            <h4>Không tìm thấy lịch sử nào</h4>
                            <p>Không có lịch sử xu nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.coin-histories.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có lịch sử xu</h4>
                            <p>Chưa có lịch sử cộng/trừ xu nào được ghi nhận trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Người dùng</th>
                                    <th class="column-medium">Loại</th>
                                    <th class="column-small">Số xu</th>
                                    <th class="column-medium">Lý do</th>
                                    <th class="column-medium">Admin</th>
                                    <th class="column-small">Thời gian</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($histories as $index => $history)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($histories->currentPage() - 1) * $histories->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <strong>{{ $history->user->full_name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $history->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="type-badge type-{{ $history->type }}">
                                                {{ $history->type_label }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="amount-badge {{ $history->amount > 0 ? 'positive' : 'negative' }}">
                                                {{ $history->formatted_amount }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="reason-text">
                                                <strong>{{ $history->reason }}</strong>
                                                @if($history->description)
                                                    <br>
                                                    <small class="text-muted">{{ $history->description }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($history->admin)
                                                <div class="admin-info">
                                                    <strong>{{ $history->admin->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $history->admin->email }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">Hệ thống</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <small>{{ $history->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.coin-histories.show', $history->id) }}"
                                                   class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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

.type-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.type-badge.type-payment {
    background: #d4edda;
    color: #155724;
}

.type-badge.type-purchase {
    background: #f8d7da;
    color: #721c24;
}

.type-badge.type-manual {
    background: #fff3e0;
    color: #f57c00;
}

.type-badge.type-monthly_bonus {
    background: #e3f2fd;
    color: #1976d2;
}

.type-badge.type-getlink {
    background: #e3f2fd;
    color: #1976d2;
}


.reason-text {
    max-width: 200px;
    word-wrap: break-word;
}

.admin-info {
    max-width: 150px;
    word-wrap: break-word;
}

.user-info strong {
    color: #333;
}

.admin-info strong {
    color: #333;
}
</style>
@endpush
