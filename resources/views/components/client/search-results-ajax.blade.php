@if($sets->count() > 0)
    <div class="result-wrapper" id="masonry-container">
        @foreach($sets as $set)
            @if($set->photos && $set->photos->count() > 0)
                <div class="masonry-item" data-height="tall">
                    <div class="image-card" data-set-type="{{ $set->type }}" data-set-price="{{ $set->price }}" 
                        data-set-id="{{ $set->id }}" onclick="openSetModalFromCard(event, {{ $set->id }})">
                        <img src="{{ Storage::url($set->image) }}" alt="{{ $set->name }}" loading="lazy"
                            class="image-clickable" data-image-url="{{ Storage::url($set->image) }}"
                            data-image-title="{{ $set->name }}"
                            data-set-id="{{ $set->id }}">
                        
                        {{-- Software icons - Góc trên phải --}}
                        @if($set->software && $set->software->count() > 0)
                            <div class="software-icons-overlay">
                                @foreach($set->software as $softwareSet)
                                    @php
                                        $soft = $softwareSet->software;
                                        $logoToShow = $soft->logo_active 
                                            ? Storage::url($soft->logo_active) 
                                            : ($soft->logo_hover 
                                                ? Storage::url($soft->logo_hover) 
                                                : Storage::url($soft->logo));
                                    @endphp
                                    <div class="software-icon-item" title="{{ $soft->name }}">
                                        <img src="{{ $logoToShow }}" alt="{{ $soft->name }}">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        {{-- Hover overlay với các nút action --}}
                        <div class="image-card-hover-overlay">
                            @if($set->type === 'premium')
                                <div class="overlay-price-badge">
                                    <span>{{ $set->price }}</span>
                                    <img src="{{ asset('/images/svg/coins.svg') }}" alt="Xu" class="mt-1">
                                </div>
                            @endif
                            
                            <div class="overlay-actions-right">
                                @auth
                                    @php
                                        $isFavorited = $set->bookmarks && $set->bookmarks->where('user_id', auth()->id())->count() > 0;
                                    @endphp
                                    <button class="overlay-action-btn favorite-btn-card {{ $isFavorited ? 'favorited' : '' }}" 
                                        data-set-id="{{ $set->id }}" 
                                        onclick="toggleFavoriteCard(event, this)"
                                        title="Yêu thích">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none" class="mt-1 heart-svg">
                                            <path d="M11.8828 0.505859C12.9949 0.463268 13.9409 0.814387 14.7637 1.56055L14.9268 1.71582C15.6008 2.40349 16.0009 3.2188 16.1133 4.20215V4.20508C16.2017 4.94809 16.0705 5.65581 15.792 6.36426C15.3403 7.44936 14.6964 8.40091 13.9385 9.32129L13.9326 9.32812L13.9277 9.33594C13.5473 9.83351 13.1373 10.2919 12.6953 10.7451L11.8789 11.5615C11.044 12.3835 10.155 13.1648 9.22852 13.8887C8.91804 14.1203 8.60681 14.3696 8.31641 14.6025C7.55442 14.0279 6.80397 13.4423 6.09961 12.8145L5.77441 12.5186C5.16959 11.9545 4.57928 11.4036 4.04004 10.8301L4.02734 10.8174L3.69238 10.4775C2.9221 9.67654 2.23244 8.81707 1.6377 7.86621C1.23044 7.21185 0.879675 6.54773 0.675781 5.83105L0.597656 5.52051C0.284434 3.93626 0.726723 2.60981 1.89844 1.5293C2.61741 0.889289 3.43781 0.551504 4.40234 0.505859L4.4043 0.504883C5.29292 0.459726 6.12157 0.706887 6.90039 1.19629L6.91016 1.20117C7.28286 1.42424 7.61919 1.71901 7.94238 2.09473L8.32617 2.54004L8.7041 2.09082C9.11036 1.60865 9.60524 1.21953 10.166 0.944336C10.7206 0.685791 11.2829 0.528453 11.8818 0.505859H11.8828Z"/>
                                        </svg>
                                    </button>
                                            @else
                                                <button class="overlay-action-btn favorite-btn-card" 
                                                    onclick="event.stopPropagation(); window.location.href='{{ route('login') }}'"
                                                    title="Đăng nhập để yêu thích">
                                                    <img src="{{ asset('/images/svg/whitelist.svg') }}" alt="Whitelist" class="mt-1">
                                                </button>
                                            @endauth
                                            
                                            <button class="overlay-action-btn download-btn-card" 
                                                data-set-id="{{ $set->id }}"
                                                onclick="handleDownloadClick(event, {{ $set->id }})"
                                                title="Tải về">
                                                <img src="{{ asset('/images/svg/search-results/download.svg') }}" alt="Download" class="mt-1">
                                            </button>
                            </div>
                            
                            {{-- Góc dưới trái: Logo web --}}
                            <div class="overlay-website-logo">
                                <img src="{{ $logoPath }}" alt="Logo">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        
        <div class="masonry-item" data-height="wide">
            <div class="image-card">
                <a href="{{ url('/y-tuong-thiet-ke') }}" class="text-decoration-none color-primary-12">
                    <img src="{{ asset('images/d/bancoytuong.png') }}" alt="Bạn có ý tưởng thiết kế của riêng mình?" loading="lazy">
                </a>
            </div>
        </div>
    </div>
    
    @if($sets->hasPages())
        <div class="pagination-wrapper mt-4">
            {{ $sets->links('components.paginate') }}
        </div>
    @endif
@else
    <div class="d-flex flex-column align-items-center justify-content-center py-5">
        <div class="text-center">
            <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
            <h4 class="text-muted mb-0">Không tìm thấy kết quả nào</h4>
        </div>
    </div>
@endif
