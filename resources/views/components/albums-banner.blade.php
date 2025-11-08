@props(['searchQuery' => ''])

@push('styles')
    @vite('resources/assets/frontend/css/albums.css')
@endpush

<div class="banner-static">
    <img src="{{ asset('/images/d/banners/banner3.png') }}" class="d-block w-100" alt="Albums Banner">

    <div class="container-custom container-banner-static">
        <div class="banner-content">
            <nav class="banner-breadcrumb" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">TRANG CHỦ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bộ sưu tập</li>
                </ol>
            </nav>

            <div class="banner-search-title text-center">
                <h2 class="banner-title mt-2 fw-bold text-white">Tất cả bộ sưu tập</h2>
                <div class="albums-search-form mt-4">
                    <form action="{{ route('albums') }}" method="GET" class="d-flex justify-content-center">
                        <div class="albums-search-input-wrapper">
                            <input type="text" 
                                   name="search" 
                                   class="albums-search-input" 
                                   placeholder="Tìm kiếm bộ sưu tập..." 
                                   value="{{ $searchQuery }}">
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
