@extends('admin.layouts.sidebar')

@section('title', 'Lịch sử cộng xu hàng tháng')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Lịch sử cộng xu hàng tháng</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-calendar-alt icon-title"></i>
                    <h5>Lịch sử cộng xu hàng tháng</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.monthly-bonuses.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="package_filter">Gói</label>
                            <select id="package_filter" name="package_id" class="filter-input">
                                <option value="">Tất cả gói</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="month_filter">Tháng</label>
                            <select id="month_filter" name="month" class="filter-input">
                                <option value="">Tất cả tháng</option>
                                @foreach($months as $month)
                                    <option value="{{ $month['value'] }}" {{ request('month') == $month['value'] ? 'selected' : '' }}>
                                        {{ $month['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="search_input">Tìm kiếm</label>
                            <input type="text" id="search_input" name="search" class="filter-input" 
                                   placeholder="Tìm kiếm theo tên gói..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.monthly-bonuses.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('package_id') || request('month') || request('search'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('package_id'))
                            <span class="filter-tag">
                                <span>Gói: {{ $packages->where('id', request('package_id'))->first()->name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('package_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('month'))
                            <span class="filter-tag">
                                <span>Tháng: {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('m/Y') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('month')) }}"
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

                @if ($monthlyBonuses->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        @if (request('package_id') || request('month') || request('search'))
                            <h4>Không tìm thấy lịch sử nào</h4>
                            <p>Không có lịch sử cộng xu nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.monthly-bonuses.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có lịch sử cộng xu hàng tháng</h4>
                            <p>Chưa có lịch sử cộng xu hàng tháng nào được thực hiện trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Gói</th>
                                    <th class="column-small">Tháng</th>
                                    <th class="column-small">Số user</th>
                                    <th class="column-small">Tổng xu</th>
                                    <th class="column-small">Xu/user</th>
                                    <th class="column-medium">Thời gian xử lý</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyBonuses as $index => $bonus)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($monthlyBonuses->currentPage() - 1) * $monthlyBonuses->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            <div class="package-info">
                                                <strong>{{ $bonus->package->name }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="month-badge">
                                                {{ $bonus->formatted_month }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="users-badge">
                                                {{ number_format($bonus->total_users) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="coins-badge positive">
                                                {{ $bonus->total_coins_formatted }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="bonus-per-user">
                                                {{ number_format($bonus->bonus_per_user) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="time-info">
                                                <strong>{{ $bonus->processed_at_formatted }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.monthly-bonuses.show', $bonus->id) }}" 
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
                            Hiển thị {{ $monthlyBonuses->firstItem() ?? 0 }} đến {{ $monthlyBonuses->lastItem() ?? 0 }} của
                            {{ $monthlyBonuses->total() }} bản ghi
                        </div>
                        <div class="pagination-controls">
                            {{ $monthlyBonuses->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .month-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .users-badge {
        background: #f3e5f5;
        color: #7b1fa2;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .bonus-per-user {
        color: #2e7d32;
        font-weight: 600;
    }

    .package-info strong {
        color: #333;
    }

    .time-info strong {
        color: #333;
    }
</style>
@endpush