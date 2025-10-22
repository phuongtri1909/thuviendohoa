@extends('admin.layouts.sidebar')

@section('title', 'Quản lý ngân hàng')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Ngân hàng</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-university icon-title"></i>
                    <h5>Danh sách ngân hàng</h5>
                </div>
                <a href="{{ route('admin.banks.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm ngân hàng
                </a>
            </div>

            <div class="card-content">
                @if ($banks->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <h4>Chưa có ngân hàng nào</h4>
                        <p>Bắt đầu bằng cách thêm ngân hàng đầu tiên.</p>
                        <a href="{{ route('admin.banks.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm ngân hàng mới
                        </a>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Logo</th>
                                    <th class="column-large">Tên ngân hàng</th>
                                    <th class="column-medium">Mã ngân hàng</th>
                                    <th class="column-large">Số tài khoản</th>
                                    <th class="column-large">Tên tài khoản</th>
                                    <th class="column-medium">QR Code</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($banks as $index => $bank)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($banks->currentPage() - 1) * $banks->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            @if ($bank->logo)
                                                <img src="{{ Storage::url($bank->logo) }}" alt="{{ $bank->name }}"
                                                    style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $bank->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="bank-code-badge">{{ $bank->code }}</span>
                                        </td>
                                        <td>
                                            <span class="account-number">{{ $bank->account_number }}</span>
                                        </td>
                                        <td>
                                            <span class="account-name">{{ $bank->account_name }}</span>
                                        </td>
                                        <td>
                                            @if ($bank->qr_code)
                                                <img src="{{ Storage::url($bank->qr_code) }}" alt="QR Code"
                                                    style="max-height: 40px; border-radius: 6px;">
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td class="bank-date">
                                            {{ $bank->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.banks.show', $bank) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.banks.edit', $bank) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @include('components.delete-form', [
                                                    'id' => $bank->id,
                                                    'route' => route('admin.banks.destroy', $bank),
                                                    'message' => "Bạn có chắc chắn muốn xóa ngân hàng '{$bank->name}'?",
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
                            Hiển thị {{ $banks->firstItem() ?? 0 }} đến {{ $banks->lastItem() ?? 0 }} của
                            {{ $banks->total() }} ngân hàng
                        </div>
                        <div class="pagination-controls">
                            {{ $banks->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bank-code-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .account-number {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .account-name {
            font-size: 14px;
            color: #495057;
        }

        .bank-date {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
@endpush
