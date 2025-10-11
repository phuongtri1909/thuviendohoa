@extends('client.layouts.app')
@section('title', 'Home - ' . config('app.name'))
@section('description', config('app.name') . ' ')
@section('keywords', config('app.name'))

@section('content')
    <div class="banner-static">
        <img src="{{ asset('/images/d/banners/banner3.png') }}" class="d-block w-100" alt="Banner">

        <div class="container-xxl">
            <div class="banner-content">
                <nav class="banner-breadcrumb" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">TRANG CHỦ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
                    </ol>
                </nav>

                <div class="banner-search-title text-center">
                    <h2 class="banner-title mt-5 fw-bold text-white">Kết quả tìm kiếm cho "{{ request()->get('q') }}"</h2>
                    <span class="text-white">Hiển thị 510 kết quả</span>
                </div>
            </div>
        </div>
    </div>

    <div class="search-section">
        <div class="container-xxl">
            <x-client.search-result />
        </div>
    </div>

    <div class="pt-3 pt-md-5 mt-md-5">
        <x-client.desktop desktop-image="images/d/desktops/desktop.png" background-image="images/d/desktops/background.png"
            frame-image="images/d/desktops/khung.png" alt="Desktop Screenshot" />
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/search.css')
@endpush

@push('scripts')
    <script>
        function adjustBannerMargin() {
            const banner = document.querySelector('.banner-static');
            const searchSection = document.querySelector('.search-section');

            if (banner && searchSection) {
                const searchSectionHeight = searchSection.offsetHeight;
                const bannerTop = banner.offsetHeight * 0.1;

                const totalMargin = searchSectionHeight + bannerTop - 450;

                banner.style.marginBottom = totalMargin + 'px';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            adjustBannerMargin();

            const resizeObserver = new ResizeObserver(function() {
                adjustBannerMargin();
            });

            const banner = document.querySelector('.banner-static');
            const searchSection = document.querySelector('.search-section');

            if (banner) resizeObserver.observe(banner);
            if (searchSection) resizeObserver.observe(searchSection);

            window.addEventListener('resize', function() {
                setTimeout(adjustBannerMargin, 100);
            });
        });
    </script>
@endpush
