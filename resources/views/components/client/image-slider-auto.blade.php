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
            --slide-height: {{ $height }}px;
            position: relative;
            width: 100%;
            overflow: hidden;
            background: #fff;
            padding: 0; /* parent quyết định padding */
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
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: var(--slide-height);
            width: var(--slide-height);
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            box-sizing: border-box;
        }

        .image-slider-auto .slide-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .image-slider-auto .slide-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transition: transform .25s ease, box-shadow .25s ease;
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
                    this.baseWidth = 0; // width của 1 chu kỳ
                    this.initialCount = 0; // số item ban đầu

                    if (!this.wrapper) return;
                    this.init();
                }

                init() {
                    // Chờ ảnh load xong để đo kích thước chính xác (tránh giật, lệch)
                    this.waitForImages().then(() => {
                        // Double raf để đảm bảo layout stabilized trước khi đo
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
                        this.updateWidths();
                        // Giữ vị trí hiện tại trong 1 chu kỳ sau khi resize
                        const loopWidth = this.baseWidth || (this.trackWidth / 2);
                        if (loopWidth > 0) this.current = ((this.current % loopWidth) + loopWidth) % loopWidth;
                        this.wrapper.style.transform = `translate3d(-${this.current}px,0,0)`;
                    });
                }

                waitForImages() {
                    const imgs = Array.from(this.wrapper.querySelectorAll('img'));
                    if (imgs.length === 0) return Promise.resolve();
                    const promises = imgs.map(img => {
                        if (img.complete) {
                            // Đã cache hoặc đã load
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

                    // Ghi nhận số item ban đầu và baseWidth (tổng width 1 chu kỳ)
                    this.initialCount = items.length;
                    const gap = 15;
                    this.baseWidth = 0;
                    items.forEach((slide, idx) => {
                        this.baseWidth += slide.offsetWidth + (idx < items.length - 1 ? gap : 0);
                    });

                    // Clone đúng 1 lần để có 2 chu kỳ liên tiếp (đủ để loop mượt)
                    items.forEach(n => this.wrapper.appendChild(n.cloneNode(true)));

                    // Nếu tổng width vẫn quá nhỏ so với container, tiếp tục clone thêm chu kỳ
                    let minTrack = (this.container ? this.container.offsetWidth : 0) * 2;
                    while (this.wrapper.scrollWidth < minTrack) {
                        for (let i = 0; i < this.initialCount; i++) {
                            this.wrapper.appendChild(this.wrapper.children[i].cloneNode(true));
                        }
                    }
                }

                updateWidths() {
                    const gap = 15;
                    this.trackWidth = 0;
                    this.wrapper.querySelectorAll('.slide-item').forEach(slide => {
                        this.trackWidth += slide.offsetWidth + gap;
                    });
                    this.trackWidth -= gap;

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


