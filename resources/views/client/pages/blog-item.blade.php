@extends('client.layouts.app')
@section('title', 'Blog')
@section('description', 'Blog')
@section('keyword', 'Blog')

@section('content')
    <div class="container-custom blog-container">
        <div class="pt-3">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb color-primary-12">
                    <li class="breadcrumb-item "><a class="color-primary-12 text-decoration-none"
                            href="{{ route('home') }}">TRANG CHỦ</a></li>
                    <li class="breadcrumb-item "><a class="color-primary-12 text-decoration-none"
                            href="{{ route('blog') }}">blog</a></li>
                    <li class="breadcrumb-item color-primary-12 active fw-semibold" aria-current="page">Blog nè</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12 col-lg-9 mt-5">
                <div class="bg-blog-item p-4 position-relative">
                    <div class="blog-layout d-flex">
                        <div class="blog-button-wrap">
                            <x-client.button-blog-item />
                        </div>
                
                        <div class="blog-main flex-grow-1 ps-md-4">
                            <div class="text-center">
                                <h2 class="color-primary-12 fs-4 fw-semibold title-blog-item">
                                    Xu hướng thiết kế đồ họa năm 2020: Phá vỡ mọi quy tắc
                                </h2>
                
                                <div class="my-2 d-flex justify-content-center align-items-center info-blog">
                                    <span class="color-primary-13">By <span class="fw-semibold">Nam Phương</span></span>
                                    <span class="color-primary-13 fs-3">•</span>
                                    <span class="d-flex align-items-center">
                                        <img class="img-info-blog me-1" src="{{ asset('images/svg/blogs/time-blue.svg') }}" alt="time">
                                        <span id="blogDate">02/10/2025</span>
                                    </span>
                                    <span class="d-flex align-items-center">
                                        <img class="img-info-blog me-1" src="{{ asset('images/svg/blogs/view-blue.svg') }}" alt="view">
                                        <span id="blogViews">232</span>
                                    </span>
                                </div>
                            </div>
                
                            <p class="fst-italic color-primary-12 text-md">
                                Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không “giảm nhiệt” trong năm nay...
                            </p>
                
                            <div class="px-md-5">
                                <div class="menu-content-main px-5 py-2">
                                    <span class="fw-semibold">
                                        <img class="me-3" src="{{ asset('images/svg/blogs/menu-main.svg') }}" alt="">
                                        Nội dung chính bài viết
                                    </span>
                                    <div class="mt-3">
                                        <ol class="blog-list mb-0">
                                            <li>3D và chủ nghĩa hiện thực</li>
                                            <li>Sử dụng màu đơn sắc</li>
                                            <li>Hiệu ứng kim loại sáng bóng</li>
                                            <li>Cơn sốt Typography</li>
                                            <li>Tạo Mask cho hình ảnh và văn bản</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

                <div class="mt-4 ps-md-5 pe-md-2">
                    <x-client.about-content :content="'Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không “giảm nhiệt” trong năm nay. Các công nghệ hiện đại và các phần mềm mang lại nhiều cơ hội cho xu hướng 3D phát triển, chúng ta sẽ tiếp tục thấy nhiều tác phẩm thiết kế đồ họa 3D tuyệt vời hơn vào năm 2020. Để tăng sự sáng tạo, các nhà thiết kế thường kết hợp chúng với các yếu tố khác, chẳng hạn như hình ảnh và các yếu tố 2D. Số lượt tìm kiếm “quần áo giá rẻ” bắt đầu giảm mạnh, trong khi cùng thời điểm này, số lượt tìm kiếm “quần áo bền vững” tăng mạnh. Trong cuốn Thế giới không rác thải, tác giả Ron Gonen cho rằng sự chú ý vào xu hướng phát triển bền vững trong ngành thời trang đang tăng đột phá.'" />

                    <div class="mt-4 tag-share-blog">
                        <div class="d-flex align-items-start">
                            <span class="tags-blog-item px-1 me-2 text-xs-2">
                                <img class="me-1" src="{{ asset('images/svg/search-results/tag.svg') }}" alt="">
                                Tags:
                            </span>
                            <div class="color-primary-13">
                                <span>Xu hướng thiết kế</span> ,
                                <span>Chiến lược thương hiệu</span> ,
                                <span>Bài học kinh doanh</span>
                            </div>
                        </div>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                            target="_blank" rel="noopener noreferrer" class="btn rounded-0 btn-share-fb p-1 fw-semibold mt-2 mt-lg-0">
                            <div class="d-flex align-items-center">
                                <img class="me-1" src="{{ asset('images/d/blogs/logo-facebook.png') }}"
                                    alt="logo-facebook">
                                Chia sẻ
                            </div>
                        </a>

                    </div>

                    <div class="mt-4"> 
                        <x-client.related-blogs />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3 mt-4">
                <x-blog-sidebar />

                <div>
                    <img class="img-fluid w-100 img-banner-blog-item" src="{{ asset('/images/d/dev/blogs/banner1.png') }}" alt="banner1">
                </div>
            </div>
        </div>

        <div class="mt-4 mt-md-5 px-0">
            <x-client.content-image :image-src="asset('/images/d/contents/content1.png')" image-alt="" button-text="> Xem thêm" position-x="31%"
                position-y="80%" button-class="px-3 py-2" />
        </div>

        <div class="pt-3 pt-md-5 mt-md-5">
            <x-client.desktop desktop-image="images/d/desktops/desktop.png"
                background-image="images/d/desktops/background.png" frame-image="images/d/desktops/khung.png"
                alt="Desktop Screenshot" />
        </div>
    </div>
@endsection

@push('scripts')
@endpush

@push('styles')
    @vite('resources/assets/frontend/css/styles-blog.css')
    @vite('resources/assets/frontend/css/blog-sidebar.css')
@endpush
