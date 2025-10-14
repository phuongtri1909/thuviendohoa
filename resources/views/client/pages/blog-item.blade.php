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
            <div class="col-12 col-lg-9">

            </div>
            <div class="col-12 col-lg-3 mt-4">
                <x-blog-sidebar />
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
    <script>
        const slides = [{
                title: 'Khi các thương hiệu Việt Nam mừng 80 năm Quốc khánh- Sáng tạo lan tỏa niềm tự hào dân tộc',
                category: 'Kiến thức thú vị',
                date: '02/10/2025',
                views: 232,
                image: '{{ asset('/images/d/dev/blogs/vertical.png') }}',
                contentImage: '{{ asset('/images/d/dev/blogs/blog-content.png') }}',
                description: 'Một năm mới nữa đã đến và chắc hẳn mọi người sẽ tự hỏi xu hướng thiết kế đồ họa trong năm 2020 sẽ như thế nào? Đồ họa luôn là một lĩnh vực quan trọng trong thời đại công nghệ số và là nguồn cảm hứng lớn đối với nhiều người. Vì vậy hãy cùng Printon khám phá những xu hướng mới sẽ lên ngôi trong năm nay nhé!'
            },
            {
                title: 'Xu hướng thiết kế web 2025 - Những điểm sáng mới trong công nghiệp digital',
                category: 'Thiết kế',
                date: '05/10/2025',
                views: 456,
                image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=300&h=400&fit=crop',
                contentImage: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=300&fit=crop',
                description: 'Năm 2025 mang đến những thay đổi lớn trong cách mà các nhà thiết kế tiếp cận công việc của họ. Các xu hướng mới không chỉ tập trung vào thẩm mỹ mà còn chú trọng đến trải nghiệm người dùng.'
            },
            {
                title: 'Tối ưu hóa SEO - Bí quyết để website của bạn lên top Google',
                category: 'SEO',
                date: '10/10/2025',
                views: 521,
                image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=300&h=400&fit=crop',
                contentImage: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=300&fit=crop',
                description: 'SEO không chỉ là về từ khóa. Đó là một quá trình toàn diện để cải thiện khả năng hiển thị của website bạn trên các công cụ tìm kiếm như Google, Bing và Yahoo.'
            }
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

            // Don't reset auto slide on every update, only when needed
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
    </script>
@endpush

@push('styles')
    @vite('resources/assets/frontend/css/styles-blog.css')
    @vite('resources/assets/frontend/css/blog-sidebar.css')
@endpush
