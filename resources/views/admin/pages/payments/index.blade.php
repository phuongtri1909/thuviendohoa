@extends('admin.layouts.sidebar')

@section('title', 'Quản lý giao dịch')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Giao dịch</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-money-bill-wave icon-title"></i>
                    <h5>Danh sách giao dịch</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.payments.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
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
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="bank_filter">Ngân hàng</label>
                            <select id="bank_filter" name="bank_id" class="filter-input">
                                <option value="">Tất cả ngân hàng</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }}
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
                        <a href="{{ route('admin.payments.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('status') || request('user_id') || request('bank_id') || request('date_from') || request('date_to'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('status'))
                            <span class="filter-tag">
                                <span>Trạng thái: {{ $statuses[request('status')] }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('user_id'))
                            <span class="filter-tag">
                                <span>Người dùng: {{ $users->where('id', request('user_id'))->first()->name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('user_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('bank_id'))
                            <span class="filter-tag">
                                <span>Ngân hàng: {{ $banks->where('id', request('bank_id'))->first()->name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('bank_id')) }}"
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

                @if ($payments->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        @if (request('status') || request('user_id') || request('bank_id') || request('date_from') || request('date_to'))
                            <h4>Không tìm thấy giao dịch nào</h4>
                            <p>Không có giao dịch nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.payments.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có giao dịch nào</h4>
                            <p>Chưa có giao dịch nào được tạo trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Người dùng</th>
                                    <th class="column-medium">Ngân hàng</th>
                                    <th class="column-small">Gói</th>
                                    <th class="column-small">Số xu</th>
                                    <th class="column-small">Số tiền</th>
                                    <th class="column-small">Trạng thái</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $index => $payment)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <strong>{{ $payment->user->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $payment->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="bank-name">{{ $payment->bank->name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="package-badge">{{ ucfirst($payment->package_plan) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="coins-badge">{{ number_format($payment->coins) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="amount-badge">{{ number_format($payment->amount) }} VNĐ</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ $payment->status }}">
                                                @switch($payment->status)
                                                    @case('pending')
                                                        <i class="fas fa-clock"></i> Chờ xử lý
                                                        @break
                                                    @case('success')
                                                        <i class="fas fa-check"></i> Thành công
                                                        @break
                                                    @case('failed')
                                                        <i class="fas fa-times"></i> Thất bại
                                                        @break
                                                    @case('cancelled')
                                                        <i class="fas fa-ban"></i> Đã hủy
                                                        @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="payment-date">
                                            {{ $payment->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.payments.show', $payment) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>


                                                @if($payment->status === 'pending')
                                                    @include('components.delete-form', [
                                                        'id' => $payment->id,
                                                        'route' => route('admin.payments.destroy', $payment),
                                                        'message' => "Bạn có chắc chắn muốn xóa giao dịch này?",
                                                    ])
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
                            Hiển thị {{ $payments->firstItem() ?? 0 }} đến {{ $payments->lastItem() ?? 0 }} của
                            {{ $payments->total() }} giao dịch
                        </div>
                        <div class="pagination-controls">
                            {{ $payments->appends(request()->query())->links('components.paginate') }}
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

        .bank-name {
            font-weight: 600;
            color: #1976d2;
        }

        .package-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .coins-badge {
            background: #fff3e0;
            color: #f57c00;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .amount-badge {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .status-cancelled {
            background: #d1ecf1;
            color: #0c5460;
        }

        .payment-date {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
@endpush

@push('scripts')
@endpush
