@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý album')

@section('main-content')
    <div class="category-container">
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Album</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-images icon-title"></i>
                    <h5>Danh sách album</h5>
                </div>
                <a href="{{ route('admin.albums.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm album
                </a>
            </div>

            <div class="filter-section">
                <form action="{{ route('admin.albums.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-4">
                            <label for="name_filter">Tên album</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                placeholder="Tìm theo tên album" value="{{ request('name') }}">
                        </div>
                        <div class="col-4">
                            <label>&nbsp;</label>
                            <div class="d-flex align-items-center" style="gap:12px;">
                                <label class="d-flex align-items-center" style="gap:6px;">
                                    <input type="checkbox" name="featured" value="1" {{ request('featured') ? 'checked' : '' }}> Featured
                                </label>
                                <label class="d-flex align-items-center" style="gap:6px;">
                                    <input type="checkbox" name="trending" value="1" {{ request('trending') ? 'checked' : '' }}> Trending
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.albums.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request()->hasAny(['name','featured','trending']))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('name'))
                            <span class="filter-tag">
                                <span>Tên: {{ request('name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('featured'))
                            <span class="filter-tag">
                                <span>Featured</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('featured')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('trending'))
                            <span class="filter-tag">
                                <span>Trending</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('trending')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($albums->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        @if (request()->hasAny(['name','featured','trending']))
                            <h4>Không tìm thấy album nào</h4>
                            <p>Không có album nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.albums.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có album nào</h4>
                            <p>Bắt đầu bằng cách thêm album đầu tiên.</p>
                            <a href="{{ route('admin.albums.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm album mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên album</th>
                                    <th class="column-medium">Ảnh</th>
                                    <th class="column-small text-center">Featured</th>
                                    <th class="column-small text-center">Trending</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($albums as $index => $album)
                                    <tr>
                                        <td class="text-center">{{ ($albums->currentPage() - 1) * $albums->perPage() + $index + 1 }}</td>
                                        <td class="item-title"><strong>{{ $album->name }}</strong></td>
                                        <td>
                                            @if ($album->image)
                                                <img src="{{ Storage::url($album->image) }}" alt="image" style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có ảnh</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($album->featuredType)
                                                <span class="stories-count">Yes</span>
                                            @else
                                                <span class="text-muted">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($album->trendingType)
                                                <span class="stories-count">Yes</span>
                                            @else
                                                <span class="text-muted">No</span>
                                            @endif
                                        </td>
                                        <td class="category-date">{{ $album->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.albums.show', $album) }}" class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.albums.edit', $album) }}" class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $album->id,
                                                    'route' => route('admin.albums.destroy', $album),
                                                    'message' => "Bạn có chắc chắn muốn xóa album '{$album->name}'?",
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
                            Hiển thị {{ $albums->firstItem() ?? 0 }} đến {{ $albums->lastItem() ?? 0 }} của {{ $albums->total() }} album
                        </div>
                        <div class="pagination-controls">
                            {{ $albums->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


