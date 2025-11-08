@extends('client.layouts.app')
@section('title', 'Home - ' . config('app.name'))
@section('description', config('app.name') . ' ')
@section('keywords', config('app.name'))

@section('content')
    <x-banner-carousel :banners="$banners" :hasBanners="$has_banners" :categories="$categories" :interval="3500" />
    <div class="featured-section container-custom">
        <div class="bg-featured-section px-4 pb-4 ">
            <x-client.featured-collections title="Bộ sưu tập nổi bật" :albums="$featuredAlbums" />
        </div>
    </div>

    <div class="container-custom">
        <div class="mt-5 bg-featured-section px-4 pb-4">
            <x-client.featured-collections title="TOP lĩnh vực thịnh hành" :albums="$trendingAlbums" />

            <div class="d-flex justify-content-center">
                <a href="{{ route('albums') }}"
                    class="view-all-collections mt-3 text-md text-white btn px-3 text-decoration-none">
                    Xem tất cả bộ sưu tập
                </a>
            </div>

        </div>
    </div>


    <div class="mt-4 mt-md-5 container-custom">
        <x-client.content-image />
    </div>

    <div class="mt-3 mt-md-5">
        <x-client.simple-content-image />
    </div>


    <div class="pt-3 pt-md-5 mt-md-5">
        <x-client.desktop desktop-image="images/d/desktops/desktop.png" background-image="images/d/desktops/background.png"
            frame-image="images/d/desktops/khung.png" alt="Desktop Screenshot" />
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/home.css')
@endpush
