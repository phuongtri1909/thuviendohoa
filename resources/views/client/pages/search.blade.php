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

    <div id="imageModal" class="modal-overlay py-2" style="display: none;">
        <div class="modal-content p-4 container-xxl rounded-4">
            <button class="modal-close" id="closeModal">&times;</button>

            <div class="row">
                <div class="col-12 col-md-7">
                    <img id="modalImage" src="" alt="" class="img-fluid rounded-4">

                    <div class="mt-4">
                        <x-client.social-share 
                            :favorite-count="125"
                            :is-favorited="true"
                        />
                    </div>
                </div>
                <div class="col-12 col-md-5">
                   
                    <span class="color-primary-12">Từ khóa:</span> <a class="color-primary-9" href="#">Backdrop - Phông sự kiện</a> ; <a class="color-primary-9" href="#">Lễ tết</a> - <span class="color-primary-6">Mẫu #502</span>
                    
                    <h4 class="color-primary text-1lg">Khánh lịch 2024 Ất Tỵ với họa tiết vàng</h4>

                    <p class="color-primary-12">
                        Chính sách và thời gian bảo hành sản phẩm sẽ được ghi trong thông tin chi tiết của sản phẩm..
                    </p>

                    <div class="color-primary-12">
                        <div class="modal-info-item">
                            <img src="{{ asset('images/svg/search-results/format.svg') }}" alt="">
                            <span>Định dạng: Illustrator, eps</span>
                        </div>
                        <div class="modal-info-item">
                            <img src="{{ asset('images/svg/search-results/capacity.svg') }}" alt="">
                            <span>Dung lượng: 2.62M</span>
                        </div>
                        <div class="modal-info-item">
                            <img src="{{ asset('images/svg/search-results/whitelist.svg') }}" alt="">
                            <span>Yêu thích: 36</span>
                        </div>
                    </div>

                    <div class="d-flex flex-column mt-4">
                        <x-client.badge value="5 XU" label="Premium" />

                        <button class="btn-download btn fw-semibold py-3 px-5 d-flex mt-2">
                            <img src="{{asset('images/svg/arrow-right.svg')}}" alt="" class="arrow-original">
                            <img src="{{asset('images/svg/arrow-right.svg')}}" alt="" class="arrow-new">
                            Tải về máy
                        </button>
                    </div>

                    <div class="color-primary-12 mt-5">
                        <div class="modal-info-item">
                            <img src="{{ asset('images/svg/search-results/image-modal.svg') }}" alt="">
                            <span class="color-primary-9"> Bạn cần chỉnh sửa hoặc thiết kế file mới, click <a class="fw-semibold color-primary-9" href="https://id.zalo.me/account?continue=https%3A%2F%2Fchat.zalo.me%2F">TẠI ĐÂY</a></span>
                        </div>
                        <div class="modal-info-item">
                            <img src="{{ asset('images/svg/search-results/share.svg') }}" alt="">
                            <a class="color-primary-9 fw-semibold text-decoration-none" href="#">Part chia sẻ file miễn phí</a>
                        </div>
                        <div class="modal-info-item">
                            <img src="{{ asset('images/svg/search-results/hotline.svg') }}" alt="">
                            <span class="color-primary-9">Hotline/Zalo hỗ trợ: 0944 133 994</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="package-info p-3">
                            <div class="info-member">
                                <div class="d-flex flex-column text-center justify-content-center">
                                    <span class="text-2xl-1 fw-semibold total-package-info">2.194 +</span>
                                    <span class="text-sm-2 fw-semibold">Thành viên VIP</span>
                                </div>
                                <div>
                                    <img src="{{ asset('images/d/coins/dong.png') }}" alt="">
                                    <img src="{{ asset('images/d/coins/bac.png') }}" alt="">
                                    <img src="{{ asset('images/d/coins/vang.png') }}" alt="">
                                    <img src="{{ asset('images/d/coins/bachkim.png') }}" alt="">
                                </div>
                            </div>
                            <div class="info-intro mt-3 text-start">
                                <ul class="px-3 text-xs mb-0">
                                    <li>
                                        Cập nhật file mới mỗi ngày
                                    </li>
                                    <li>
                                        Sản phẩm và chủ đề đa dạng
                                    </li>
                                </ul>
                                <button class="btn rounded-5 bg-white text-xs mt-2">
                                    Kích hoạt tải không giới hạn
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
        </div>
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
