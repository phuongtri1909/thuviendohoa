@extends('admin.layouts.sidebar')

@section('title', 'Quản lý người dùng')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Người dùng</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-users icon-title"></i>
                    <h5>Danh sách người dùng</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.users.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="name_filter">Họ tên</label>
                            <input type="text" id="name_filter" name="name" class="filter-input" value="{{ request('name') }}" placeholder="Tìm theo họ tên">
                        </div>
                        <div class="col-3">
                            <label for="email_filter">Email</label>
                            <input type="text" id="email_filter" name="email" class="filter-input" value="{{ request('email') }}" placeholder="Tìm theo email">
                        </div>
                        <div class="col-3">
                            <label for="role_filter">Vai trò</label>
                            <select id="role_filter" name="role" class="filter-input">
                                <option value="">Tất cả vai trò</option>
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}" {{ request('role') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="active_filter">Trạng thái</label>
                            <select id="active_filter" name="active" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                @foreach($activeStatuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('active') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('name') || request('email') || request('role') || request('active'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('name'))
                            <span class="filter-tag">
                                <span>Họ tên: {{ request('name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('email'))
                            <span class="filter-tag">
                                <span>Email: {{ request('email') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('email')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('role'))
                            <span class="filter-tag">
                                <span>Vai trò: {{ $roles[request('role')] }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('role')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('active'))
                            <span class="filter-tag">
                                <span>Trạng thái: {{ $activeStatuses[request('active')] }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('active')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($users->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        @if (request('name') || request('email') || request('role') || request('active'))
                            <h4>Không tìm thấy người dùng nào</h4>
                            <p>Không có người dùng nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.users.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có người dùng nào</h4>
                            <p>Chưa có người dùng nào được tạo trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Thông tin</th>
                                    <th class="column-small">Vai trò</th>
                                    <th class="column-small">Trạng thái</th>
                                    <th class="column-small">Xu</th>
                                    <th class="column-medium">Gói</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <strong>{{ $user->full_name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $user->email ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="role-badge role-{{ $user->role }}">
                                                {{ $user->role === 'admin' ? 'Admin' : 'User' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ $user->active ? 'active' : 'inactive' }}">
                                                {{ $user->active ? 'Hoạt động' : 'Không hoạt động' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="coins-badge">{{ number_format($user->coins ?? 0) }}</span>
                                        </td>
                                        <td>
                                            @if($user->package_id)
                                                <div class="package-info">
                                                    <strong>{{ $user->package->name ?? 'N/A' }}</strong>
                                                    @if($user->package_expired_at)
                                                        <br>
                                                        <small class="{{ \Carbon\Carbon::parse($user->package_expired_at)->isFuture() ? 'text-success' : 'text-danger' }}">
                                                            Hết hạn: {{ \Carbon\Carbon::parse($user->package_expired_at)->format('d/m/Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Chưa có gói</span>
                                            @endif
                                        </td>
                                        <td class="user-date">
                                            {{ $user->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.users.show', $user) }}"
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
                            Hiển thị {{ $users->firstItem() ?? 0 }} đến {{ $users->lastItem() ?? 0 }} của
                            {{ $users->total() }} người dùng
                        </div>
                        <div class="pagination-controls">
                            {{ $users->appends(request()->query())->links('components.paginate') }}
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
        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-badge.role-admin {
            background: #f8d7da;
            color: #721c24;
        }
        .role-badge.role-user {
            background: #d1ecf1;
            color: #0c5460;
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
        .status-badge.status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .coins-badge {
            background: #fff3e0;
            color: #f57c00;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .package-info {
            font-size: 14px;
        }
        .package-info strong {
            color: #333;
        }
        .user-date {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
@endpush