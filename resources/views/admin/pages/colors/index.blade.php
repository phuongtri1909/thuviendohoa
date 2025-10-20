@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý màu sắc')

@section('main-content')
    <div class="color-container">
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Màu sắc</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-palette icon-title"></i>
                    <h5>Danh sách màu sắc</h5>
                </div>

                <a href="{{ route('admin.colors.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm màu
                </a>

            </div>

            <div class="filter-section">
                <form action="{{ route('admin.colors.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="name_filter">Tên màu</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                placeholder="Tìm theo tên màu" value="{{ request('name') }}">
                        </div>
                        <div class="col-6">
                            <label for="value_filter">Mã màu</label>
                            <input type="text" id="value_filter" name="value" class="filter-input"
                                placeholder="Tìm theo mã màu (#RRGGBB)" value="{{ request('value') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.colors.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('name') || request('value'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('name'))
                            <span class="filter-tag">
                                <span>Tên: {{ request('name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('value'))
                            <span class="filter-tag">
                                <span>Mã màu: {{ request('value') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('value')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($colors->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        @if (request('name') || request('value'))
                            <h4>Không tìm thấy màu nào</h4>
                            <p>Không có màu nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.colors.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có màu nào</h4>
                            <p>Bắt đầu bằng cách thêm màu đầu tiên.</p>
                            <a href="{{ route('admin.colors.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm màu mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên màu</th>
                                    <th class="column-medium">Mã màu</th>
                                    <th class="column-medium">Xem trước</th>
                                    <th class="column-small text-center">Số bộ</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($colors as $index => $color)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($colors->currentPage() - 1) * $colors->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $color->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="color-code">{{ $color->value }}</span>
                                        </td>
                                        <td>
                                            <div class="color-preview" style="background-color: {{ $color->value }}"></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="stories-count">{{ $color->sets_count }}</span>
                                        </td>
                                        <td class="category-date">
                                            {{ $color->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.colors.show', $color) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.colors.edit', $color) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @include('components.delete-form', [
                                                    'id' => $color->id,
                                                    'route' => route('admin.colors.destroy', $color),
                                                    'message' => "Bạn có chắc chắn muốn xóa màu '{$color->name}'?",
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
                            Hiển thị {{ $colors->firstItem() ?? 0 }} đến {{ $colors->lastItem() ?? 0 }} của
                            {{ $colors->total() }} màu
                        </div>
                        <div class="pagination-controls">
                            {{ $colors->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .color-code {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            color: #495057;
        }

        .color-date {
            font-size: 14px;
            color: #6c757d;
        }

        .filter-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 16px;
            font-size: 14px;
            margin-right: 8px;
        }

        .stories-count {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-date {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
@endpush
