@php
    $height = $height ?? 260; // fixed item height
    $speed = $speed ?? 0.5; // px per frame
    $dragEnabled = $dragEnabled ?? true; // enable drag
@endphp

<div class="image-slider-auto" data-speed="{{ $speed }}" data-drag="{{ $dragEnabled ? '1' : '0' }}">
    @if(!empty($title))
        <h6 class="color-primary-12 fw-semibold fs-5 mb-3">{{ $title }}</h6>
    @endif
    <div class="slider-container" style="--slide-height: {{ $height }}px;">
        <div class="slider-wrapper" id="{{ $id ?? 'sliderAutoWrapper' }}">
            @foreach ($slides as $slide)
                <div class="slide-item">
                    <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] ?? 'Slide' }}" loading="lazy">
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
    <style>
        .image-slider-auto .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            background: #fff;
            padding: 0;
            min-height: 280px;
        }

        .image-slider-auto .slider-wrapper {
            display: flex;
            gap: 15px;
            align-items: center;
            will-change: transform;
        }

        .image-slider-auto .slide-item {
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

        .image-slider-auto .slide-item img {
            height: 100%;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            object-position: center;
            display: block;
        }


        /* Cursor state when dragging */
        .image-slider-auto.is-dragging .slider-wrapper { cursor: grabbing; }
        .image-slider-auto .slider-wrapper { cursor: grab; }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            class AutoImageSlider {
                constructor(host, wrapperId) {
                    this.host = host;
                    this.wrapper = document.getElementById(wrapperId);
                    this.container = this.wrapper ? this.wrapper.parentElement : null;
                    this.speed = parseFloat(host.getAttribute('data-speed') || '0.5');
                    this.dragEnabled = host.getAttribute('data-drag') !== '0';

                    this.current = 0;
                    this.rafId = null;
                    this.isDragging = false;
                    this.startX = 0;
                    this.startPos = 0;
                    this.baseWidth = 0;
                    this.initialCount = 0;

                    if (!this.wrapper) return;
                    this.init();
                }

                init() {
                    this.waitForImages().then(() => {
                        this.setSlideSizes();
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                this.buildInfiniteTrack();
                                this.updateWidths();
                                this.attachEvents();
                                this.play();
                            });
                        });
                    });

                    window.addEventListener('resize', () => {
                        this.setSlideSizes();
                        this.updateWidths();
                        const loopWidth = this.baseWidth || (this.trackWidth / 2);
                        if (loopWidth > 0) this.current = ((this.current % loopWidth) + loopWidth) % loopWidth;
                        this.wrapper.style.transform = `translate3d(-${this.current}px,0,0)`;
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
                        
                        // Tính width dựa trên tỉ lệ ảnh
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
                                    this.updateWidths();
                                }, { once: true });
                            }
                        }
                    });
                }

                waitForImages() {
                    const imgs = Array.from(this.wrapper.querySelectorAll('img'));
                    if (imgs.length === 0) return Promise.resolve();
                    const promises = imgs.map(img => {
                        if (img.complete) {
                            if (img.decode) {
                                return img.decode().catch(() => {});
                            }
                            return Promise.resolve();
                        }
                        return new Promise(res => {
                            img.addEventListener('load', () => res(), { once: true });
                            img.addEventListener('error', () => res(), { once: true });
                        });
                    });
                    return Promise.all(promises);
                }

                buildInfiniteTrack() {
                    const items = Array.from(this.wrapper.children);
                    if (items.length === 0) return;

                    this.initialCount = items.length;
                    const gap = 10;
                    this.baseWidth = 0;
                    items.forEach((slide, idx) => {
                        this.baseWidth += slide.offsetWidth + (idx < items.length - 1 ? gap : 0);
                    });

                    items.forEach(n => this.wrapper.appendChild(n.cloneNode(true)));

                    let minTrack = (this.container ? this.container.offsetWidth : 0) * 2;
                    while (this.wrapper.scrollWidth < minTrack) {
                        for (let i = 0; i < this.initialCount; i++) {
                            this.wrapper.appendChild(this.wrapper.children[i].cloneNode(true));
                        }
                    }
                }

                updateWidths() {
                    const gap = 10;
                    this.trackWidth = 0;
                    const slides = this.wrapper.querySelectorAll('.slide-item');
                    slides.forEach(slide => {
                        this.trackWidth += slide.offsetWidth + gap;
                    });
                    if (slides.length > 0) {
                        this.trackWidth -= gap;
                    }

                    // Recompute baseWidth in case of responsive changes
                    const children = Array.from(this.wrapper.children).slice(0, this.initialCount);
                    if (children.length) {
                        let w = 0;
                        children.forEach((slide, idx) => {
                            w += slide.offsetWidth + (idx < children.length - 1 ? gap : 0);
                        });
                        this.baseWidth = w;
                    }
                }

                play() {
                    if (this.rafId) cancelAnimationFrame(this.rafId);
                    const step = () => {
                        if (!this.isDragging) {
                            this.current += this.speed;
                            const loopWidth = this.baseWidth || (this.trackWidth / 2);
                            if (loopWidth > 0) {
                                // modulo dương để tránh nhảy giật
                                this.current = ((this.current % loopWidth) + loopWidth) % loopWidth;
                            }
                            this.wrapper.style.transform = `translate3d(-${this.current}px,0,0)`;
                        }
                        this.rafId = requestAnimationFrame(step);
                    };
                    this.rafId = requestAnimationFrame(step);
                }

                attachEvents() {
                    if (!this.dragEnabled) return;
                    const start = (e) => {
                        const p = e.touches ? e.touches[0] : e;
                        this.isDragging = true;
                        this.startX = p.clientX;
                        this.startPos = this.current;
                        this.host.classList.add('is-dragging');
                    };
                    const move = (e) => {
                        if (!this.isDragging) return;
                        const p = e.touches ? e.touches[0] : e;
                        const delta = p.clientX - this.startX;
                        this.current = this.startPos - delta;
                        const loopWidth = this.baseWidth || (this.trackWidth / 2);
                        if (loopWidth > 0) {
                            if (this.current < 0) this.current = 0;
                            if (this.current > loopWidth) this.current = loopWidth; // giới hạn trong 1 chu kỳ khi kéo
                        }
                        this.wrapper.style.transform = `translate3d(-${this.current}px,0,0)`;
                    };
                    const end = () => {
                        if (!this.isDragging) return;
                        this.isDragging = false;
                        this.host.classList.remove('is-dragging');
                    };

                    this.wrapper.addEventListener('mousedown', start);
                    window.addEventListener('mousemove', move);
                    window.addEventListener('mouseup', end);

                    this.wrapper.addEventListener('touchstart', start, { passive: true });
                    window.addEventListener('touchmove', move, { passive: true });
                    window.addEventListener('touchend', end);
                }
            }

            window.AutoImageSlider = AutoImageSlider;
            
            document.querySelectorAll('.image-slider-auto').forEach((host, idx) => {
                const wrapperId = host.querySelector('.slider-wrapper').id || `sliderAutoWrapper_${idx}`;
                new AutoImageSlider(host, wrapperId);
            });
        });
    </script>
@endpush


