@extends('client.layouts.app')
@section('title', 'Home - ' . config('app.name'))
@section('description', config('app.name') . ' ')
@section('keywords', config('app.name'))

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <!-- Banner Slide -->
    <x-banner-carousel 
        :banners="$banners" 
        :hasBanners="$has_banners" 
        :categories="$categories"
        :interval="3500" 
    />

    <!-- Component Giữa -->
    <div class="featured-section container-custom">
        <div class="bg-featured-section px-4 pb-4 ">
            <x-client.featured-collections title="Bộ sưu tập nổi bật" :collections="[
                [
                    'title' => 'Lịch Bính Ngọ 2025',
                    'images' => [
                        'https://picsum.photos/400/300?random=1',
                        'https://picsum.photos/400/300?random=2',
                        'https://picsum.photos/400/300?random=3',
                        'https://picsum.photos/400/300?random=4',
                    ],
                ],
                [
                    'title' => 'Hộp quà đẹp 2025',
                    'images' => [
                        'https://picsum.photos/400/300?random=5',
                        'https://picsum.photos/400/300?random=6',
                        'https://picsum.photos/400/300?random=7',
                        'https://picsum.photos/400/300?random=8',
                    ],
                ],
                [
                    'title' => 'Chủ đề 20/11',
                    'images' => [
                        'https://picsum.photos/400/300?random=9',
                        'https://picsum.photos/400/300?random=10',
                        'https://picsum.photos/400/300?random=11',
                        'https://picsum.photos/400/300?random=12',
                    ],
                ],
                [
                    'title' => 'Thiết kế nổi bật 2025',
                    'images' => [
                        'https://picsum.photos/400/300?random=13',
                        'https://picsum.photos/400/300?random=14',
                        'https://picsum.photos/400/300?random=15',
                        'https://picsum.photos/400/300?random=16',
                    ],
                ],
            ]" />
        </div>
    </div>

    <div class="container-custom">
        <div class="mt-3 bg-featured-section px-4 pb-4">
            <x-client.featured-collections title="TOP lĩnh vực thịnh hành" :collections="[
                [
                    'title' => 'DÀNH CHO THỢ QC',
                    'images' => [
                        'https://picsum.photos/400/300?random=1',
                        'https://picsum.photos/400/300?random=2',
                        'https://picsum.photos/400/300?random=3',
                        'https://picsum.photos/400/300?random=4',
                    ],
                ],
                [
                    'title' => 'Hộp quà đẹp 2025',
                    'images' => [
                        'https://picsum.photos/400/300?random=5',
                        'https://picsum.photos/400/300?random=6',
                        'https://picsum.photos/400/300?random=7',
                        'https://picsum.photos/400/300?random=8',
                    ],
                ],
                [
                    'title' => 'Chủ đề 20/11',
                    'images' => [
                        'https://picsum.photos/400/300?random=9',
                        'https://picsum.photos/400/300?random=10',
                        'https://picsum.photos/400/300?random=11',
                        'https://picsum.photos/400/300?random=12',
                    ],
                ],
                [
                    'title' => 'Lịch Bính Ngọ 2025',
                    'images' => [
                        'https://picsum.photos/400/300?random=13',
                        'https://picsum.photos/400/300?random=14',
                        'https://picsum.photos/400/300?random=15',
                        'https://picsum.photos/400/300?random=16',
                    ],
                ],
                [
                    'title' => 'Hộp quà đẹp 2025',
                    'images' => [
                        'https://picsum.photos/400/300?random=5',
                        'https://picsum.photos/400/300?random=6',
                        'https://picsum.photos/400/300?random=7',
                        'https://picsum.photos/400/300?random=8',
                    ],
                ],
                [
                    'title' => 'Chủ đề 20/11',
                    'images' => [
                        'https://picsum.photos/400/300?random=9',
                        'https://picsum.photos/400/300?random=10',
                        'https://picsum.photos/400/300?random=11',
                        'https://picsum.photos/400/300?random=12',
                    ],
                ],
                [
                    'title' => 'DÀNH CHO THỢ QC',
                    'images' => [
                        'https://picsum.photos/400/300?random=1',
                        'https://picsum.photos/400/300?random=2',
                        'https://picsum.photos/400/300?random=3',
                        'https://picsum.photos/400/300?random=4',
                    ],
                ],
                [
                    'title' => 'Lịch Bính Ngọ 2025',
                    'images' => [
                        'https://picsum.photos/400/300?random=13',
                        'https://picsum.photos/400/300?random=14',
                        'https://picsum.photos/400/300?random=15',
                        'https://picsum.photos/400/300?random=16',
                    ],
                ],
            ]" />

            <div class="d-flex justify-content-center">
                <button class="view-all-collections mt-3 text-md text-white btn px-3">
                    Xem tất cả bộ sưu tập
                </button>
            </div>

        </div>
    </div>

    <div class="mt-4 mt-md-5 px-0">
        <x-client.content-image :image-src="asset('/images/d/contents/content1.png')" image-alt="" button-text="> Xem thêm" position-x="31%" position-y="80%"
            button-class="px-3 py-2" />
    </div>

    <div class="mt-3 mt-md-5">
        <img src="{{ asset('/images/d/contents/content2.png') }}" alt="" class="img-fluid">
    </div>

    <div class="pt-3 pt-md-5 mt-md-5">
        <x-client.desktop desktop-image="images/d/desktops/desktop.png" background-image="images/d/desktops/background.png"
            frame-image="images/d/desktops/khung.png" alt="Desktop Screenshot" />
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/home.css')
@endpush
