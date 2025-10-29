@extends('admin.layouts.sidebar')

@section('title', 'Quản lý trang')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-file-alt icon-title"></i>
                    <h5>Danh sách trang</h5>
                </div>
                <a href="{{ route('admin.pages.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm trang
                </a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.pages.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-12">
                            <label for="title_filter">Tiêu đề</label>
                            <input type="text" id="title_filter" name="title" class="filter-input"
                                placeholder="Tìm theo tiêu đề" value="{{ request('title') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.pages.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('title'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('title'))
                            <span class="filter-tag">
                                <span>Tiêu đề: {{ request('title') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('title')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($pages->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        @if (request('title'))
                            <h4>Không tìm thấy trang nào</h4>
                            <p>Không có trang nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.pages.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có trang nào</h4>
                            <p>Bắt đầu bằng cách thêm trang đầu tiên.</p>
                            <a href="{{ route('admin.pages.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm trang mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tiêu đề</th>
                                    <th class="column-large">Slug</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pages as $index => $page)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($pages->currentPage() - 1) * $pages->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $page->title }}</strong>
                                        </td>
                                        <td>
                                            <span class="slug-text">{{ $page->slug }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if ($page->status)
                                                <span class="status-badge-active">Hoạt động</span>
                                            @else
                                                <span class="status-badge-inactive">Ẩn</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="order-badge">{{ $page->order }}</span>
                                        </td>
                                        <td class="category-date">
                                            {{ $page->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.pages.show', $page) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.pages.edit', $page) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @include('components.delete-form', [
                                                    'id' => $page->id,
                                                    'route' => route('admin.pages.destroy', $page),
                                                    'message' => "Bạn có chắc chắn muốn xóa trang '{$page->title}'?",
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($pages->hasPages())
                        <div class="pagination-container">
                            <div class="pagination-info">
                                Hiển thị {{ $pages->firstItem() }} - {{ $pages->lastItem() }} trong tổng số
                                {{ $pages->total() }} trang
                            </div>
                            <div class="pagination-wrapper">
                                {{ $pages->links('components.paginate') }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
