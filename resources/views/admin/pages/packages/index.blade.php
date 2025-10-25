@extends('admin.layouts.sidebar')

@section('title', 'Quản lý gói xu')

@section('main-content')
    <div class="category-container">

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-coins icon-title"></i>
                    <h5>Quản lý gói xu</h5>
                </div>
            </div>

            <div class="card-content">
                @if ($packages->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h4>Chưa có gói xu nào</h4>
                        <p>Vui lòng chạy seeder để tạo các gói xu mặc định.</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên gói</th>
                                    <th class="column-medium">Loại</th>
                                    <th class="column-medium">Giá (VNĐ)</th>
                                    <th class="column-medium">Xu</th>
                                    <th class="column-medium">Xu thưởng</th>
                                    <th class="column-medium">Hạn sử dụng (tháng)</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $index => $package)
                                    <tr>
                                        <td class="text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $package->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="package-plan-badge package-plan-{{ $package->plan }}">
                                                {{ ucfirst($package->plan) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="package-amount">{{ number_format($package->amount) }} VNĐ</span>
                                        </td>
                                        <td>
                                            <span class="package-coins">{{ number_format($package->coins) }} xu</span>
                                        </td>
                                        <td>
                                            <span class="package-bonus">{{ number_format($package->bonus_coins) }} xu</span>
                                        </td>
                                        <td>
                                            <span class="package-expiry">{{ $package->expiry }} tháng</span>
                                        </td>
                                        <td class="package-date">
                                            {{ $package->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.packages.edit', $package) }}"
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

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $packages->count() }} gói xu
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .package-plan-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .package-plan-bronze {
            background: #cd7f32;
            color: white;
        }

        .package-plan-silver {
            background: #c0c0c0;
            color: #333;
        }

        .package-plan-gold {
            background: #ffd700;
            color: #333;
        }

        .package-plan-platinum {
            background: #e5e4e2;
            color: #333;
        }

        .package-amount {
            font-weight: 600;
            color: #28a745;
        }

        .package-coins {
            font-weight: 600;
            color: #007bff;
        }

        .package-bonus {
            font-weight: 600;
            color: #ffc107;
        }

        .package-expiry {
            font-size: 14px;
            color: #6c757d;
        }

        .package-date {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
@endpush
