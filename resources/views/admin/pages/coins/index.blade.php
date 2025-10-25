@extends('admin.layouts.sidebar')

@section('title', 'Quản lý xu')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Quản lý xu</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-coins icon-title"></i>
                    <h5>Lịch sử giao dịch xu</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.coins.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Cộng xu
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.coins.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="type_filter">Loại giao dịch</label>
                            <select id="type_filter" name="type" class="filter-input">
                                <option value="">Tất cả loại</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="admin_filter">Admin thực hiện</label>
                            <select id="admin_filter" name="admin_id" class="filter-input">
                                <option value="">Tất cả admin</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->full_name }} ({{ $admin->email }})
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
                                        {{ $user->full_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
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
                        <a href="{{ route('admin.coins.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('type') || request('admin_id') || request('user_id') || request('date_from') || request('date_to'))
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
                    </div>
                @endif

                @if ($transactions->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        @if (request('type') || request('admin_id') || request('user_id') || request('date_from') || request('date_to'))
                            <h4>Không tìm thấy giao dịch nào</h4>
                            <p>Không có giao dịch nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.coins.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có giao dịch xu nào</h4>
                            <p>Chưa có giao dịch xu nào được thực hiện trong hệ thống.</p>
                            <a href="{{ route('admin.coins.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Cộng xu ngay
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Người dùng</th>
                                    <th class="column-small">Số xu</th>
                                    <th class="column-small">Loại</th>
                                    <th class="column-medium">Lý do</th>
                                    <th class="column-medium">Admin</th>
                                    <th class="column-medium">Thời gian</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $index => $transaction)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <strong>{{ $transaction->user->full_name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $transaction->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="coins-badge {{ $transaction->amount > 0 ? 'positive' : 'negative' }}">
                                                {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="type-badge type-{{ $transaction->type }}">
                                                {{ $types[$transaction->type] ?? $transaction->type }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="reason-text">
                                                <strong>{{ $transaction->reason }}</strong>
                                                @if($transaction->note)
                                                    <br>
                                                    <small class="text-muted">{{ $transaction->note }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="admin-info">
                                                <strong>{{ $transaction->admin->full_name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $transaction->admin->email ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td class="transaction-date">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.coins.show', $transaction) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($transaction->type === 'manual' && $transaction->amount > 0)
                                                    <a href="{{ route('admin.coins.create') }}?subtract_user={{ $transaction->user_id }}&amount={{ $transaction->amount }}" 
                                                       class="action-icon subtract-icon text-decoration-none" title="Trừ xu tương ứng">
                                                        <i class="fas fa-minus"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $transactions->firstItem() ?? 0 }} đến {{ $transactions->lastItem() ?? 0 }} của
                            {{ $transactions->total() }} giao dịch
                        </div>
                        <div class="pagination-controls">
                            {{ $transactions->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .user-info, .admin-info {
            font-size: 14px;
        }
        .user-info strong, .admin-info strong {
            color: #333;
        }
        .coins-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            margin: 0 2px;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .subtract-icon {
            background: #fff3cd;
            color: #856404;
        }

        .subtract-icon:hover {
            background: #856404;
            color: white;
        }
        .coins-badge.positive {
            background: #d4edda;
            color: #155724;
        }
        .coins-badge.negative {
            background: #f8d7da;
            color: #721c24;
        }
        .type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .type-badge.type-manual {
            background: #e3f2fd;
            color: #1976d2;
        }
        .type-badge.type-package_bonus {
            background: #fff3e0;
            color: #f57c00;
        }
        .type-badge.type-refund {
            background: #d4edda;
            color: #155724;
        }
        .type-badge.type-penalty {
            background: #f8d7da;
            color: #721c24;
        }
        .reason-text {
            font-size: 14px;
        }
        .reason-text strong {
            color: #333;
        }
        .transaction-date {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
@endpush
