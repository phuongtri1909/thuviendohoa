@extends('admin.layouts.sidebar')

@section('title', 'Quản lý Desktop Content')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-desktop icon-title"></i>
                    <h5>Danh sách Desktop Content</h5>
                </div>
            </div>

            <div class="card-content">

                @if ($desktopContents->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <h4>Chưa có desktop content nào</h4>
                        <p>Vui lòng chạy seeder để tạo dữ liệu mặc định.</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Key</th>
                                    <th class="column-large">Tên</th>
                                    <th class="column-medium">Logo</th>
                                    <th class="column-medium">Tiêu đề</th>
                                    <th class="column-small">Số tính năng</th>
                                    <th class="column-small">Trạng thái</th>
                                    <th class="column-medium">Ngày cập nhật</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($desktopContents as $index => $content)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td><code>{{ $content->key }}</code></td>
                                        <td class="item-title"><strong>{{ $content->name }}</strong></td>
                                        <td>
                                            @if ($content->logo)
                                                @if (str_starts_with($content->logo, 'desktop-content/'))
                                                    <img src="{{ Storage::url($content->logo) }}" alt="logo" style="max-height: 40px; border-radius: 6px;">
                                                @else
                                                    <img src="{{ asset($content->logo) }}" alt="logo" style="max-height: 40px; border-radius: 6px;">
                                                @endif
                                            @else
                                                <span class="text-muted">Chưa có</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($content->title, 30) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                                {{ count($content->features) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($content->status)
                                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Kích hoạt</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Tắt</span>
                                            @endif
                                        </td>
                                        <td class="category-date">{{ $content->updated_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.desktop-contents.edit', $content) }}" class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

