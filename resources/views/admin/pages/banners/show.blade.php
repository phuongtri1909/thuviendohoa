@extends('Admin.layouts.sidebar')

@section('title', 'Chi tiết Banner')

@section('main-content')
    <div class="category-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-eye icon-title"></i>
                    <h5>Chi tiết Banner</h5>
                </div>
                <div class="form-actions">
                    <a href="{{ route('admin.banners.edit', $banner) }}" class="save-button">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                </div>
            </div>

            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">ID</label>
                            <div class="form-control-static">{{ $banner->id }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Trang</label>
                            <div class="form-control-static">
                                <span class="badge badge-{{ $banner->key_page === \App\Models\Banner::PAGE_HOME ? 'primary' : 'info' }}">
                                    {{ $banner->key_page === \App\Models\Banner::PAGE_HOME ? 'Home' : 'Search' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Thứ tự</label>
                            <div class="form-control-static">{{ $banner->order }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Trạng thái</label>
                            <div class="form-control-static">
                                <span class="badge badge-{{ $banner->status ? 'success' : 'danger' }}">
                                    {{ $banner->status ? 'Kích hoạt' : 'Không kích hoạt' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Ngày tạo</label>
                            <div class="form-control-static">{{ $banner->created_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Ngày cập nhật</label>
                            <div class="form-control-static">{{ $banner->updated_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label-custom">Ảnh Banner</label>
                            <div class="form-control-static">
                                <img src="{{ Storage::url($banner->image) }}" alt="Banner" style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.banners.index') }}" class="back-button"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    <a href="{{ route('admin.banners.edit', $banner) }}" class="save-button"><i class="fas fa-edit"></i> Chỉnh sửa</a>
                </div>
            </div>
        </div>
    </div>
@endsection
