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
                    <li class="breadcrumb-item color-primary-12 active fw-semibold" aria-current="page">Blog</li>
                </ol>
            </nav>
        </div>
        <div class="mt-4">
            <div class="d-flex align-items-baseline">
                <img class="me-3" src="{{ asset('/images/svg/blogs/blog.svg') }}" alt="Blog">
                <h2 class="fw-semibold color-primary me-1">BLOG</h2>
                <p class="color-primary-12 fw-semibold">TIN NỔI BẬT</p>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-9">
                <div class="blog-featured rounded-4 h-100" id="blogCarousel" style="cursor: pointer;">
                    <div class="image-vertical ">
                        <img class="rounded-4" id="featuredImage" src="{{ asset('/images/d/dev/blogs/vertical.png') }}"
                            alt="Blog Featured">
                    </div>
                    <div class="blog-featured-content">
                        <img class="rounded-4 img-fluid" id="contentImage"
                            src="{{ asset('/images/d/dev/blogs/blog-content.png') }}" alt="Blog content">
                        <h4 class="fw-semibold mt-3" id="blogTitle">Khi các thương hiệu Việt Nam mừng 80 năm Quốc khánh-
                            Sáng tạo lan tỏa niềm tự hào dân tộc</h4>
                        <div class="my-2 text-sm blog-featured-info">
                            <span id="blogCategory">Kiến thức thú vị</span> |
                            <span>
                                <img src="{{ asset('images/svg/blogs/time.svg') }}" alt="">
                                <span id="blogDate">02/10/2025</span>
                            </span>
                            <span>
                                <img src="{{ asset('images/svg/blogs/view.svg') }}" alt="">
                                <span id="blogViews">232</span>
                            </span>
                        </div>
                        <p class="text-justify text-xs-2" id="blogDesc">
                            Một năm mới nữa đã đến và chắc hẳn mọi người sẽ tự hỏi xu hướng thiết kế đồ họa trong năm 2020
                            sẽ như thế nào? Đồ họa luôn là một lĩnh vực quan trọng trong thời đại công nghệ số và là nguồn
                            cảm hứng lớn đối với nhiều người. Vì vậy hãy cùng Printon khám phá những xu hướng mới sẽ lên
                            ngôi trong năm nay nhé!
                        </p>
                        <div class="blog-pagination" id="blogPagination"></div>
                    </div>
                    <!-- Pagination -->
                </div>
            </div>
            <div class="col-12 col-lg-3 mt-4">
                <x-blog-sidebar :categories="$categories" :sidebarSetting="$sidebarSetting" :sidebarBlogs="$sidebarBlogs" />
            </div>
        </div>

        <div class="mt-5 blogs-knowledge">
            <div class="d-flex align-items-baseline title-blog-knowledge">
                <div class="d-flex align-items-baseline">
                    <img class="me-3" src="{{ asset('/images/svg/blogs/blog.svg') }}" alt="Blog">
                    <h2 class="fw-semibold color-primary me-1">BLOG</h2>
                    <p class="color-primary-12 fw-semibold mb-0">KIẾN THỨC THÚ VỊ</p>
                </div>

                <form action="#" method="GET" class="search-box position-relative" id="blogSearchForm">
                    <input type="text" name="q" id="blogSearchInput" class="form-control search-input"
                        placeholder="Tìm kiếm bài viết..">
                    <button type="submit" class="search-btn">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="mt-2 blogs-knowledge-items">
            <div class="row gx-3 gy-2" id="blogListContainer">
                @foreach ($blogs as $blog)
                    @include('components.blog.blog-item', ['blog' => $blog])
                @endforeach
            </div>

            <div class="mt-4" id="blogPaginationContainer">
                {{ $blogs->links('components.paginate') }}
            </div>
        </div>

        @if ($contentImage1 && $contentImage1->image)
            <div class="mt-4 mt-md-5 px-0">
                <x-client.content-image :image-src="str_starts_with($contentImage1->image, 'content-images/')
                    ? Storage::url($contentImage1->image)
                    : asset($contentImage1->image)" image-alt="{{ $contentImage1->name }}"
                    button-text="{{ $contentImage1->button_text ?? '> Xem thêm' }}"
                    position-x="{{ $contentImage1->button_position_x ?? '50%' }}"
                    position-y="{{ $contentImage1->button_position_y ?? '50%' }}" button-class="px-3 py-2"
                    :url="$contentImage1->url" />
            </div>
        @endif
    </div>

    <div class="pt-3 pt-md-5 mt-md-5">
        <x-client.desktop desktop-image="images/d/desktops/desktop.png" background-image="images/d/desktops/background.png"
            frame-image="images/d/desktops/khung.png" alt="Desktop Screenshot" />
    </div>
@endsection

