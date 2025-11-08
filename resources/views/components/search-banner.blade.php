@props(['banners', 'hasBanners', 'interval' => 4000])

@if($hasBanners)
    @foreach($banners as $index => $banner)
        <div class="banner-static">
            <img src="{{ Storage::url($banner->image) }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">

            <div class="container-custom container-banner-static">
                <div class="banner-content">
                    <nav class="banner-breadcrumb" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">TRANG CHỦ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
                        </ol>
                    </nav>

                    <div class="banner-search-title text-center">
                        <h2 class="banner-title mt-2 fw-bold text-white">Kết quả tìm kiếm cho "{{ request()->get('q', '') }}"</h2>
                        <span class="text-white">Hiển thị 510 kết quả</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <!-- Default banner when no banners in database -->
    <div class="banner-static">
        <img src="{{ asset('/images/d/banners/banner3.png') }}" class="d-block w-100" alt="Banner">

        <div class="container-custom container-banner-static">
            <div class="banner-content">
                <nav class="banner-breadcrumb" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TRANG CHỦ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
                    </ol>
                </nav>

                <div class="banner-search-title text-center">
                    <h2 class="banner-title fw-bold text-white">Kết quả tìm kiếm cho "{{ request()->get('q', '') }}"</h2>
                    <span class="text-white">Hiển thị 510 kết quả</span>
                </div>
            </div>
        </div>
    </div>
@endif
