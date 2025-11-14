@extends('client.layouts.information')

@section('info_title', 'Đã mua')
@section('info_description', 'Danh sách các file đã mua của bạn trên ' . request()->getHost())
@section('info_keyword', 'Đã mua, mua file, ' . request()->getHost())
@section('info_section_title', 'Danh sách đã mua')
@section('info_section_desc', 'Quản lý các file thiết kế bạn đã mua')

@push('styles')
    <style>
        .purchases-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .purchases-table-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .purchases-table-header h4 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .purchases-table-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .purchases-list {
            padding: 0;
        }

        .purchase-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .purchase-item:last-child {
            border-bottom: none;
        }

        .purchase-item:hover {
            background: #f8f9fa;
        }

        .purchase-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .purchase-content {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .purchase-info {
            flex: 1;
        }

        .purchase-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .purchase-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
            color: #666;
        }

        .purchase-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .purchase-category {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .purchase-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .purchase-btn {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .purchase-btn-primary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
        }

        .purchase-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .purchase-btn-success {
            background: #28a745;
            color: white;
            border: none;
        }

        .purchase-btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .purchase-price {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
            display: inline-block;
        }

        .purchase-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
        }

        .purchase-status.downloaded {
            color: #28a745;
        }

        .purchase-status.not-downloaded {
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .purchase-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .purchase-image {
                width: 100%;
                height: 200px;
                margin-right: 0;
            }

            .purchase-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                width: 100%;
            }

            .purchase-actions {
                width: 100%;
                justify-content: space-between;
            }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .empty-state-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .empty-state-desc {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .empty-state-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .empty-state-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .purchase-stats {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .purchase-stats h4 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
        }

        .purchase-stats p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
    </style>
@endpush

@section('info_content')
    @if($purchases->count() > 0)
        <div class="purchases-table">
            <div class="purchases-list">
                @foreach($purchases as $purchase)
                    <div class="purchase-item" data-purchase-id="{{ $purchase->id }}" data-set-id="{{ $purchase->set->id ?? 0 }}" data-set-slug="{{ $purchase->set->slug ?? '' }}" onclick="showSetModal({{ $purchase->set->id ?? 0 }})">
                        @if($purchase->set && $purchase->set->image)
                            <img src="{{ Storage::url($purchase->set->image) }}" alt="{{ $purchase->set->name }}" class="purchase-image">
                        @else
                            <div class="purchase-image d-flex align-items-center justify-content-center">
                                <i class="fas fa-image fa-2x text-white opacity-50"></i>
                            </div>
                        @endif
                        
                        <div class="purchase-content">
                            @if($purchase->set)
                                <div class="purchase-info">
                                    <h5 class="purchase-title">{{ $purchase->set->name }}</h5>
                                    
                                    <div class="purchase-price">
                                        <i class="fas fa-coins me-1"></i>
                                        {{ number_format($purchase->coins) }} xu
                                    </div>
                                    
                                    <div class="purchase-meta">
                                        <div class="purchase-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $purchase->created_at->format('d/m/Y') }}
                                        </div>
                                        
                                        <div class="purchase-status {{ $purchase->downloaded_at ? 'downloaded' : 'not-downloaded' }}">
                                            <i class="fas {{ $purchase->downloaded_at ? 'fa-check-circle' : 'fa-clock' }}"></i>
                                            {{ $purchase->downloaded_at ? 'Đã tải' : 'Chưa tải' }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="purchase-actions">
                                    <button type="button" class="purchase-btn purchase-btn-primary" onclick="event.stopPropagation(); showSetModal({{ $purchase->set->id }})">
                                        <i class="fas fa-eye"></i>
                                        Xem chi tiết
                                    </button>
                                    
                                    @if($purchase->set->drive_url)
                                        <button type="button" class="purchase-btn purchase-btn-success" onclick="event.stopPropagation(); downloadSet({{ $purchase->set->id }})">
                                            <i class="fas fa-download"></i>
                                            Tải lại
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="purchase-info">
                                    <h5 class="purchase-title text-muted">File đã bị xóa</h5>
                                    <div class="purchase-meta">
                                        <div class="purchase-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $purchase->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <x-client.pagination :paginator="$purchases" />
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="empty-state-title">Chưa có file nào đã mua</h3>
            <p class="empty-state-desc">
                Bạn chưa mua file thiết kế nào. Hãy khám phá và mua những file thiết kế chất lượng!
            </p>
            <a href="{{ route('search') }}" class="empty-state-btn">
                <i class="fas fa-search me-2"></i>Khám phá ngay
            </a>
        </div>
    @endif

@endsection

@push('info_scripts')
    <script>
        // Show set modal - redirect to search page
        function showSetModal(setId) {
            if (!setId || setId === 0) {
                showToast('Không thể hiển thị chi tiết file này', 'error');
                return;
            }

            // Get set slug from data attribute
            const setSlug = document.querySelector(`[data-set-id="${setId}"]`).getAttribute('data-set-slug');
            if (!setSlug) {
                showToast('Không thể tìm thấy slug của file này', 'error');
                return;
            }

            // Redirect to search page with set slug parameter
            window.location.href = `/search?set=${setSlug}`;
        }

        // Download set function
        function downloadSet(setId) {
            if (!setId || setId === 0) {
                showToast('Không thể tải file này', 'error');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Đang tải...',
                text: 'Vui lòng chờ trong giây lát',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Call download API
            fetch(`/user/purchase/confirm/${setId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_confirmed: true,
                    payment_method: 'coins' // File đã mua rồi nên không trừ gì nữa
                })
            })
            .then(response => {
                Swal.close();

                const contentType = response.headers.get('content-type');
                const contentDisposition = response.headers.get('content-disposition');
                
                if (contentType?.includes('application/zip') || contentDisposition?.includes('attachment')) {
                    return response.blob().then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = contentDisposition?.split('filename=')[1] || 'download.zip';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Tải xuống thành công!',
                            text: 'File đã được tải về máy',
                            confirmButtonColor: '#28a745'
                        });
                    });
                }

                return response.json().then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: data.message,
                            confirmButtonColor: '#28a745'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message,
                            confirmButtonColor: '#28a745'
                        });
                    }
                });
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi tải file',
                    confirmButtonColor: '#28a745'
                });
            });
        }
    </script>
@endpush
