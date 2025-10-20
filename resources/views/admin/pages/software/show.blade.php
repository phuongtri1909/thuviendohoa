@extends('Admin.layouts.sidebar')

@section('title', 'Chi tiết phần mềm')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-code icon-title"></i>
                    <h5>Chi tiết phần mềm</h5>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.software.edit', $software) }}" class="action-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.software.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="category-details">
                    <div class="detail-section">
                        <div class="detail-item">
                            <label class="detail-label">Tên:</label>
                            <span class="detail-value">{{ $software->name }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Logo:</label>
                            <div class="detail-value">
                                @if ($software->logo)
                                    <img src="{{ Storage::url($software->logo) }}" alt="logo" style="max-height: 120px; border-radius: 6px;">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Logo hover:</label>
                            <div class="detail-value">
                                @if ($software->logo_hover)
                                    <img src="{{ Storage::url($software->logo_hover) }}" alt="logo hover" style="max-height: 120px; border-radius: 6px;">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Logo active:</label>
                            <div class="detail-value">
                                @if ($software->logo_active)
                                    <img src="{{ Storage::url($software->logo_active) }}" alt="logo active" style="max-height: 120px; border-radius: 6px;">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Ngày tạo:</label>
                            <span class="detail-value">{{ $software->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Cập nhật lần cuối:</label>
                            <span class="detail-value">{{ $software->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


