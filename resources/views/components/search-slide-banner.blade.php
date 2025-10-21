@props([
    'banners',
    'hasBanners',
    'interval' => 4000,
    'selectedCategory' => null,
    'selectedAlbum' => null,
    'sets' => null,
])

@push('styles')
    @vite('resources/assets/frontend/css/search.css')
@endpush

@if($hasBanners && count($banners) > 1)
    <div class="banner-static">
        <!-- Image Carousel -->
        <div id="searchBannerCarousel" class="carousel slide banner-carousel" data-bs-ride="carousel" data-bs-interval="{{ $interval }}">
            <div class="carousel-indicators">
                @foreach($banners as $index => $banner)
                    <button type="button" data-bs-target="#searchBannerCarousel" data-bs-slide-to="{{ $index }}" 
                            class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                            aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>

            <div class="carousel-inner">
                @foreach($banners as $index => $banner)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ Storage::url($banner->image) }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Static Content -->
        <div class="container-custom container-banner-static">
            <div class="banner-content">
                <nav class="banner-breadcrumb" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TRANG CHỦ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
                    </ol>
                </nav>

                <div class="banner-search-title text-center">
                    @if(request()->get('q') || request()->get('category') || request()->get('album'))
                        <h2 class="banner-title mt-5 fw-bold text-white">
                            Kết quả tìm kiếm
                            @if(request()->get('q'))
                                cho "{{ request()->get('q') }}"
                            @endif
                            @if(request()->get('category') && $selectedCategory)
                                - Danh mục: {{ $selectedCategory->name }}
                            @endif
                            @if(request()->get('album') && $selectedAlbum)
                                - Album: {{ $selectedAlbum->name }}
                            @endif
                        </h2>
                        @if($sets)
                            <span class="text-white">Hiển thị {{ $sets->total() }} kết quả</span>
                        @endif
                    @else
                        <h2 class="banner-title mt-5 fw-bold text-white">Tìm kiếm trong thư viện</h2>
                        <span class="text-white">Khám phá hàng nghìn mẫu thiết kế đẹp</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Single banner or default banner -->
    @php
        $banner = $hasBanners && count($banners) > 0 ? $banners[0] : null;
    @endphp
    
    <div class="banner-static">
        <img src="{{ $banner ? Storage::url($banner->image) : asset('/images/d/banners/banner3.png') }}" 
             class="d-block w-100" alt="Search Banner">

        <div class="container-custom container-banner-static">
            <div class="banner-content">
                <nav class="banner-breadcrumb" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TRANG CHỦ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
                    </ol>
                </nav>

                <div class="banner-search-title text-center">
                    @if(request()->get('q') || request()->get('category') || request()->get('album'))
                        <h2 class="banner-title mt-5 fw-bold text-white">
                            Kết quả tìm kiếm
                            @if(request()->get('q'))
                                cho "{{ request()->get('q') }}"
                            @endif
                            @if(request()->get('category') && $selectedCategory)
                                - Danh mục: {{ $selectedCategory->name }}
                            @endif
                            @if(request()->get('album') && $selectedAlbum)
                                - Album: {{ $selectedAlbum->name }}
                            @endif
                        </h2>
                        @if($sets)
                            <span class="text-white">Hiển thị {{ $sets->total() }} kết quả</span>
                        @endif
                    @else
                        <h2 class="banner-title mt-5 fw-bold text-white">Tìm kiếm trong thư viện</h2>
                        <span class="text-white">Khám phá hàng nghìn mẫu thiết kế đẹp</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
