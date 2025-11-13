@extends('admin.layouts.sidebar')

@section('title', 'Quản lý set')

@section('main-content')
    <div class="category-container">

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
                                <option value="{{ \App\Models\Set::TYPE_FREE }}"
                                    {{ request('type') === \App\Models\Set::TYPE_FREE ? 'selected' : '' }}>Miễn phí</option>
                                <option value="{{ \App\Models\Set::TYPE_PREMIUM }}"
                                    {{ request('type') === \App\Models\Set::TYPE_PREMIUM ? 'selected' : '' }}>Premium
                                </option>
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
                    <div class="row">
                        <div class="col-4">
                            <label for="category_filter">Danh mục</label>
                            <select id="category_filter" name="category_id" class="filter-input">
                                <option value="">-- Tất cả --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="album_filter">Album</label>
                            <select id="album_filter" name="album_id" class="filter-input">
                                <option value="">-- Tất cả --</option>
                                @foreach ($albums as $album)
                                    <option value="{{ $album->id }}"
                                        {{ request('album_id') == $album->id ? 'selected' : '' }}>
                                        {{ $album->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="color_filter">Màu sắc</label>
                            <select id="color_filter" name="color_id" class="filter-input">
                                <option value="">-- Tất cả --</option>
                                @foreach ($colors as $color)
                                    <option value="{{ $color->id }}"
                                        {{ request('color_id') == $color->id ? 'selected' : '' }}>
                                        {{ $color->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="tag_filter">Tag</label>
                            <select id="tag_filter" name="tag_id" class="filter-input">
                                <option value="">-- Tất cả --</option>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}"
                                        {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="software_filter">Phần mềm</label>
                            <select id="software_filter" name="software_id" class="filter-input">
                                <option value="">-- Tất cả --</option>
                                @foreach ($software as $soft)
                                    <option value="{{ $soft->id }}"
                                        {{ request('software_id') == $soft->id ? 'selected' : '' }}>
                                        {{ $soft->name }}
                                    </option>
                                @endforeach
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
                        @if (request()->hasAny(['name', 'type', 'status', 'category_id', 'album_id', 'color_id', 'tag_id', 'software_id']))
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
                    <div class="table-responsive">
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
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-medium">Danh mục</th>
                                    <th class="column-medium">Album</th>
                                    <th class="column-medium">Màu sắc</th>
                                    <th class="column-medium">Tag</th>
                                    <th class="column-medium">Phần mềm</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sets as $index => $set)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($sets->currentPage() - 1) * $sets->perPage() + $index + 1 }}</td>
                                        <td class="item-title"><strong>{{ $set->name }}</strong></td>
                                        <td>
                                            @if ($set->image)
                                                <img src="{{ Storage::url($set->image) }}" alt="image"
                                                    style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td>{{ $set->type }}</td>
                                        <td>{{ $set->status ? 'Kích hoạt' : 'Tắt' }}</td>
                                        <td>{{ $set->size }}</td>
                                        <td>{{ number_format($set->price) }}</td>
                                        <td>
                                            @if ($set->is_featured)
                                                <span
                                                    class="badge bg-success-subtle text-success-emphasis rounded-pill">Có</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Không</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="order-badge">{{ $set->order }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $categories = $set->categories->pluck('category.name')->take(3);
                                                $totalCategories = $set->categories->count();
                                            @endphp
                                            <div class="relation-items">
                                                @foreach($categories as $catName)
                                                    <span class="relation-badge">{{ $catName }}</span>
                                                @endforeach
                                                @if($totalCategories > 3)
                                                    <button type="button" class="btn-show-more" 
                                                        onclick="showMoreItems(this, 'categories', {{ $set->id }})"
                                                        data-total="{{ $totalCategories }}">
                                                        +{{ $totalCategories - 3 }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $albums = $set->albums->pluck('album.name')->take(3);
                                                $totalAlbums = $set->albums->count();
                                            @endphp
                                            <div class="relation-items">
                                                @foreach($albums as $albumName)
                                                    <span class="relation-badge">{{ $albumName }}</span>
                                                @endforeach
                                                @if($totalAlbums > 3)
                                                    <button type="button" class="btn-show-more" 
                                                        onclick="showMoreItems(this, 'albums', {{ $set->id }})"
                                                        data-total="{{ $totalAlbums }}">
                                                        +{{ $totalAlbums - 3 }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $colors = $set->colors->pluck('color.name')->take(3);
                                                $totalColors = $set->colors->count();
                                            @endphp
                                            <div class="relation-items">
                                                @foreach($colors as $colorName)
                                                    <span class="relation-badge">{{ $colorName }}</span>
                                                @endforeach
                                                @if($totalColors > 3)
                                                    <button type="button" class="btn-show-more" 
                                                        onclick="showMoreItems(this, 'colors', {{ $set->id }})"
                                                        data-total="{{ $totalColors }}">
                                                        +{{ $totalColors - 3 }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $tags = $set->tags->pluck('tag.name')->take(3);
                                                $totalTags = $set->tags->count();
                                            @endphp
                                            <div class="relation-items">
                                                @foreach($tags as $tagName)
                                                    <span class="relation-badge">{{ $tagName }}</span>
                                                @endforeach
                                                @if($totalTags > 3)
                                                    <button type="button" class="btn-show-more" 
                                                        onclick="showMoreItems(this, 'tags', {{ $set->id }})"
                                                        data-total="{{ $totalTags }}">
                                                        +{{ $totalTags - 3 }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $software = $set->software->pluck('software.name')->take(3);
                                                $totalSoftware = $set->software->count();
                                            @endphp
                                            <div class="relation-items">
                                                @foreach($software as $softName)
                                                    <span class="relation-badge">{{ $softName }}</span>
                                                @endforeach
                                                @if($totalSoftware > 3)
                                                    <button type="button" class="btn-show-more" 
                                                        onclick="showMoreItems(this, 'software', {{ $set->id }})"
                                                        data-total="{{ $totalSoftware }}">
                                                        +{{ $totalSoftware - 3 }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="category-date">{{ $set->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.sets.show', $set) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.sets.edit', $set) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $set->id,
                                                    'route' => route('admin.sets.destroy', $set),
                                                    'message' => "Bạn có chắc chắn muốn xóa set '{$set->name}'?",
                                                ])
                                                <form method="POST" action="{{ route('admin.sets.clean-files', $set) }}"
                                                    id="clean-files-form-{{ $set->id }}"
                                                    style="display: inline-block;">
                                                    @csrf
                                                    <button type="button" class="action-icon text-decoration-none"
                                                        onclick="confirmCleanFiles({{ $set->id }})"
                                                        title="Làm sạch file">
                                                        <i class="fas fa-broom" style="color: #ff5722;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $sets->firstItem() ?? 0 }} đến {{ $sets->lastItem() ?? 0 }} của
                            {{ $sets->total() }} set
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

@push('styles')
    <style>
        .main-content,
        .content-wrapper,
        .content,
        .container-custom,
        .category-container,
        .content-card,
        .card-content {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }
        .table-responsive {
            overflow-x: auto !important;
            overflow-y: visible !important;
            -webkit-overflow-scrolling: touch;
        }
        .data-table {
            margin-bottom: 0;
            width: 100%;
            min-width: 1200px;
        }
        .relation-items {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: center;
        }
        .relation-badge {
            display: inline-block;
            padding: 2px 6px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 4px;
            font-size: 11px;
            white-space: nowrap;
        }
        .btn-show-more {
            padding: 2px 6px;
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-show-more:hover {
            background: #ffc107;
            color: #000;
        }
        .order-badge {
            background: #fff3cd;
            color: #856404;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        /* Tăng chiều rộng cột Tên set */
        .data-table thead tr th:nth-child(2),
        .data-table tbody tr td:nth-child(2) {
            width: 50% !important;
            min-width: 300px;
        }
        .item-title {
            word-break: break-word;
            line-height: 1.4;
        }
        /* Sắp xếp nút thao tác thành 2 cột 2 hàng */
        .action-buttons-wrapper {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 6px;
            max-width: 80px;
        }
        .action-buttons-wrapper .action-icon,
        .action-buttons-wrapper form {
            width: 100%;
            margin: 0;
        }
        .action-buttons-wrapper .action-icon {
            width: 32px;
            height: 32px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function confirmCleanFiles(setId) {
            Swal.fire({
                title: 'Xác nhận làm sạch file',
                html: 'Bạn có chắc muốn xóa file tạm và file ZIP của set này?<br><br><small class="text-muted">Hành động này sẽ buộc người dùng download lại file mới từ Drive.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff5722',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-broom me-1"></i> Làm sạch',
                cancelButtonText: 'Hủy',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('clean-files-form-' + setId).submit();
                }
            });
        }

        const setsData = @json($setsData);

        function showMoreItems(btn, type, setId) {
            const total = parseInt(btn.getAttribute('data-total'));
            const setData = setsData[setId] || {};
            const allItems = setData[type] || [];
            const itemList = allItems.map(item => `<span class="relation-badge">${item}</span>`).join('');
            
            Swal.fire({
                title: type.charAt(0).toUpperCase() + type.slice(1) + ' (' + total + ')',
                html: '<div class="relation-items" style="justify-content: center; max-width: 100%;">' + itemList + '</div>',
                width: '600px',
                showConfirmButton: true,
                confirmButtonText: 'Đóng'
            });
        }
    </script>
@endpush
