@extends('admin.layouts.sidebar')

@section('title', 'Quản lý About Content')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-info-circle icon-title"></i>
                    <h5>Danh sách About Content</h5>
                    <small class="text-muted">Quản lý nội dung About Content cho các trang</small>
                </div>
            </div>

            <div class="card-content">
                @if ($aboutContents->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h4>Chưa có About Content nào</h4>
                        <p>Dữ liệu sẽ được tạo tự động khi sử dụng component.</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Key</th>
                                    <th class="column-medium">Tiêu đề</th>
                                    <th class="column-large">Nội dung</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($aboutContents as $index => $content)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="page-info">
                                                <div class="page-name">{{ $content->key }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="seo-title">{{ $content->title ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="content-preview">
                                                {{ Str::limit(strip_tags($content->content), 100) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.about-contents.edit', $content) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
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

@push('styles')
    <style>
        .content-preview {
            color: #6c757d;
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
@endpush

