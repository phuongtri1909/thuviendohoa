@extends('admin.layouts.sidebar')

@section('title', 'Quản lý bài viết')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-newspaper icon-title"></i>
                    <h5>Danh sách bài viết</h5>
                </div>
                <a href="{{ route('admin.blogs.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm bài viết
                </a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.blogs.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="title_filter">Tiêu đề</label>
                            <input type="text" id="title_filter" name="title" class="filter-input"
                                placeholder="Tìm theo tiêu đề" value="{{ request('title') }}">
                        </div>
                        <div class="col-6">
                            <label for="category_filter">Danh mục</label>
                            <select id="category_filter" name="category_id" class="filter-input">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('title') || request('category_id'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('title'))
                            <span class="filter-tag">
                                <span>Tiêu đề: {{ request('title') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('title')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('category_id'))
                            <span class="filter-tag">
                                <span>Danh mục: {{ $categories->firstWhere('id', request('category_id'))->name ?? '' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('category_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($blogs->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        @if (request('title') || request('category_id'))
                            <h4>Không tìm thấy bài viết nào</h4>
                            <p>Không có bài viết nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.blogs.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có bài viết nào</h4>
                            <p>Bắt đầu bằng cách thêm bài viết đầu tiên.</p>
                            <a href="{{ route('admin.blogs.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm bài viết mới
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
                                    <th class="column-medium">Danh mục</th>
                                    <th class="column-medium">Tác giả</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blogs as $index => $blog)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($blogs->currentPage() - 1) * $blogs->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $blog->title }}</strong>
                                        </td>
                                        <td>
                                            <span class="category-badge">{{ $blog->category->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="author-name">{{ $blog->user->name ?? $blog->create_by }}</span>
                                        </td>
                                        <td class="category-date">
                                            {{ $blog->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.blogs.show', $blog) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.blogs.edit', $blog) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @include('components.delete-form', [
                                                    'id' => $blog->id,
                                                    'route' => route('admin.blogs.destroy', $blog),
                                                    'message' => "Bạn có chắc chắn muốn xóa bài viết '{$blog->title}'?",
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
                            Hiển thị {{ $blogs->firstItem() ?? 0 }} đến {{ $blogs->lastItem() ?? 0 }} của
                            {{ $blogs->total() }} bài viết
                        </div>
                        <div class="pagination-controls">
                            {{ $blogs->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .category-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .author-name {
            font-size: 14px;
            color: #6c757d;
        }

        .category-date {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
@endpush

