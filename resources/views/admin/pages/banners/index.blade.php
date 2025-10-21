@extends('admin.layouts.sidebar')

@section('title', 'Quản lý banner')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Banner</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-images icon-title"></i>
                    <h5>Danh sách banner</h5>
                </div>
                <a href="{{ route('admin.banners.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm banner
                </a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.banners.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="page_filter">Trang</label>
                            <select id="page_filter" name="page_filter" class="filter-input">
                                <option value="">-- Tất cả trang --</option>
                                <option value="{{ \App\Models\Banner::PAGE_HOME }}"
                                    {{ request('page_filter') === \App\Models\Banner::PAGE_HOME ? 'selected' : '' }}>Home
                                </option>
                                <option value="{{ \App\Models\Banner::PAGE_SEARCH }}"
                                    {{ request('page_filter') === \App\Models\Banner::PAGE_SEARCH ? 'selected' : '' }}>
                                    Search</option>
                                <option value="{{ \App\Models\Banner::PAGE_ALBUMS }}"
                                    {{ request('page_filter') === \App\Models\Banner::PAGE_ALBUMS ? 'selected' : '' }}>
                                    Albums</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Kích hoạt</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Không kích hoạt
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.banners.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('page_filter') || request('status'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('page_filter'))
                            <span class="filter-tag">
                                <span>Trang:
                                    {{ request('page_filter') === \App\Models\Banner::PAGE_HOME ? 'Home' : (request('page_filter') === \App\Models\Banner::PAGE_SEARCH ? 'Search' : 'Albums') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('page_filter')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('status'))
                            <span class="filter-tag">
                                <span>Trạng thái: {{ request('status') === '1' ? 'Kích hoạt' : 'Không kích hoạt' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($banners->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        @if (request('page_filter') || request('status'))
                            <h4>Không tìm thấy banner nào</h4>
                            <p>Không có banner nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.banners.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có banner nào</h4>
                            <p>Bắt đầu bằng cách thêm banner đầu tiên.</p>
                            <a href="{{ route('admin.banners.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm banner mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Ảnh</th>
                                    <th class="column-medium">Trang</th>
                                    <th class="column-medium text-center">Thứ tự</th>
                                    <th class="column-medium">Trạng thái</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($banners as $index => $banner)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($banners->currentPage() - 1) * $banners->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            @if ($banner->image)
                                                <img src="{{ Storage::url($banner->image) }}" alt="Banner"
                                                    style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có ảnh</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge text-bg-{{ $banner->key_page === \App\Models\Banner::PAGE_HOME ? 'primary' : ($banner->key_page === \App\Models\Banner::PAGE_SEARCH ? 'info' : 'success') }}">
                                                {{ $banner->key_page === \App\Models\Banner::PAGE_HOME ? 'Home' : ($banner->key_page === \App\Models\Banner::PAGE_SEARCH ? 'Search' : 'Albums') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="order-badge">{{ $banner->order }}</span>
                                        </td>
                                        <td>
                                            @if (isset($banner->status))
                                                <span class="badge text-bg-{{ $banner->status ? 'success' : 'danger' }}">
                                                    {{ $banner->status ? 'Kích hoạt' : 'Không kích hoạt' }}
                                                </span>
                                            @else
                                                <span class="badge text-bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td class="banner-date">
                                            {{ $banner->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.banners.show', $banner) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.banners.edit', $banner) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @include('components.delete-form', [
                                                    'id' => $banner->id,
                                                    'route' => route('admin.banners.destroy', $banner),
                                                    'message' => 'Bạn có chắc chắn muốn xóa banner này?',
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
                            Hiển thị {{ $banners->firstItem() ?? 0 }} đến {{ $banners->lastItem() ?? 0 }} của
                            {{ $banners->total() }} banner
                        </div>
                        <div class="pagination-controls">
                            {{ $banners->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .order-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .banner-date {
            font-size: 13px;
            color: #6c757d;
        }
    </style>
@endpush
