@extends('admin.layouts.sidebar')

@section('title', 'Quản lý phần mềm')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-code icon-title"></i>
                    <h5>Danh sách phần mềm</h5>
                </div>
                <a href="{{ route('admin.software.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm phần mềm
                </a>
            </div>

            <div class="filter-section">
                <form action="{{ route('admin.software.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="name_filter">Tên phần mềm</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                placeholder="Tìm theo tên" value="{{ request('name') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.software.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                

                @if ($software->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h4>Chưa có phần mềm nào</h4>
                        <p>Bắt đầu bằng cách thêm phần mềm đầu tiên.</p>
                        <a href="{{ route('admin.software.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm phần mềm
                        </a>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên</th>
                                    <th class="column-medium">Logo</th>
                                    <th class="column-medium">Logo hover</th>
                                    <th class="column-medium">Logo active</th>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($software as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ ($software->currentPage() - 1) * $software->perPage() + $index + 1 }}</td>
                                        <td class="item-title"><strong>{{ $item->name }}</strong></td>
                                        <td>
                                            @if ($item->logo)
                                                <img src="{{ Storage::url($item->logo) }}" alt="logo" style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->logo_hover)
                                                <img src="{{ Storage::url($item->logo_hover) }}" alt="logo hover" style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->logo_active)
                                                <img src="{{ Storage::url($item->logo_active) }}" alt="logo active" style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="order-badge">{{ $item->order }}</span>
                                        </td>
                                        <td class="category-date">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.software.show', $item) }}" class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.software.edit', $item) }}" class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $item->id,
                                                    'route' => route('admin.software.destroy', $item),
                                                    'message' => "Bạn có chắc chắn muốn xóa phần mềm '{$item->name}'?",
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
                            Hiển thị {{ $software->firstItem() ?? 0 }} đến {{ $software->lastItem() ?? 0 }} của
                            {{ $software->total() }} phần mềm
                        </div>
                        <div class="pagination-controls">
                            {{ $software->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .category-date { font-size: 14px; color: #6c757d; }
        .order-badge {
            background: #fff3cd;
            color: #856404;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
@endpush


