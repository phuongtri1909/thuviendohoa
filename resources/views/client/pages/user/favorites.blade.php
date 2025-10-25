@extends('client.layouts.information')

@section('info_title', 'Yêu thích của tôi')
@section('info_description', 'Danh sách yêu thích của bạn trên ' . request()->getHost())
@section('info_keyword', 'Yêu thích, bookmark, ' . request()->getHost())
@section('info_section_title', 'Danh sách yêu thích')
@section('info_section_desc', 'Quản lý các file thiết kế bạn đã yêu thích')

@push('styles')
    <style>
        .favorites-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .favorites-table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .favorites-table-header h4 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .favorites-table-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .favorites-list {
            padding: 0;
        }

        .favorite-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .favorite-item:last-child {
            border-bottom: none;
        }

        .favorite-item:hover {
            background: #f8f9fa;
        }

        .favorite-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .favorite-content {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .favorite-info {
            flex: 1;
        }

        .favorite-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .favorite-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
            color: #666;
        }

        .favorite-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .favorite-category {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .favorite-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .favorite-btn {
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

        .favorite-btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .favorite-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .favorite-btn-danger {
            background: #ff4757;
            color: white;
            border: none;
        }

        .favorite-btn-danger:hover {
            background: #ff3742;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.4);
            color: white;
        }

        .remove-favorite-btn {
            background: none;
            border: none;
            color: #ff4757;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .remove-favorite-btn:hover {
            background: rgba(255, 71, 87, 0.1);
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .favorite-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .favorite-image {
                width: 100%;
                height: 200px;
                margin-right: 0;
            }

            .favorite-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                width: 100%;
            }

            .favorite-actions {
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .favorite-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .favorite-stats h4 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
        }

        .favorite-stats p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .remove-favorite-btn {
            background: none;
            border: none;
            color: #ff4757;
            font-size: 18px;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .remove-favorite-btn:hover {
            background: rgba(255, 71, 87, 0.1);
            transform: scale(1.1);
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .favorite-category {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 10px;
        }

    </style>
@endpush

@section('info_content')
    @if($favorites->count() > 0)
        <div class="favorites-table">
            <div class="favorites-list">
                @foreach($favorites as $favorite)
                    <div class="favorite-item" data-favorite-id="{{ $favorite->id }}" data-set-id="{{ $favorite->set->id ?? 0 }}" data-set-slug="{{ $favorite->set->slug ?? '' }}" onclick="showSetModal({{ $favorite->set->id ?? 0 }})">
                        @if($favorite->set && $favorite->set->image)
                            <img src="{{ Storage::url($favorite->set->image) }}" alt="{{ $favorite->set->name }}" class="favorite-image">
                        @else
                            <div class="favorite-image d-flex align-items-center justify-content-center">
                                <i class="fas fa-image fa-2x text-white opacity-50"></i>
                            </div>
                        @endif
                        
                        <div class="favorite-content">
                            @if($favorite->set)
                                <div class="favorite-info">
                                    <div class="favorite-category">{{ $favorite->set->category->name ?? 'Thiết kế' }}</div>
                                    <h5 class="favorite-title">{{ $favorite->set->name }}</h5>
                                    
                                    <div class="favorite-meta">
                                        <div class="favorite-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $favorite->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="favorite-actions">
                                    <button type="button" class="favorite-btn bg-gradient btn" onclick="event.stopPropagation(); showSetModal({{ $favorite->set->id }})">
                                        <i class="fas fa-eye text-white"></i>
                                    </button>
                                    
                                    <button type="button" class="remove-favorite-btn" onclick="event.stopPropagation(); removeFavorite({{ $favorite->id }})" title="Bỏ yêu thích">
                                        <i class="fas fa-heart-broken"></i>
                                    </button>
                                </div>
                            @else
                                <div class="favorite-info">
                                    <h5 class="favorite-title text-muted">File đã bị xóa</h5>
                                </div>
                                <div class="favorite-actions">
                                    <button type="button" class="favorite-btn favorite-btn-danger" onclick="event.stopPropagation(); removeFavorite({{ $favorite->id }})">
                                        <i class="fas fa-trash"></i>
                                        Xóa khỏi yêu thích
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <x-client.pagination :paginator="$favorites" />
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-heart-broken"></i>
            </div>
            <h3 class="empty-state-title">Chưa có yêu thích nào</h3>
            <p class="empty-state-desc">
                Bạn chưa yêu thích file thiết kế nào. Hãy khám phá và thêm vào danh sách yêu thích của bạn!
            </p>
            <a href="{{ route('home') }}" class="empty-state-btn">
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


        function removeFavorite(favoriteId) {
            Swal.fire({
                title: 'Bỏ yêu thích?',
                text: 'Bạn có chắc chắn muốn bỏ yêu thích file này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                const favoriteItem = document.querySelector(`[data-favorite-id="${favoriteId}"]`);
                if (favoriteItem) {
                    favoriteItem.style.opacity = '0.5';
                    favoriteItem.style.pointerEvents = 'none';
                }

                $.ajax({
                    url: '{{ route("user.favorites.remove") }}',
                    type: 'POST',
                    data: {
                        favorite_id: favoriteId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            
                            // Remove item from DOM
                            if (favoriteItem) {
                                favoriteItem.style.transition = 'all 0.3s ease';
                                favoriteItem.style.transform = 'scale(0.8)';
                                favoriteItem.style.opacity = '0';
                                
                                setTimeout(() => {
                                    favoriteItem.remove();
                                    
                                    // Check if no more favorites
                                    const remainingItems = document.querySelectorAll('.favorite-item');
                                    if (remainingItems.length === 0) {
                                        location.reload();
                                    }
                                }, 300);
                            }
                        } else {
                            showToast(response.message, 'error');
                            if (favoriteItem) {
                                favoriteItem.style.opacity = '1';
                                favoriteItem.style.pointerEvents = 'auto';
                            }
                        }
                    },
                    error: function(xhr) {
                        showToast('Có lỗi xảy ra khi xóa yêu thích', 'error');
                        if (favoriteItem) {
                            favoriteItem.style.opacity = '1';
                            favoriteItem.style.pointerEvents = 'auto';
                        }
                    }
                });
            });
        }

        // Add to favorites function (if needed)
        function addToFavorites(setId) {
            $.ajax({
                url: '{{ route("user.favorites.add") }}',
                type: 'POST',
                data: {
                    set_id: setId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function(xhr) {
                    showToast('Có lỗi xảy ra khi thêm yêu thích', 'error');
                }
            });
        }
    </script>
@endpush
