<div class="image-slider">
    <h6 class="color-primary-12 fw-semibold fs-5 mb-0">{{ $title ?? 'Thiết kế cùng chủ đề' }}</h6>
    <div class="slider-container" style="--slide-height: {{ $height ?? 260 }}px;">
        <div class="slider-wrapper" id="{{ $id ?? 'sliderWrapper' }}">
            @foreach ($slides as $slide)
                <div class="slide-item">
                    <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] ?? 'Slide' }}" loading="lazy">
                </div>
            @endforeach
        </div>

        <button class="slider-nav slider-nav-prev" id="{{ $prevId ?? 'prevBtn' }}">
            <img src="{{ asset('images/svg/image-sliders/arrow-left.svg') }}" alt="Arrow Left">
        </button>
        <button class="slider-nav slider-nav-next" id="{{ $nextId ?? 'nextBtn' }}">
            <img src="{{ asset('images/svg/image-sliders/arrow-right.svg') }}" alt="Arrow Right">
        </button>
    </div>
</div>


@push('styles')
    <style>
        .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            background: white;
            padding: 0;
        }

        .slider-wrapper {
            display: flex;
            gap: 0;
            transition: transform 0.5s ease;
            align-items: center;
        }

        .slide-item {
            flex: 0 0 auto;
            position: relative;
            overflow: hidden;
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            box-sizing: border-box;
            max-width: 400px;
            max-height: 400px;
        }

        .slide-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 70px;
            opacity: 0.8;
            background: #FFF;
            border: none;
            border-radius: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            box-shadow: 0 0 2px 0 rgba(0, 0, 0, 0.40);
        }

        .slider-nav:hover {
            opacity: 1;
            box-shadow: 0 0 2px 0 rgba(0, 0, 0, 0.40);
        }

        .slider-nav i {
            font-size: 18px;
            color: #333;
        }

        .slider-nav-prev {
            left: 10px;
        }

        .slider-nav-next {
            right: 10px;
        }

        .slider-nav:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .slider-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .slider-nav {
                width: 35px;
                height: 35px;
            }

            .slider-nav i {
                font-size: 16px;
            }

            .slider-title {
                font-size: 20px;
            }
        }

        @media (max-width: 576px) {
            .slider-nav {
                width: 30px;
                height: 30px;
            }

            .slider-nav i {
                font-size: 14px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            class ImageSlider {
                constructor(wrapperId, prevBtnId, nextBtnId) {
                    this.wrapper = document.getElementById(wrapperId);
                    this.prevBtn = document.getElementById(prevBtnId);
                    this.nextBtn = document.getElementById(nextBtnId);
                    this.currentPosition = 0;
                    this.slideWidth = 0;

                    if (!this.wrapper) return;

                    this.init();
                }

                init() {
                    this.setSlideSizes();
                    this.updateSlideWidth();
                    this.attachEvents();
                    this.updateButtons();

                    window.addEventListener('resize', () => {
                        this.setSlideSizes();
                        this.updateSlideWidth();
                        this.updateButtons();
                    });
                }

                setSlideSizes() {
                    const slides = this.wrapper.querySelectorAll('.slide-item');
                    const containerHeight = this.wrapper.parentElement.offsetHeight || 300;
                    const maxSize = Math.min(containerHeight, 400);
                    
                    slides.forEach(slide => {
                        const img = slide.querySelector('img');
                        if (img && img.complete) {
                            // Set size based on image, maintaining 1:1 aspect ratio
                            // Use max of width/height but cap at maxSize
                            const imgSize = Math.max(img.naturalWidth, img.naturalHeight);
                            const size = Math.min(imgSize, maxSize);
                            slide.style.width = size + 'px';
                            slide.style.height = size + 'px';
                        } else if (img) {
                            // Wait for image to load
                            img.addEventListener('load', () => {
                                const imgSize = Math.max(img.naturalWidth, img.naturalHeight);
                                const size = Math.min(imgSize, maxSize);
                                slide.style.width = size + 'px';
                                slide.style.height = size + 'px';
                                this.updateSlideWidth();
                                this.updateButtons();
                            }, { once: true });
                        }
                    });
                }

                updateSlideWidth() {
                    const slides = this.wrapper.querySelectorAll('.slide-item');
                    const gap = 0;

                    this.slideWidth = 0;
                    slides.forEach(slide => {
                        this.slideWidth += slide.offsetWidth + gap;
                    });
                    if (slides.length > 0) {
                        this.slideWidth -= gap;
                    }

                    this.containerWidth = this.wrapper.parentElement.offsetWidth;
                }

                attachEvents() {
                    this.prevBtn.addEventListener('click', () => this.prev());
                    this.nextBtn.addEventListener('click', () => this.next());
                }

                prev() {
                    // Smooth scroll: scroll 1/3 container width
                    const scrollAmount = this.containerWidth / 3;
                    this.currentPosition = Math.max(0, this.currentPosition - scrollAmount);
                    this.updatePosition();
                }

                next() {
                    // Smooth scroll: scroll 1/3 container width
                    const scrollAmount = this.containerWidth / 3;
                    const maxPosition = this.slideWidth - this.containerWidth;
                    this.currentPosition = Math.min(maxPosition, this.currentPosition + scrollAmount);
                    this.updatePosition();
                }

                updatePosition() {
                    this.wrapper.style.transform = `translateX(-${this.currentPosition}px)`;
                    this.updateButtons();
                }

                updateButtons() {
                    this.prevBtn.disabled = this.currentPosition <= 0;
                    const maxPosition = this.slideWidth - this.containerWidth;
                    this.nextBtn.disabled = this.currentPosition >= maxPosition;
                }
            }

            window.ImageSlider = ImageSlider;
            
            new ImageSlider('{{ $id ?? 'sliderWrapper' }}', '{{ $prevId ?? 'prevBtn' }}',
                '{{ $nextId ?? 'nextBtn' }}');
        });
    </script>
@endpush
