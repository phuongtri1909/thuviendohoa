@extends('client.layouts.app')
@section('title', 'Home - ' . config('app.name'))
@section('description', config('app.name') . ' ')
@section('keywords', config('app.name'))

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <x-banner-carousel :banners="$banners" :hasBanners="$has_banners" :categories="$categories" :interval="3500" />
    <div class="featured-section container-custom">
        <div class="bg-featured-section px-4 pb-4 ">
            <x-client.featured-collections title="Bộ sưu tập nổi bật" :albums="$featuredAlbums" />
        </div>
    </div>

    <div class="container-custom">
        <div class="mt-3 bg-featured-section px-4 pb-4">
            <x-client.featured-collections title="TOP lĩnh vực thịnh hành" :albums="$trendingAlbums" />

            <div class="d-flex justify-content-center">
                <a href="{{ route('albums') }}"
                    class="view-all-collections mt-3 text-md text-white btn px-3 text-decoration-none">
                    Xem tất cả bộ sưu tập
                </a>
            </div>

        </div>
    </div>

    @if ($contentImage1 && $contentImage1->image)
        <div class="mt-4 mt-md-5 container-custom">
            <x-client.content-image :image-src="str_starts_with($contentImage1->image, 'content-images/')
                ? Storage::url($contentImage1->image)
                : asset($contentImage1->image)" image-alt="{{ $contentImage1->name }}"
                button-text="{{ $contentImage1->button_text ?? '> Xem thêm' }}"
                position-x="{{ $contentImage1->button_position_x ?? '50%' }}"
                position-y="{{ $contentImage1->button_position_y ?? '50%' }}" button-class="px-3 py-2" :url="$contentImage1->url" />
        </div>
    @endif

    @if ($contentImage2 && $contentImage2->image)
        <div class="mt-3 mt-md-5">
            <x-client.simple-content-image :image-src="str_starts_with($contentImage2->image, 'content-images/')
                ? Storage::url($contentImage2->image)
                : asset($contentImage2->image)" image-alt="{{ $contentImage2->name }}" :url="$contentImage2->url" />
        </div>
    @endif

    <div class="pt-3 pt-md-5 mt-md-5">
        <x-client.desktop desktop-image="images/d/desktops/desktop.png" background-image="images/d/desktops/background.png"
            frame-image="images/d/desktops/khung.png" alt="Desktop Screenshot" />
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/home.css')
@endpush
