@props([
    'desktopImage' => 'images/d/desktops/desktop.png',
    'backgroundImage' => 'images/d/desktops/background.png',
    'frameImage' => 'images/d/desktops/khung.png',
    'alt' => 'Desktop',
])

@push('styles')
    @vite('resources/assets/frontend/css/desktop.css')
@endpush

@push('scripts')
    <script>
        function redirectToPayment(packagePlan) {
            window.location.href = '{{ route('user.payment') }}?package=' + packagePlan;
        }

        function togglePackageView() {
            const desktopScreenArea = document.querySelector('.desktop-screen-area');
            const desktopPackageOverlay = document.querySelector('.desktop-package-overlay');
            const desktopPackageContent = document.querySelector('.desktop-package-content');

            desktopPackageOverlay.style.display = 'block';
            desktopPackageContent.style.display = 'flex';

            desktopScreenArea.classList.add('package-view');

            function handleOutsideClick(event) {
                if (!desktopScreenArea.contains(event.target)) {
                    // Thêm animation đóng
                    desktopScreenArea.classList.add('package-closing');
                    desktopScreenArea.classList.remove('package-view');

                    setTimeout(() => {
                        desktopPackageOverlay.style.display = 'none';
                        desktopPackageContent.style.display = 'none';
                        desktopScreenArea.classList.remove('package-closing');
                    }, 500);

                    document.removeEventListener('click', handleOutsideClick);
                }
            }

            setTimeout(() => {
                document.addEventListener('click', handleOutsideClick);
            }, 100);
        }
    </script>
@endpush

