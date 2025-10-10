@extends('client.layouts.app')
@section('title', 'Home - ' . config('app.name'))
@section('description', config('app.name') . ' ')
@section('keywords', config('app.name'))

@section('content')
    <!-- Banner Slide -->
    <div id="bannerCarousel" class="carousel slide banner-slide" data-bs-ride="carousel" data-bs-interval="3500">
        <div class="carousel-inner" l'>
            <div class="carousel-item active">
                <img src="{{ asset('/images/d/banners/banner1.png') }}" class="d-block w-100" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('/images/d/banners/banner2.png') }}" class="d-block w-100" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('/images/d/banners/banner3.png') }}" class="d-block w-100" alt="Banner 3">
            </div>
        </div>

        <!-- Icon Buttons -->
        <div class="banner-icons">
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/text-effect-3d.png') }}" alt="">
                Hiệu ứng chữ (text effect 3D)
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/font-vh.png') }}" alt="">
                Tổng hợp font Việt Hóa
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/mockup-branding.png') }}" alt="">
                Bộ Mockup Branding
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/effect.png') }}" alt="">
                Hiệu ứng ánh sáng (effects)
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/adobe.png') }}" alt="">
                Full bộ Adobe bản quyền
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/plugin-preset.png') }}" alt="">
                Plugin, Preset, Brushes thiết kế
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/software.png') }}" alt="">
                Tổng hợp phần mềm cần thiết
            </div>
        </div>
    </div>

    <!-- Component Giữa -->
    <div class="featured-section">
        <div class="bg-featured-section px-4 pb-4 container-xxl">
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

    <div class="mt-3 bg-featured-section px-4 pb-4 container-xxl">
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

    <div class="mt-4 mt-md-5 container-xxl px-0">
        <x-client.content-image 
            :image-src="asset('/images/d/contents/content1.png')"
            image-alt=""
            button-text="> Xem thêm"
            position-x="31%"
            position-y="80%"
            button-class="px-3 py-2"
        />
    </div>

    <div class="mt-3 mt-md-5">
        <img src="{{ asset('/images/d/contents/content2.png') }}" alt="" class="img-fluid">
    </div>

    <div class="pt-3 pt-md-5 mt-md-5">
        <x-client.desktop 
            desktop-image="images/d/desktops/desktop.png"
            background-image="images/d/desktops/background.png"
            frame-image="images/d/desktops/khung.png"
            alt="Desktop Screenshot"
        />
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/home.css')
@endpush

