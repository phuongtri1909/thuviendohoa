@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý tag')

@section('main-content')
    <div class="category-container">
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Tag</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-tags icon-title"></i>
                    <h5>Danh sách tag</h5>
                </div>
                <a href="{{ route('admin.tags.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm tag
                </a>
            </div>

            <div class="filter-section">
                <form action="{{ route('admin.tags.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="name_filter">Tên tag</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                placeholder="Tìm theo tên tag" value="{{ request('name') }}">
                        </div>
                        <div class="col-6">
                            <label for="slug_filter">Slug</label>
                            <input type="text" id="slug_filter" name="slug" class="filter-input"
                                placeholder="Tìm theo slug" value="{{ request('slug') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.tags.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('name') || request('slug'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('name'))
                            <span class="filter-tag">
                                <span>Tên: {{ request('name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('slug'))
                            <span class="filter-tag">
                                <span>Slug: {{ request('slug') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('slug')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($tags->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        @if (request('name') || request('slug'))
                            <h4>Không tìm thấy tag nào</h4>
                            <p>Không có tag nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.tags.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có tag nào</h4>
                            <p>Bắt đầu bằng cách thêm tag đầu tiên.</p>
                            <a href="{{ route('admin.tags.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm tag mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên tag</th>
                                    <th class="column-large">Slug</th>
                                    <th class="column-small text-center">Số bộ</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tags as $index => $tag)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($tags->currentPage() - 1) * $tags->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $tag->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="slug-text">{{ $tag->slug }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="stories-count">{{ $tag->sets_count }}</span>
                                        </td>
                                        <td class="category-date">
                                            {{ $tag->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.tags.show', $tag) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.tags.edit', $tag) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @include('components.delete-form', [
                                                    'id' => $tag->id,
                                                    'route' => route('admin.tags.destroy', $tag),
                                                    'message' => "Bạn có chắc chắn muốn xóa tag '{$tag->name}'?",
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
                            Hiển thị {{ $tags->firstItem() ?? 0 }} đến {{ $tags->lastItem() ?? 0 }} của
                            {{ $tags->total() }} tag
                        </div>
                        <div class="pagination-controls">
                            {{ $tags->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

