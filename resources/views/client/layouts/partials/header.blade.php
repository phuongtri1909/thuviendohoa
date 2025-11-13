<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="vi_VN">

    {!! SEO::generate() !!}

    @stack('custom_schema')

    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $faviconPath }}">

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="google-site-verification" content="pQiP1ejMlkYnhemJmmmDhBPesa7pbMtjNjZTSZBaksM" />
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
                        <img src="{{ $logoPath }}" alt="Logo" style="margin-right: 20px;">
                    </a>
                </div>

                <div class="d-flex align-items-center header-center" style="gap: 10px">
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
                                                <div class="vip-content bg-white">
                                                    <div class="row g-0">
                                                        @forelse($headerVipAlbums as $album)
                                                            <div class="col-6 col-album py-2">
                                                                <a href="{{ route('search', ['album' => $album->slug, 'type' => 'premium']) }}"
                                                                    class="text-decoration-none"
                                                                    style="display: flex; align-items: center; gap: 8px; padding-left: 25px;">
                                                                    <img src="{{ Storage::url($album->icon ?? $album->image) }}"
                                                                        alt="{{ $album->name }}"
                                                                        style="width: 24px; height: 24px; object-fit: contain;">
                                                                    <span class="text-dark">{{ $album->name }}</span>
                                                                </a>
                                                            </div>
                                                        @empty
                                                            <div class="col-12 p-3 pb-0 text-center text-muted">
                                                                Chưa có album VIP
                                                            </div>
                                                        @endforelse
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
                                                <div class="free-content bg-white">
                                                    <div class="row g-0">
                                                        @forelse($headerFreeAlbums as $album)
                                                            <div class="col-6 col-album py-2">
                                                                <a href="{{ route('search', ['album' => $album->slug, 'type' => 'free']) }}"
                                                                    class="text-decoration-none"
                                                                    style="display: flex; align-items: center; gap: 8px; padding-left: 25px;">
                                                                    <img src="{{ Storage::url($album->icon ?? $album->image) }}"
                                                                        alt="{{ $album->name }}"
                                                                        style="width: 24px; height: 24px; object-fit: contain;">
                                                                    <span class="text-dark">{{ $album->name }}</span>
                                                                </a>
                                                            </div>
                                                        @empty
                                                            <div class="col-12 p-3 pb-0 text-center text-muted">
                                                                Chưa có album miễn phí
                                                            </div>
                                                        @endforelse
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
                                    @foreach (request()->get('colors', []) as $color)
                                        <input type="hidden" name="colors[]" value="{{ $color }}">
                                    @endforeach
                                    @foreach (request()->get('software', []) as $software)
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
                        <a href="{{ route('get.link') }}"
                            class="action-btn get-link-btn rounded-5 p-1 text-decoration-none">
                            <img src="{{ asset('/images/svg/g.svg') }}" alt="Link">
                            <p class="color-primary fw-semibold text-md pe-3 mb-1">Get Link</p>
                        </a>
                        <a href="{{ route('blog') }}" class="action-btn blog-btn rounded-5 p-1 text-decoration-none">
                            <img src="{{ asset('/images/svg/logo.svg') }}" alt="Blog">
                            <p class="color-primary fw-semibold text-md pe-3 mb-1">Vietfile Blog</p>
                        </a>

                    </div>
                </div>

                <div class="user-section">
                    @auth
                        <div class="user-dropdown">
                            <button class="notification-btn" id="notificationBtn">
                                <img src="{{ asset('/images/svg/notification.svg') }}" alt="Notification">
                                <span class="notification-badge" id="notificationBadge">0</span>
                            </button>

                            <div class="notification-dropdown" id="notificationDropdown">
                                <div class="notification-header">
                                    <h6 class="notification-title">Thông báo xu</h6>
                                    <button class="mark-all-read-btn" id="markAllReadBtn">
                                        <i class="fas fa-check-double me-1"></i>
                                        Đọc tất cả
                                    </button>
                                </div>

                                <div class="notification-list" id="notificationList">
                                    <div class="notification-loading">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        Đang tải...
                                    </div>
                                </div>

                                <div class="notification-footer">
                                    <a href="{{ route('user.coin-history') }}" class="notification-view-all">
                                        <i class="fas fa-history me-1"></i>
                                        Xem tất cả
                                    </a>
                                </div>
                            </div>

                            <button class="user-profile-btn" id="userDropdownBtn">
                                <div class="user-avatar-container">
                                    @if (auth()->user() && auth()->user()->avatar)
                                        <img class="avatar" src="{{ Storage::url(auth()->user()->avatar) }}"
                                            alt="User">
                                    @else
                                        <img class="avatar-default" src="{{ asset('/images/svg/user.svg') }}"
                                            alt="User">
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
                                                <img class="avatar" src="{{ Storage::url(auth()->user()->avatar) }}"
                                                    alt="User">
                                            @else
                                                <img class="avatar-default" src="{{ asset('/images/svg/user.svg') }}"
                                                    alt="User">
                                            @endif
                                        </div>
                                        <div class="user-details">
                                            <h6 class="user-name">{{ auth()->user()->full_name }}</h6>
                                            <p class="user-email">{{ auth()->user()->email }}</p>
                                            <div class="user-type mt-2">
                                                @if (auth()->user()->package_id)
                                                    <img src="{{ asset('/images/svg/gplus.svg') }}" alt="G+"
                                                        class="{{ auth()->user()->package ? auth()->user()->package->getPlanFilter() : 'filter-primary-color-3' }}">
                                                    <span
                                                        class="text-md {{ auth()->user()->package ? auth()->user()->package->getPlanColor() : 'color-primary-3' }}">
                                                        {{ auth()->user()->package->getPlanName() }}</span>
                                                @else
                                                    <img src="{{ asset('/images/svg/gplus.svg') }}" alt="G+"
                                                        class="filter-primary-color-3">
                                                    <span class="text-md color-primary-3">TK THƯỜNG</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    @if (auth()->user()->package_id)
                                        <div class="download-count">
                                            <p class="text-md color-primary-4 mb-0">Số dư:</p>
                                            <div class="count-display">
                                                <span
                                                    class="count-number text-6xl {{ auth()->user()->package ? auth()->user()->package->getPlanColor() : 'color-primary-12' }}">{{ auth()->user()->coins }}</span>
                                                <span class="count-text text-md color-primary-4">XU</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="download-count">
                                            <p class="text-md color-primary-4 mb-0">Lượt tải file của bạn:</p>
                                            <div class="count-display">
                                                <span
                                                    class="count-number text-6xl {{ auth()->user()->package ? auth()->user()->package->getPlanColor() : 'color-primary-3' }}">{{ auth()->user()->free_downloads }}</span>
                                                <span class="count-text text-md color-primary-4">Lượt</span>
                                            </div>
                                        </div>
                                    @endif

                                    <hr class="my-3">

                                    <div class="d-flex justify-content-center text-center">
                                        @if (auth()->user()->package_id)
                                            <a href="{{ route('user.payment') }}"
                                                class="text-decoration-none text-white unlimited-btn rounded-5 {{ auth()->user()->package ? auth()->user()->package->getPlanGradient() : 'bg-gradient-to-r from-primary-4 to-primary-5' }}">
                                                NẠP XU
                                            </a>
                                        @else
                                            <a href="{{ route('user.payment') }}"
                                                class="text-decoration-none text-white unlimited-btn rounded-5 {{ auth()->user()->package ? auth()->user()->package->getPlanGradient() : 'bg-gradient-to-r from-primary-4 to-primary-5' }}">
                                                Đăng kí tải không giới hạn
                                            </a>
                                        @endif
                                    </div>

                                    <ul class="user-menu mt-2">
                                        @if (auth()->user()->role == App\Models\User::ROLE_ADMIN)
                                            <li>
                                                <a href="{{ route('admin.dashboard') }}">
                                                    <i class="fas fa-user-shield"></i>
                                                    <span>Quản trị viên</span>
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('user.profile') }}">
                                                <img src="{{ asset('/images/svg/user.svg') }}" alt="User"
                                                    style="filter: contrast(0);">
                                                <span>Thông tin tài khoản</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('user.purchases') }}">
                                                <img src="{{ asset('/images/svg/download.svg') }}" alt="Download">
                                                <span>Lịch sử tải xuống
                                                    ({{ auth()->user()->purchasedSets->count() }})</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('user.favorites') }}">
                                                <img src="{{ asset('/images/svg/whitelist.svg') }}" alt="Whitelist">
                                                <span>Sản phẩm yêu thích ({{ auth()->user()->favorites->count() }})</span>
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

                                {{-- <button class="partner-btn">
                                    Đăng kí làm đối tác Printon
                                </button> --}}
                            </div>
                        </div>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}"
                            class="action-btn login-btn rounded-5 py-2 px-4 text-decoration-none d-none d-sm-flex">
                            <img src="{{ asset('/images/svg/clock.svg') }}" alt="Login">
                            <span class="color-primary fw-semibold text-md mb-1">Đăng nhập</span>
                        </a>
                    @endguest
                </div>

                <div class="d-lg-none">
                    <button class="btn border rounded-circle bg-white" id="mobileMenuToggle"
                        style="width: 45px; height: 45px;">
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
                <a href="{{ route('user.payment') }}"
                    class="text-decoration-none btn btn-danger px-4 py-2 fw-bold text-uppercase">
                    <img src="{{ asset('/images/svg/user.svg') }}" alt="User" class="me-2" width="16"
                        height="16">
                    ĐĂNG KÍ GÓI TẢI
                </a>
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
                        @foreach (request()->get('colors', []) as $color)
                            <input type="hidden" name="colors[]" value="{{ $color }}">
                        @endforeach
                        @foreach (request()->get('software', []) as $software)
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
                                    <div class="vip-content">
                                        <div class="row g-0">
                                            @forelse($headerVipAlbums as $album)
                                                <div class="col-12">
                                                    <a href="{{ route('search', ['album' => $album->slug, 'type' => 'premium']) }}"
                                                        class="text-decoration-none"
                                                        style="display: flex; align-items: center; gap: 8px;">
                                                        <img src="{{ Storage::url($album->icon ?? $album->image) }}"
                                                            alt="{{ $album->name }}"
                                                            style="width: 24px; height: 24px; object-fit: contain;">
                                                        <span>{{ $album->name }}</span>
                                                    </a>
                                                </div>
                                            @empty
                                                <div class="col-12 p-3 pb-0 text-center text-muted">
                                                    Chưa có album VIP
                                                </div>
                                            @endforelse
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
                                    <div class="free-content">
                                        <div class="row g-0">
                                            @forelse($headerFreeAlbums as $album)
                                                <div class="col-12">
                                                    <a href="{{ route('search', ['album' => $album->slug, 'type' => 'free']) }}"
                                                        class="text-decoration-none"
                                                        style="display: flex; align-items: center; gap: 8px;">
                                                        <img src="{{ Storage::url($album->icon ?? $album->image) }}"
                                                            alt="{{ $album->name }}"
                                                            style="width: 24px; height: 24px; object-fit: contain;">
                                                        <span>{{ $album->name }}</span>
                                                    </a>
                                                </div>
                                            @empty
                                                <div class="col-12 p-3 pb-0 text-center text-muted">
                                                    Chưa có album miễn phí
                                                </div>
                                            @endforelse
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
                <p class="color-primary fw-semibold text-md pe-3 mb-1">Get Link</p>
            </a>
            <a href="{{ route('blog') }}" class="action-btn blog-btn rounded-5 p-1 text-decoration-none">
                <img src="{{ asset('/images/svg/logo.svg') }}" alt="Blog">
                <p class="color-primary fw-semibold text-md pe-3 mb-1">Vietfile Blog</p>
            </a>
            @guest
                <a href="{{ route('login') }}" class="action-btn login-btn rounded-5 py-2 px-4 text-decoration-none">
                    <img src="{{ asset('/images/svg/clock.svg') }}" alt="Login">
                    <span class="color-primary fw-semibold text-md mb-1">Đăng nhập</span>
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

            document.addEventListener('click', function(e) {
                if (!categoryDropdownBtn.contains(e.target) && !categoryDropdown.contains(e.target)) {
                    categoryDropdown.classList.remove('active');
                    dropdownArrow.style.transform = 'rotate(0deg)';
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && categoryDropdown.classList.contains('active')) {
                    categoryDropdown.classList.remove('active');
                    dropdownArrow.style.transform = 'rotate(0deg)';
                }
            });

            if (userDropdownBtn && userDropdown) {
                userDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('active');
                });
            }

            document.addEventListener('click', function(e) {
                if (userDropdownBtn && userDropdown && !userDropdownBtn.contains(e.target) && !userDropdown
                    .contains(e.target)) {
                    userDropdown.classList.remove('active');
                }
            });

            @auth
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationList = document.getElementById('notificationList');
            const notificationBadge = document.getElementById('notificationBadge');
            const markAllReadBtn = document.getElementById('markAllReadBtn');

            if (notificationBtn && notificationDropdown) {
                notificationBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('active');

                    if (notificationDropdown.classList.contains('active')) {
                        loadNotifications();
                    }
                });
            }

            document.addEventListener('click', function(e) {
                if (notificationBtn && notificationDropdown && !notificationBtn.contains(e.target) && !
                    notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.remove('active');
                }
            });

            function loadNotifications() {
                if (!notificationList) return;

                fetch('{{ route('user.coin-history.unread') }}')
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        displayNotifications(data.histories);
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        notificationList.innerHTML =
                            '<div class="notification-empty"><i class="fas fa-exclamation-triangle"></i>Không thể tải thông báo</div>';
                    });
            }

            function displayNotifications(histories) {
                if (!notificationList) return;

                if (histories.length === 0) {
                    notificationList.innerHTML =
                        '<div class="notification-empty"><i class="fas fa-bell-slash"></i>Không có thông báo chưa đọc</div>';
                    return;
                }

                let html = '';
                histories.forEach(history => {
                    const iconClass = getNotificationIcon(history.type);
                    const amountClass = history.amount > 0 ? 'positive' : 'negative';

                    html += `
                        <div class="notification-item unread" data-history-id="${history.id}">
                            <div class="notification-icon ${history.type}">
                                <i class="fas ${iconClass}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title-text">${history.reason}</div>
                                ${history.description ? `<div class="notification-desc">${history.description}</div>` : ''}
                                <div class="notification-meta">
                                    <span>${formatDate(history.created_at)}</span>
                                    <span class="notification-amount ${amountClass}">${history.amount > 0 ? '+' : ''}${formatNumber(history.amount)} xu</span>
                                </div>
                            </div>
                        </div>
                    `;
                });

                notificationList.innerHTML = html;

                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const historyId = this.dataset.historyId;
                        markAsRead(historyId);
                    });
                });
            }

            function markAsRead(historyId) {
                fetch('{{ route('user.coin-history.mark-read') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            id: historyId
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const item = document.querySelector(`[data-history-id="${historyId}"]`);
                            if (item) {
                                item.remove();
                            }

                            const remainingItems = document.querySelectorAll('.notification-item');
                            if (remainingItems.length === 0 && notificationList) {
                                notificationList.innerHTML =
                                    '<div class="notification-empty"><i class="fas fa-bell-slash"></i>Không có thông báo chưa đọc</div>';
                            }

                            updateNotificationCount();
                        }
                    })
                    .catch(error => console.error('Error marking as read:', error));
            }

            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function(e) {
                    e.stopPropagation();

                    fetch('{{ route('user.coin-history.mark-read') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && notificationList) {
                                notificationList.innerHTML =
                                    '<div class="notification-empty"><i class="fas fa-bell-slash"></i>Không có thông báo chưa đọc</div>';
                                updateNotificationCount();
                            }
                        })
                        .catch(error => console.error('Error marking all as read:', error));
                });
            }

            function updateNotificationCount() {
                if (!notificationBadge) return;

                fetch('{{ route('user.coin-history.unread-count') }}')
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        const count = data.count;
                        if (count > 0) {
                            notificationBadge.textContent = count;
                            notificationBadge.style.display = 'flex';
                        } else {
                            notificationBadge.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error updating notification count:', error));
            }

            function getNotificationIcon(type) {
                switch (type) {
                    case 'payment':
                        return 'fa-credit-card';
                    case 'purchase':
                        return 'fa-shopping-cart';
                    case 'manual':
                        return 'fa-user-cog';
                    case 'monthly_bonus':
                        return 'fa-gift';
                    default:
                        return 'fa-coins';
                }
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                const minutes = Math.floor(diff / 60000);
                const hours = Math.floor(diff / 3600000);
                const days = Math.floor(diff / 86400000);

                if (minutes < 1) return 'Vừa xong';
                if (minutes < 60) return `${minutes} phút trước`;
                if (hours < 24) return `${hours} giờ trước`;
                if (days < 7) return `${days} ngày trước`;

                return date.toLocaleDateString('vi-VN');
            }

            function formatNumber(num) {
                return new Intl.NumberFormat('vi-VN').format(num);
            }

            updateNotificationCount();
        @endauth

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
