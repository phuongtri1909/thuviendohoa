@extends('admin.layouts.sidebar')

@section('title', 'Quản lý set')

@section('main-content')
    <div class="category-container">
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Set</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-layer-group icon-title"></i>
                    <h5>Danh sách set</h5>
                </div>
                <a href="{{ route('admin.sets.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm set
                </a>
            </div>

            <div class="filter-section">
                <form action="{{ route('admin.sets.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-4">
                            <label for="name_filter">Tên set</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                   placeholder="Tìm theo tên set" value="{{ request('name') }}">
                        </div>
                        <div class="col-4">
                            <label for="type_filter">Loại</label>
                            <select id="type_filter" name="type" class="filter-input">
                                <option value="">-- Tất cả --</option>
                                <option value="{{ \App\Models\Set::TYPE_FREE }}" {{ request('type') === \App\Models\Set::TYPE_FREE ? 'selected' : '' }}>Miễn phí</option>
                                <option value="{{ \App\Models\Set::TYPE_PREMIUM }}" {{ request('type') === \App\Models\Set::TYPE_PREMIUM ? 'selected' : '' }}>Premium</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">-- Tất cả --</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Kích hoạt</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tắt</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.sets.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if ($sets->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        @if (request()->hasAny(['name','type','status']))
                            <h4>Không tìm thấy set nào</h4>
                            <p>Không có set nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.sets.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có set nào</h4>
                            <p>Bắt đầu bằng cách thêm set đầu tiên.</p>
                            <a href="{{ route('admin.sets.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm set mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên set</th>
                                    <th class="column-medium">Logo</th>
                                    <th class="column-small">Loại</th>
                                    <th class="column-small">Trạng thái</th>
                                    <th class="column-small">Kích thước</th>
                                    <th class="column-small">Giá</th>
                                    <th class="column-small">Nổi bật</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sets as $index => $set)
                                    <tr>
                                        <td class="text-center">{{ ($sets->currentPage() - 1) * $sets->perPage() + $index + 1 }}</td>
                                        <td class="item-title"><strong>{{ $set->name }}</strong></td>
                                        <td>
                                            @if ($set->image)
                                                <img src="{{ Storage::url($set->image) }}" alt="image" style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td>{{ $set->type }}</td>
                                        <td>{{ $set->status ? 'Kích hoạt' : 'Tắt' }}</td>
                                        <td>{{ $set->size }}</td>
                                        <td>{{ number_format($set->price) }}</td>
                                        <td>
                                            @if($set->is_featured)
                                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Có</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Không</span>
                                            @endif
                                        </td>
                                        <td class="category-date">{{ $set->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.sets.show', $set) }}" class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.sets.edit', $set) }}" class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $set->id,
                                                    'route' => route('admin.sets.destroy', $set),
                                                    'message' => "Bạn có chắc chắn muốn xóa set '{$set->name}'?",
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
                            Hiển thị {{ $sets->firstItem() ?? 0 }} đến {{ $sets->lastItem() ?? 0 }} của {{ $sets->total() }} set
                        </div>
                        <div class="pagination-controls">
                            {{ $sets->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


