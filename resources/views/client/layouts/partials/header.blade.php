<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @php
        $currentLocale = app()->getLocale();
        $seoTitle = 'Home - ' . config('app.name');
        $seoDescription = config('app.name');
        $seoKeywords = config('app.name') . ',thu vien';
        $seoThumbnail = asset('images/d/Thumbnail.png');

        if (isset($seoSetting) && $seoSetting) {
            $seoTitle =
                $seoSetting->getTranslation('title', $currentLocale) ?: $seoSetting->getTranslation('title', 'vi');
            $seoDescription =
                $seoSetting->getTranslation('description', $currentLocale) ?:
                $seoSetting->getTranslation('description', 'vi');
            $seoKeywords =
                $seoSetting->getTranslation('keywords', $currentLocale) ?:
                $seoSetting->getTranslation('keywords', 'vi');
            $seoThumbnail = $seoSetting->thumbnail_url;
        } elseif (isset($seoData) && $seoData) {
            $seoTitle = $seoData->title;
            $seoDescription = $seoData->description;
            $seoKeywords = $seoData->keywords;
            $seoThumbnail = $seoData->thumbnail;
        }
    @endphp

    <title>
        @if ($seoTitle)
            {{ $seoTitle }}
        @elseif(@hasSection('title'))
            @yield('title')
        @else
            Home - {{ config('app.name') }}
        @endif
    </title>
    <meta name="description"
        content="@if ($seoDescription) {{ $seoDescription }}@elseif(@hasSection('description'))@yield('description')@else {{ config('app.name') }} @endif">
    <meta name="keywords"
        content="@if ($seoKeywords) {{ $seoKeywords }}@elseif(@hasSection('keywords'))@yield('keywords')@else {{ config('app.name') }},park @endif">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="website">
    <meta property="og:title"
        content="@if ($seoTitle) {{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif">
    <meta property="og:description"
        content="@if ($seoDescription) {{ $seoDescription }}@elseif(@hasSection('description'))@yield('description')@else {{ config('app.name') }} @endif">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:image" content="{{ $seoThumbnail }}">
    <meta property="og:image:secure_url" content="{{ $seoThumbnail }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt"
        content="@if ($seoTitle) {{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
        content="@if ($seoTitle) {{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif">
    <meta name="twitter:description"
        content="@if ($seoDescription) {{ $seoDescription }}@elseif(@hasSection('description'))@yield('description')@else {{ config('app.name') }} @endif">
    <meta name="twitter:image" content="{{ $seoThumbnail }}">
    <meta name="twitter:image:alt"
        content="@if ($seoTitle) {{ $seoTitle }}@elseif(@hasSection('title'))@yield('title')@else Home - {{ config('app.name') }} @endif">
    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="google-site-verification" content="" />
    @verbatim
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('/images/d/Thumbnail.png') }}"
        }
        </script>
    @endverbatim

    @stack('meta')

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->

    {{-- styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    @vite('resources/assets/frontend/css/styles.css')
    @vite('resources/assets/frontend/css/styles-header.css')
    @vite('resources/assets/frontend/css/footer.css')

    @stack('styles')
    {{-- end styles --}}
</head>

<body>
    <header class="header-main" id="header">
        <div class="container-custom">
            <div class="header-custom py-2">
                <div class="header-logo">
                    <a href="{{ route('home') }}" class="logo-link">
                        <img src="{{ $logoPath }}" alt="Logo" style="margin-right: 20px;" height="30px">
                    </a>
                </div>

                <div class="d-flex align-items-center" style="gap: 10px">
                    <div class="header-search d-none d-xl-block">
                        <div class="search-container rounded-5 ps-3">
                            <div class="category-dropdown">
                                <button class="category-btn rounded-5" type="button" id="categoryDropdownBtn">
                                    <img src="{{ asset('/images/svg/category.svg') }}" alt="Category">
                                    <span>Chọn danh mục</span>
                                    <img src="{{ asset('/images/svg/dropdown.svg') }}" alt="Arrow Up"
                                        class="dropdown-arrow">
                                </button>
                                <div class="category-dropdown-menu" id="categoryDropdown">
                                    <div class="row g-1">
                                        <!-- VIP Resources Column -->
                                        <div class="col-6">
                                            <div class="vip-column h-100">
                                                <div class="vip-header">
                                                    <h5 class="mb-0 fs-6">Tài nguyên VIP</h5>
                                                </div>
                                                <div class="vip-content px-3 pb-3 bg-white">
                                                    <div class="row">
                                                        <div class="col-6 p-3 pb-0">
                                                            <img src="{{ asset('/images/svg/image.svg') }}"
                                                                alt="Image">
                                                            <span>Hộp quà đẹp 2016</span>
                                                        </div>
                                                        <div class="col-6 p-3 pb-0">
                                                            <img src="{{ asset('/images/svg/pttx.svg') }}"
                                                                alt="Image">
                                                            <span>Chủ đề 20/11</span>
                                                        </div>
                                                        <div class="col-6 p-3 pb-0">
                                                            <img src="{{ asset('/images/svg/device.svg') }}"
                                                                alt="Image">
                                                            <span>Thiết kế nổi bật 2026</span>
                                                        </div>
                                                        <div class="col-6 p-3 pb-0">
                                                            <img src="{{ asset('/images/svg/image.svg') }}"
                                                                alt="Image">
                                                            <span>Hộp quà đẹp 2016</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- FREE Resources Column -->
                                        <div class="col-6">
                                            <div class="free-column h-100">
                                                <div class="free-header">
                                                    <h5 class="mb-0 fs-6">Tài nguyên MIỄN PHÍ</h5>
                                                </div>
                                                <div class="free-content px-3 pb-3 bg-white">
                                                    <div class="row">
                                                        <div class="col-6 p-3 pb-0">
                                                            <img src="{{ asset('/images/svg/image.svg') }}"
                                                                alt="Image">
                                                            <span>Hộp quà đẹp 2016</span>
                                                        </div>
                                                        <div class="col-6 p-3 pb-0">
                                                            <img src="{{ asset('/images/svg/pttx.svg') }}"
                                                                alt="Image">
                                                            <span>Chủ đề 20/11</span>
                                                        </div>
                                                        <div class="col-6 p-3 pb-0">
                                                            <img src="{{ asset('/images/svg/device.svg') }}"
                                                                alt="Image">
                                                            <span>Thiết kế nổi bật 2026</span>
                                                        </div>
                                                        <div class="col-6 p-3 pb-0">
                                                            <img src="{{ asset('/images/svg/image.svg') }}"
                                                                alt="Image">
                                                            <span>Hộp quà đẹp 2016</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="search-separator"></div>

                            <div class="search-input-container">
                                <form class="search-form" method="GET" action="{{ route('search') }}">
                                    <input type="hidden" name="category" value="{{ request()->get('category') }}">
                                    <input type="hidden" name="album" value="{{ request()->get('album') }}">
                                    @foreach(request()->get('colors', []) as $color)
                                        <input type="hidden" name="colors[]" value="{{ $color }}">
                                    @endforeach
                                    @foreach(request()->get('software', []) as $software)
                                        <input type="hidden" name="software[]" value="{{ $software }}">
                                    @endforeach
                                    <input type="text" class="search-input" name="q"
                                        value="{{ request()->get('q') }}" placeholder="Tìm trong thư viện đồ họa..">
                                    <button class="search-btn" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="header-actions d-none d-md-flex">
                        <a href="{{ route('get.link') }}" class="action-btn get-link-btn rounded-5 p-1 text-decoration-none">
                            <img src="{{ asset('/images/svg/g.svg') }}" alt="Link">
                            <span class="color-primary fw-semibold text-md pe-3">Get Link</span>
                        </a>
                        <a href="{{ route('blog') }}" class="action-btn blog-btn rounded-5 p-1 text-decoration-none">
                            <img src="{{ asset('/images/svg/logo.svg') }}" alt="Blog">
                            <span class="color-primary fw-semibold text-md pe-3">Vietfile Blog</span>
                        </a>

                    </div>
                </div>

                <div class="user-section">
                    @auth
                        <div class="user-dropdown">
                            <button class="notification-btn">
                                <img src="{{ asset('/images/svg/notification.svg') }}" alt="Notification">
                                <span class="notification-badge">3</span>
                            </button>

                            <button class="user-profile-btn" id="userDropdownBtn">
                                <div class="user-avatar-container">
                                    @if (auth()->user() && auth()->user()->avatar)
                                        <img class="avatar" src="{{ Storage::url(auth()->user()->avatar) }}" alt="User">
                                    @else
                                        <img class="avatar-default" src="{{ asset('/images/svg/user.svg') }}" alt="User">
                                    @endif
                                </div>
                                <img src="{{ asset('/images/svg/arrow-down.svg') }}" alt="Arrow Up"
                                    class="dropdown-arrow">
                            </button>

                            <div class="user-dropdown-menu" id="userDropdown">
                                <div class="p-4 pb-2">
                                    <div class="user-info">
                                        <div class="user-avatar-large">
                                            @if (auth()->user() && auth()->user()->avatar)
                                                <img class="avatar" src="{{ Storage::url(auth()->user()->avatar) }}" alt="User">
                                            @else
                                                <img class="avatar-default" src="{{ asset('/images/svg/user.svg') }}" alt="User">
                                            @endif
                                        </div>
                                        <div class="user-details">
                                            <h6 class="user-name">{{ auth()->user()->full_name }}</h6>
                                            <p class="user-email">{{ auth()->user()->email }}</p>
                                            <div class="user-type mt-2">
                                                <img src="{{ asset('/images/svg/gplus.svg') }}" alt="G+">
                                                <span class="text-md color-primary-3">TK THƯỜNG</span>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="download-count">
                                        <p class="text-md color-primary-4 mb-0">Lượt tải file của bạn:</p>
                                        <div class="count-display">
                                            <span class="count-number text-6xl color-primary-3">2</span>
                                            <span class="count-text text-md color-primary-4">lượt</span>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <button class="unlimited-btn rounded-5">
                                        Đăng kí tải không giới hạn
                                    </button>

                                    <ul class="user-menu mt-2">
                                        <li>
                                            <a href="{{ route('admin.dashboard') }}">
                                                <i class="fas fa-user-shield"></i>
                                                <span>Quản trị viên</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('user.profile') }}">
                                                <img src="{{ asset('/images/svg/user.svg') }}" alt="User"
                                                    style="filter: contrast(0);">
                                                <span>Thông tin tài khoản</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <img src="{{ asset('/images/svg/download.svg') }}" alt="Download">
                                                <span>Lịch sử tải xuống (35)</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <img src="{{ asset('/images/svg/whitelist.svg') }}" alt="Whitelist">
                                                <span>Sản phẩm yêu thích (103)</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}">
                                                <img src="{{ asset('/images/svg/logout.svg') }}" alt="Logout">
                                                <span>Đăng xuất</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <button class="partner-btn">
                                    Đăng kí làm đối tác Printon
                                </button>
                            </div>
                        </div>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}"
                            class="action-btn login-btn rounded-5 py-2 px-4 text-decoration-none d-none d-sm-flex">
                            <img src="{{ asset('/images/svg/clock.svg') }}" alt="Login">
                            <span class="color-primary fw-semibold text-md">Đăng nhập</span>
                        </a>
                    @endguest
                </div>

                <div class="d-lg-none">
                    <button class="btn border rounded-circle bg-white" id="mobileMenuToggle">
                        <img src="{{ asset('/images/svg/menu.svg') }}" alt="">
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Register Download Package Button -->
    <div class="register-download-section">
        <div class="container-custom">
            <div class="d-flex justify-content-end">
                <button class="btn btn-danger px-4 py-2 fw-bold text-uppercase">
                    <img src="{{ asset('/images/svg/user.svg') }}" alt="User" class="me-2" width="16"
                        height="16">
                    ĐĂNG KÍ GÓI TẢI
                </button>
            </div>
        </div>
    </div>

    <div class="mobile-side-menu-overlay" id="mobileMenuOverlay"></div>

    <div class="mobile-side-menu" id="mobileSideMenu">
        <div class="mobile-menu-header">
            <a href="{{ route('home') }}" class="logo-link">
                <img src="{{ $logoPath }}" alt="Logo">
            </a>
            <button class="mobile-menu-close" id="mobileMenuClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mobile-search">
            <div class="search-container">
                <form class="search-form" method="GET" action="{{ route('search') }}">
                    <div class="search-input-container">
                        <input type="hidden" name="category" value="{{ request()->get('category') }}">
                        <input type="hidden" name="album" value="{{ request()->get('album') }}">
                        @foreach(request()->get('colors', []) as $color)
                            <input type="hidden" name="colors[]" value="{{ $color }}">
                        @endforeach
                        @foreach(request()->get('software', []) as $software)
                            <input type="hidden" name="software[]" value="{{ $software }}">
                        @endforeach
                        <input type="text" class="search-input" name="q" value="{{ request()->get('q') }}"
                            placeholder="Tìm trong thư viện đồ họa..">
                        <button class="search-btn" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <ul class="mobile-nav-list">
            <li><a href="{{ route('home') }}"
                    class="text-md fw-medium {{ Route::currentRouteNamed('home') ? 'active' : '' }}">{{ __('home') }}</a>
            </li>
            <li>
                <div class="mobile-category-dropdown">
                    <button class="mobile-category-btn" type="button" id="mobileCategoryDropdownBtn">
                        <img src="{{ asset('/images/svg/category.svg') }}" alt="Category">
                        <span>Chọn danh mục</span>
                        <img src="{{ asset('/images/svg/dropdown.svg') }}" alt="Arrow Up"
                            class="mobile-dropdown-arrow">
                    </button>
                    <div class="mobile-category-dropdown-menu" id="mobileCategoryDropdown">
                        <div class="row g-1">
                            <!-- VIP Resources Column -->
                            <div class="col-12">
                                <div class="vip-column h-100">
                                    <div class="vip-header">
                                        <h5 class="mb-0 fs-6">Tài nguyên VIP</h5>
                                    </div>
                                    <div class="vip-content px-3 pb-3">
                                        <div class="row">
                                            <div class="col-12 p-3 pb-0">
                                                <img src="{{ asset('/images/svg/image.svg') }}" alt="Image">
                                                <span>Hộp quà đẹp 2016</span>
                                            </div>
                                            <div class="col-12 p-3 pb-0">
                                                <img src="{{ asset('/images/svg/pttx.svg') }}" alt="Image">
                                                <span>Chủ đề 20/11</span>
                                            </div>
                                            <div class="col-12 p-3 pb-0">
                                                <img src="{{ asset('/images/svg/device.svg') }}" alt="Image">
                                                <span>Thiết kế nổi bật 2026</span>
                                            </div>
                                            <div class="col-12 p-3 pb-0">
                                                <img src="{{ asset('/images/svg/image.svg') }}" alt="Image">
                                                <span>Hộp quà đẹp 2016</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FREE Resources Column -->
                            <div class="col-12">
                                <div class="free-column h-100">
                                    <div class="free-header">
                                        <h5 class="mb-0 fs-6">Tài nguyên MIỄN PHÍ</h5>
                                    </div>
                                    <div class="free-content px-3 pb-3">
                                        <div class="row">
                                            <div class="col-12 p-3 pb-0">
                                                <img src="{{ asset('/images/svg/image.svg') }}" alt="Image">
                                                <span>Hộp quà đẹp 2016</span>
                                            </div>
                                            <div class="col-12 p-3 pb-0">
                                                <img src="{{ asset('/images/svg/pttx.svg') }}" alt="Image">
                                                <span>Chủ đề 20/11</span>
                                            </div>
                                            <div class="col-12 p-3 pb-0">
                                                <img src="{{ asset('/images/svg/device.svg') }}" alt="Image">
                                                <span>Thiết kế nổi bật 2026</span>
                                            </div>
                                            <div class="col-12 p-3 pb-0">
                                                <img src="{{ asset('/images/svg/image.svg') }}" alt="Image">
                                                <span>Hộp quà đẹp 2016</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <div class="mobile-actions">
            <a href="{{ route('get.link') }}" class="action-btn get-link-btn rounded-5 p-1 text-decoration-none">
                <img src="{{ asset('/images/svg/g.svg') }}" alt="Link">
                <span class="color-primary fw-semibold text-md pe-3">Get Link</span>
            </a>
            <a href="{{ route('blog') }}" class="action-btn blog-btn rounded-5 p-1 text-decoration-none">
                <img src="{{ asset('/images/svg/logo.svg') }}" alt="Blog">
                <span class="color-primary fw-semibold text-md pe-3">Vietfile Blog</span>
            </a>
            @guest
                <a href="{{ route('login') }}" class="action-btn login-btn rounded-5 py-2 px-4 text-decoration-none">
                    <img src="{{ asset('/images/svg/clock.svg') }}" alt="Login">
                    <span class="color-primary fw-semibold text-md">Đăng nhập</span>
                </a>
            @endguest
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileSideMenu = document.getElementById('mobileSideMenu');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
            const mobileMenuClose = document.getElementById('mobileMenuClose');

            // Category Dropdown
            const categoryDropdownBtn = document.getElementById('categoryDropdownBtn');
            const categoryDropdown = document.getElementById('categoryDropdown');
            const dropdownArrow = document.querySelector('.dropdown-arrow');

            // User Dropdown
            const userDropdownBtn = document.getElementById('userDropdownBtn');
            const userDropdown = document.getElementById('userDropdown');

            // Mobile Category Dropdown
            const mobileCategoryDropdownBtn = document.getElementById('mobileCategoryDropdownBtn');
            const mobileCategoryDropdown = document.getElementById('mobileCategoryDropdown');
            const mobileDropdownArrow = document.querySelector('.mobile-dropdown-arrow');

            mobileMenuToggle.addEventListener('click', function() {
                mobileSideMenu.classList.add('active');
                mobileMenuOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            function closeMobileMenu() {
                mobileSideMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            mobileMenuClose.addEventListener('click', closeMobileMenu);
            mobileMenuOverlay.addEventListener('click', closeMobileMenu);

            const mobileNavLinks = document.querySelectorAll('.mobile-nav-list a');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                }
            });

            // Category Dropdown Toggle
            if (categoryDropdownBtn && categoryDropdown) {
                categoryDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    console.log('Dropdown clicked');
                    categoryDropdown.classList.toggle('active');
                    console.log('Active class:', categoryDropdown.classList.contains('active'));
                    if (dropdownArrow) {
                        dropdownArrow.style.transform = categoryDropdown.classList.contains('active') ?
                            'rotate(180deg)' : 'rotate(0deg)';
                    }
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!categoryDropdownBtn.contains(e.target) && !categoryDropdown.contains(e.target)) {
                    categoryDropdown.classList.remove('active');
                    dropdownArrow.style.transform = 'rotate(0deg)';
                }
            });

            // Close dropdown on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && categoryDropdown.classList.contains('active')) {
                    categoryDropdown.classList.remove('active');
                    dropdownArrow.style.transform = 'rotate(0deg)';
                }
            });

            // User Dropdown Toggle
            if (userDropdownBtn && userDropdown) {
                userDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('active');
                });
            }

            // Close user dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (userDropdownBtn && userDropdown && !userDropdownBtn.contains(e.target) && !userDropdown
                    .contains(e.target)) {
                    userDropdown.classList.remove('active');
                }
            });

            // Mobile Category Dropdown Toggle
            if (mobileCategoryDropdownBtn && mobileCategoryDropdown) {
                mobileCategoryDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    mobileCategoryDropdown.classList.toggle('active');
                    if (mobileDropdownArrow) {
                        mobileDropdownArrow.style.transform = mobileCategoryDropdown.classList.contains(
                                'active') ?
                            'rotate(180deg)' : 'rotate(0deg)';
                    }
                });
            }

            // Close mobile dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (mobileCategoryDropdownBtn && mobileCategoryDropdown && !mobileCategoryDropdownBtn
                    .contains(e.target) && !mobileCategoryDropdown.contains(e.target)) {
                    mobileCategoryDropdown.classList.remove('active');
                    if (mobileDropdownArrow) {
                        mobileDropdownArrow.style.transform = 'rotate(0deg)';
                    }
                }
            });
        });
    </script>
