<div class="image-slider">
    <h6 class="color-primary-12 fw-semibold fs-5 mb-3">{{ $title ?? 'Thiết kế cùng chủ đề' }}</h6>
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
            min-height: 280px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            box-sizing: border-box;
            height: 280px !important;
            min-height: 280px !important;
            max-height: 280px !important;
            width: auto;
        }

        .slide-item img {
            height: 100%;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            object-position: center;
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
                    
                    requestAnimationFrame(() => {
                        this.setSlideSizes();
                        this.updateSlideWidth();
                        this.attachEvents();
                        this.updateButtons();
                    });

                    window.addEventListener('resize', () => {
                        this.setSlideSizes();
                        this.updateSlideWidth();
                        this.updateButtons();
                    });
                }

                setSlideSizes() {
                    const slides = this.wrapper.querySelectorAll('.slide-item');
                    if (slides.length === 0) return;
                    
                    const targetHeight = 280;
                    
                    slides.forEach(slide => {
                        slide.style.setProperty('height', targetHeight + 'px', 'important');
                        slide.style.setProperty('min-height', targetHeight + 'px', 'important');
                        slide.style.setProperty('max-height', targetHeight + 'px', 'important');

                        const img = slide.querySelector('img');
                        if (img) {
                            if (img.complete && img.naturalWidth > 0 && img.naturalHeight > 0) {
                                const aspectRatio = img.naturalWidth / img.naturalHeight;
                                const calculatedWidth = targetHeight * aspectRatio;
                                slide.style.setProperty('width', calculatedWidth + 'px', 'important');
                                slide.style.setProperty('min-width', calculatedWidth + 'px', 'important');
                                slide.style.setProperty('max-width', calculatedWidth + 'px', 'important');
                            } else {
                                img.addEventListener('load', () => {
                                    const aspectRatio = img.naturalWidth / img.naturalHeight;
                                    const calculatedWidth = targetHeight * aspectRatio;
                                    slide.style.setProperty('width', calculatedWidth + 'px', 'important');
                                    slide.style.setProperty('min-width', calculatedWidth + 'px', 'important');
                                    slide.style.setProperty('max-width', calculatedWidth + 'px', 'important');
                                    this.updateSlideWidth();
                                    this.updateButtons();
                                }, { once: true });
                            }
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