<div class="desktop-wrapper">
    <div class="desktop-container">
        <img src="{{ asset($desktopImage) }}" alt="{{ $alt }}" class="desktop-base">
    </div>
    
    <div class="desktop-screen-area">
        <div class="desktop-background"></div>
        <div class="desktop-package-overlay" style="display: none;"></div>

        <div class="desktop-package-content" style="display: none;">
            <div class="package-grid">
                @foreach ($sharedPackages as $index => $package)
                    <!-- {{ strtoupper($package->name) }} -->
                    <div class="package-item" data-package-plan="{{ $package->plan }}"
                        onclick="event.stopPropagation(); redirectToPayment('{{ $package->plan }}')"
                        style="cursor: pointer;">
                        <img src="{{ asset('images/d/packages/bg-package.png') }}" alt="{{ $package->name }}"
                            class="package-img">
                        <div class="package-content package-bg-content">
                            <div class="package-left">
                                <div class="package-title">{{ strtoupper($package->name) }}</div>
                                <div class="package-price">{{ number_format($package->coins) }} xu</div>
                            </div>
                            <div class="package-divider"></div>
                            <div class="package-right">
                                <div class="package-feature">Sử dụng: {{ $package->expiry }} tháng</div>
                                <div class="package-feature">Không giới hạn lượt tải</div>
                                <div class="package-feature">Cập nhật file mới mỗi ngày</div>
                            </div>
                        </div>
                        <img src="{{ asset('images/d/packages/package' . ($index + 1) . '.png') }}"
                            alt="{{ $package->name }} Hover" class="package-hover-img">
                        <div class="package-content package-hover-content">
                            <div class="package-left">
                                <div class="package-register">Đăng kí</div>
                            </div>
                            <div class="package-divider"></div>
                            <div class="package-right">
                                <div class="package-feature package-feature-colored-{{ $index + 1 }}">Sử dụng:
                                    {{ $package->expiry }} tháng</div>
                                <div class="package-feature package-feature-colored-{{ $index + 1 }}">Không giới hạn
                                    lượt tải</div>
                                <div class="package-feature package-feature-colored-{{ $index + 1 }}">Cập nhật file
                                    mới mỗi ngày</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="desktop-frame">
            <div class="desktop-content">

                <div class="desktop-content-header mt-3">
                    @if ($desktopContent && $desktopContent->logo)
                        @if (str_starts_with($desktopContent->logo, 'desktop-content/'))
                            <img src="{{ Storage::url($desktopContent->logo) }}" alt="Desktop logo"
                                class="desktop-content-logo">
                        @else
                            <img src="{{ asset($desktopContent->logo) }}" alt="Desktop logo"
                                class="desktop-content-logo">
                        @endif
                    @else
                        <img src="{{ asset('/images/d/desktops/logo.png') }}" alt="Desktop logo"
                            class="desktop-content-logo">
                    @endif

                    <h5 class="fw-bold mt-2 mt-md-4">
                        {{ $desktopContent->title ?? 'CHỌN GÓI TÀI KHOẢN VIP ĐỂ TẢI FILE' }}</h5>
                    <P class="text-justify m-0">{!! $desktopContent->description ??
                        'Bạn thân mến! Việc <span class="fw-bold">đăng kí VIP</span>, bạn sẽ nhận được các gói XU tương ứng và kích hoạt quyền tải không giới hạn...' !!}</P>
                </div>

                @if ($desktopContent && !empty($desktopContent->features))
                    <div class="row row-desktop-features">
                        @foreach ($desktopContent->features as $feature)
                            <div class="col-3 desktop-feature-item">
                                <div class="desktop-feature-item-icon">
                                    @if (!empty($feature['icon']))
                                        @if (str_starts_with($feature['icon'], 'desktop-content/'))
                                            <img src="{{ Storage::url($feature['icon']) }}"
                                                alt="{{ $feature['title'] ?? '' }}">
                                        @else
                                            <img src="{{ asset($feature['icon']) }}"
                                                alt="{{ $feature['title'] ?? '' }}">
                                        @endif
                                    @endif
                                </div>
                                <p class="fw-bold mb-3">{{ $feature['title'] ?? '' }}</p>
                                <p class="mb-0">{{ $feature['description'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="row row-desktop-features">
                        <div class="col-3 desktop-feature-item">
                            <img src="{{ asset('/images/svg/desktops/big-data.svg') }}" alt="Big Data">
                            <p class="fw-bold">Kho dữ liệu lớn</p>
                            <p>Hơn 5.000GB file tại kho đồ họa</p>
                        </div>
                        <div class="col-3 desktop-feature-item">
                            <img src="{{ asset('/images/svg/desktops/update.svg') }}" alt="Update">
                            <p class="fw-bold">Luôn cập nhật mới</p>
                            <p>Cập nhật hàng ngàn file mới mỗi ngày</p>
                        </div>
                        <div class="col-3 desktop-feature-item">
                            <img src="{{ asset('/images/svg/desktops/dadang.svg') }}" alt="đa dạng">
                            <p class="fw-bold">Đa dang sản phẩm</p>
                            <p>Thiết kế đa dang sản phẩm và chủ đề</p>
                        </div>
                        <div class="col-3 desktop-feature-item">
                            <img src="{{ asset('/images/svg/desktops/high.svg') }}" alt="High">
                            <p class="fw-bold">Chất lượng cao</p>
                            <p>Sở hữu những file chất lượng cao</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <div class="desktop-button-container" onclick="togglePackageView()">
            <svg class="desktop-button" viewBox="0 0 200 70" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="buttonGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#FF8686;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#E93030;stop-opacity:1" />
                    </linearGradient>
                </defs>

                <path
                    d="M186.2 0.7H13.7C8.6 0.7 5.8 0.7 4.0 2.0C3.2 2.5 2.5 3.2 2.0 4.0C0.7 5.8 0.7 8.6 0.7 13.7V23.0C0.7 26.6 0.7 28.4 1.5 30.0C1.8 30.8 2.2 31.5 2.7 32.1C3.8 33.0 5.5 33.6 8.9 35.0L93.7 68.9C95.8 69.7 96.8 70.1 97.8 70.2C98.2 70.2 98.6 70.2 99.0 70.2C100.0 70.1 101.0 69.7 103.1 68.9L191.0 33.7C194.4 32.3 196.1 31.6 197.2 30.4C197.7 29.8 198.1 29.1 198.4 28.3C199.0 27.1 199.0 25.3 199.0 21.6V13.7C199.0 8.6 199.0 5.8 197.7 4.0C197.2 3.2 196.5 2.5 195.7 2.0C194.0 0.7 191.2 0.7 186.2 0.7Z"
                    fill="white" stroke="#8E8E8E" class="button-bg" />

                <text x="100" y="28" text-anchor="middle" font-size="20" font-weight="700" fill="#333"
                    class="button-text">Xem các gói tải</text>

                <g transform="translate(78, 35)" class="button-arrow">
                    <path
                        d="M33.5 3.96675L29.2997 2.14179L23.2478 4.77784L21.4134 5.57391L19.5789 4.77784L13.5271 2.13428L9.32678 3.95924L21.3962 9.22383L33.5 3.96675Z"
                        fill="#FF3F40" />
                    <path
                        d="M43.0488 11.5666L35.5832 8.2101L24.8267 13.0584L21.5662 14.5225L18.3057 13.0584L7.54915 8.19629L0.0835494 11.5528L21.5357 21.2355L43.0488 11.5666Z"
                        fill="#FF3F40" />
                </g>
            </svg>
        </div>
    </div>
</div>