@push('scripts')
    <script>
        const slides = [
            @foreach ($featuredBlogs as $featuredBlog)
                {
                    title: {!! json_encode($featuredBlog->title) !!},
                    category: {!! json_encode($featuredBlog->category->name ?? 'Uncategorized') !!},
                    date: '{{ $featuredBlog->created_at->format('d/m/Y') }}',
                    views: {{ $featuredBlog->views ?? 0 }},
                    image: {!! json_encode(
                        $featuredBlog->image ? asset('storage/' . $featuredBlog->image) : asset('/images/d/dev/blogs/vertical.png'),
                    ) !!},
                    contentImage: {!! json_encode(
                        $featuredBlog->image ? asset('storage/' . $featuredBlog->image) : asset('/images/d/dev/blogs/blog-content.png'),
                    ) !!},
                    description: {!! json_encode(cleanDescription($featuredBlog->subtitle, 300)) !!},
                    slug: '{{ $featuredBlog->slug }}'
                }
                {{ $loop->last ? '' : ',' }}
            @endforeach
        ];

        let currentSlide = 0;
        let autoSlideTimer;
        let isDragging = false;
        let dragStart = 0;

        const carousel = document.getElementById('blogCarousel');
        const pagination = document.getElementById('blogPagination');

        function initPagination() {
            pagination.innerHTML = '';
            slides.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.className = `pagination-dot ${index === currentSlide ? 'active' : ''}`;
                dot.addEventListener('click', () => goToSlide(index));
                pagination.appendChild(dot);
            });
        }

        function updateSlide() {
            const slide = slides[currentSlide];
            document.getElementById('featuredImage').src = slide.image;
            document.getElementById('contentImage').src = slide.contentImage;
            document.getElementById('blogTitle').textContent = slide.title;
            document.getElementById('blogCategory').textContent = slide.category;
            document.getElementById('blogDate').textContent = slide.date;
            document.getElementById('blogViews').textContent = slide.views;
            document.getElementById('blogDesc').textContent = slide.description;

            document.querySelectorAll('.pagination-dot').forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });

            // Update carousel click handler
            carousel.onclick = (e) => {
                if (!isDragging && Math.abs(dragStart - e.clientX) < 10) {
                    window.location.href = '/blog/' + slide.slug;
                }
            };
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlide();
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            updateSlide();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            updateSlide();
        }

        function startAutoSlide() {
            clearInterval(autoSlideTimer);
            autoSlideTimer = setInterval(nextSlide, 5000);
        }

        function resetAutoSlide() {
            clearInterval(autoSlideTimer);
            startAutoSlide();
        }

        carousel.addEventListener('mousedown', (e) => {
            isDragging = true;
            dragStart = e.clientX;
            carousel.classList.add('grabbing');
            clearInterval(autoSlideTimer);
        });

        carousel.addEventListener('mouseup', (e) => {
            if (!isDragging) return;
            isDragging = false;
            carousel.classList.remove('grabbing');

            const dragEnd = e.clientX;
            const diff = dragStart - dragEnd;

            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }

            startAutoSlide();
        });

        carousel.addEventListener('mouseleave', () => {
            if (isDragging) {
                isDragging = false;
                carousel.classList.remove('grabbing');
                startAutoSlide();
            }
        });

        // Touch events for mobile swipe
        let touchStart = 0;
        let touchEnd = 0;

        carousel.addEventListener('touchstart', (e) => {
            touchStart = e.touches[0].clientX;
            clearInterval(autoSlideTimer);
        });

        carousel.addEventListener('touchend', (e) => {
            touchEnd = e.changedTouches[0].clientX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const swipeDistance = touchStart - touchEnd;

            if (Math.abs(swipeDistance) > swipeThreshold) {
                if (swipeDistance > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }

            startAutoSlide();
        }

        // Pause auto slide on hover
        carousel.addEventListener('mouseenter', () => {
            clearInterval(autoSlideTimer);
        });

        carousel.addEventListener('mouseleave', () => {
            if (!isDragging) {
                startAutoSlide();
            }
        });

        initPagination();
        updateSlide();
        startAutoSlide();

        // Blog Search AJAX
        let searchTimeout;
        const searchForm = document.getElementById('blogSearchForm');
        const searchInput = document.getElementById('blogSearchInput');
        const blogListContainer = document.getElementById('blogListContainer');
        const paginationContainer = document.getElementById('blogPaginationContainer');

        // Search on input with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(1);
            }, 500);
        });

        // Prevent form submit
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch(1);
        });

        // AJAX search function
        function performSearch(page = 1) {
            const query = searchInput.value;

            fetch(`{{ route('blog.search') }}?q=${encodeURIComponent(query)}&page=${page}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    blogListContainer.innerHTML = data.html;
                    paginationContainer.innerHTML = data.pagination;

                    // Attach click event to new pagination links
                    attachPaginationEvents();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Attach pagination click events
        function attachPaginationEvents() {
            const paginationLinks = paginationContainer.querySelectorAll('.pagination-item');

            paginationLinks.forEach(link => {
                if (link.tagName === 'A') {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const url = new URL(this.href);
                        const page = url.searchParams.get('page') || 1;

                        performSearch(page);

                        // Scroll to blog list
                        document.querySelector('.blogs-knowledge').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                }
            });
        }

        // Initial pagination events
        attachPaginationEvents();
    </script>
@endpush

@push('styles')
    @vite('resources/assets/frontend/css/styles-blog.css')
    @vite('resources/assets/frontend/css/blog-sidebar.css')
@endpush
