@extends('admin.layouts.sidebar')

@section('title', 'Quản lý mua file')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-shopping-cart icon-title"></i>
                    <h5>Danh sách mua file</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.purchase-sets.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="user_filter">Người dùng</label>
                            <select id="user_filter" name="user_id" class="filter-input">
                                <option value="">Tất cả người dùng</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="set_filter">File</label>
                            <select id="set_filter" name="set_id" class="filter-input">
                                <option value="">Tất cả file</option>
                                @foreach($sets as $set)
                                    <option value="{{ $set->id }}" {{ request('set_id') == $set->id ? 'selected' : '' }}>
                                        {{ $set->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="downloaded_filter">Trạng thái tải</label>
                            <select id="downloaded_filter" name="downloaded" class="filter-input">
                                <option value="">Tất cả</option>
                                <option value="yes" {{ request('downloaded') == 'yes' ? 'selected' : '' }}>Đã tải</option>
                                <option value="no" {{ request('downloaded') == 'no' ? 'selected' : '' }}>Chưa tải</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="date_from">Từ ngày</label>
                            <input type="date" id="date_from" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <label for="date_to">Đến ngày</label>
                            <input type="date" id="date_to" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.purchase-sets.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('user_id') || request('set_id') || request('downloaded') || request('date_from') || request('date_to'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('user_id'))
                            <span class="filter-tag">
                                <span>Người dùng: {{ $users->where('id', request('user_id'))->first()->full_name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('user_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('set_id'))
                            <span class="filter-tag">
                                <span>File: {{ $sets->where('id', request('set_id'))->first()->name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('set_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('downloaded'))
                            <span class="filter-tag">
                                <span>Trạng thái: {{ request('downloaded') == 'yes' ? 'Đã tải' : 'Chưa tải' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('downloaded')) }}"
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
                    </div>
                @endif

                @if ($purchases->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        @if (request('user_id') || request('set_id') || request('downloaded') || request('date_from') || request('date_to'))
                            <h4>Không tìm thấy giao dịch mua file nào</h4>
                            <p>Không có giao dịch nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.purchase-sets.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có giao dịch mua file nào</h4>
                            <p>Chưa có giao dịch mua file nào được tạo trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Người dùng</th>
                                    <th class="column-large">File</th>
                                    <th class="column-small">Số xu</th>
                                    <th class="column-small">Trạng thái tải</th>
                                    <th class="column-medium">Ngày mua</th>
                                    <th class="column-medium">Ngày tải</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $index => $purchase)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($purchases->currentPage() - 1) * $purchases->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <strong>{{ $purchase->user->full_name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $purchase->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="set-info">
                                                <strong>{{ $purchase->set->name ?? 'File đã bị xóa' }}</strong>
                                                @if($purchase->set)
                                                    <br>
                                                    <small class="text-muted">{{ $purchase->set->type === 'free' ? 'Miễn phí' : 'Premium' }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="coins-badge">{{ number_format($purchase->coins) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="download-status {{ $purchase->downloaded_at ? 'downloaded' : 'not-downloaded' }}">
                                                @if($purchase->downloaded_at)
                                                    <i class="fas fa-check-circle"></i> Đã tải
                                                @else
                                                    <i class="fas fa-clock"></i> Chưa tải
                                                @endif
                                            </span>
                                        </td>
                                        <td class="purchase-date">
                                            {{ $purchase->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="download-date">
                                            @if($purchase->downloaded_at)
                                                {{ $purchase->downloaded_at->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.purchase-sets.show', $purchase) }}"
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
                            Hiển thị {{ $purchases->firstItem() ?? 0 }} đến {{ $purchases->lastItem() ?? 0 }} của
                            {{ $purchases->total() }} giao dịch
                        </div>
                        <div class="pagination-controls">
                            {{ $purchases->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .user-info {
            font-size: 14px;
        }

        .user-info strong {
            color: #333;
        }

        .set-info {
            font-size: 14px;
        }

        .set-info strong {
            color: #333;
        }

        .coins-badge {
            background: #fff3e0;
            color: #f57c00;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .download-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .download-status.downloaded {
            background: #d4edda;
            color: #155724;
        }

        .download-status.not-downloaded {
            background: #fff3cd;
            color: #856404;
        }

        .purchase-date, .download-date {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
@endpush
