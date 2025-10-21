@props(['banners', 'hasBanners', 'searchQuery' => '', 'interval' => 4000])

@push('styles')
    @vite('resources/assets/frontend/css/albums.css')
    @vite('resources/assets/frontend/css/search.css')
@endpush

@if ($hasBanners && count($banners) > 1)
    <div class="banner-static">
        <!-- Image Carousel -->
        <div id="albumsBannerCarousel" class="carousel slide banner-carousel" data-bs-ride="carousel"
            data-bs-interval="{{ $interval }}">
            <div class="carousel-indicators">
                @foreach ($banners as $index => $banner)
                    <button type="button" data-bs-target="#albumsBannerCarousel" data-bs-slide-to="{{ $index }}"
                        class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>

            <div class="carousel-inner">
                @foreach ($banners as $index => $banner)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ Storage::url($banner->image) }}" class="d-block w-100"
                            alt="Albums Banner {{ $index + 1 }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Static Content -->
        <div class="container-custom container-banner-static">
            <div class="banner-content">
                <nav class="banner-breadcrumb" style="--bs-breadcrumb-divider: '>';"
                    aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TRANG CHỦ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bộ sưu tập</li>
                    </ol>
                </nav>

                <div class="banner-search-title text-center">
                    <h2 class="banner-title mt-5 fw-bold text-white">Tất cả bộ sưu tập</h2>
                    <div class="albums-search-form mt-4">
                        <form action="{{ route('albums') }}" method="GET"
                            class="d-flex justify-content-center">
                            <div class="albums-search-input-wrapper">
                                <input type="text" name="search" class="albums-search-input"
                                    placeholder="Tìm kiếm bộ sưu tập..." value="{{ $searchQuery }}">
                                <button type="submit" class="albums-search-btn">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    @php
        $banner = $hasBanners && count($banners) > 0 ? $banners[0] : null;
    @endphp

    <div class="banner-static">
        <img src="{{ $banner ? Storage::url($banner->image) : asset('/images/d/banners/banner3.png') }}"
            class="d-block w-100" alt="Albums Banner">

        <div class="container-custom container-banner-static">
            <div class="banner-content">
                <nav class="banner-breadcrumb" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TRANG CHỦ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bộ sưu tập</li>
                    </ol>
                </nav>

                <div class="banner-search-title text-center">
                    <h2 class="banner-title mt-5 fw-bold text-white">Tất cả bộ sưu tập</h2>
                    <div class="albums-search-form mt-4">
                        <form action="{{ route('albums') }}" method="GET" class="d-flex justify-content-center">
                            <div class="albums-search-input-wrapper">
                                <input type="text" name="search" class="albums-search-input"
                                    placeholder="Tìm kiếm bộ sưu tập..." value="{{ $searchQuery }}">
                                <button type="submit" class="albums-search-btn">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
