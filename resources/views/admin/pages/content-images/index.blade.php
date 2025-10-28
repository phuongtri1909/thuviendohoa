@extends('admin.layouts.sidebar')

@section('title', 'Quản lý Content Images')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-images icon-title"></i>
                    <h5>Danh sách Content Images</h5>
                </div>
            </div>

            <div class="card-content">
                @include('components.alert', ['alertType' => 'alert'])

                @if ($contentImages->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h4>Chưa có content image nào</h4>
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
                                    <th class="column-medium">Hình ảnh</th>
                                    <th class="column-small">Button</th>
                                    <th class="column-small">Trạng thái</th>
                                    <th class="column-medium">Ngày cập nhật</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contentImages as $index => $contentImage)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td><code>{{ $contentImage->key }}</code></td>
                                        <td class="item-title"><strong>{{ $contentImage->name }}</strong></td>
                                        <td>
                                            @if ($contentImage->image)
                                                @if (str_starts_with($contentImage->image, 'content-images/'))
                                                    <img src="{{ Storage::url($contentImage->image) }}" alt="image" style="max-height: 60px; max-width: 120px; border-radius: 6px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset($contentImage->image) }}" alt="image" style="max-height: 60px; max-width: 120px; border-radius: 6px; object-fit: cover;">
                                                @endif
                                            @else
                                                <span class="text-muted">Chưa có</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contentImage->button_text)
                                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill">Có</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Không</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contentImage->status)
                                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Kích hoạt</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Tắt</span>
                                            @endif
                                        </td>
                                        <td class="category-date">{{ $contentImage->updated_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.content-images.edit', $contentImage) }}" class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
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

