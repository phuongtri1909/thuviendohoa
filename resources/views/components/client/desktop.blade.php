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

<div class="desktop-container">
    <img src="{{ asset($desktopImage) }}" alt="{{ $alt }}" class="desktop-base">

    <div class="desktop-screen-area">
        <img src="{{ asset($backgroundImage) }}" alt="Screen background" class="desktop-background">
        <img src="{{ asset('images/d/desktops/khung-package.png') }}" alt="Package overlay"
            class="desktop-package-overlay" style="display: none;">
        
        <div class="desktop-package-content" style="display: none;">
            <div class="package-grid">
                <!-- GÓI ĐỒNG - 199k -->
                <div class="package-item" data-package="1">
                    <img src="{{ asset('images/d/packages/bg-package.png') }}" alt="Package 1" class="package-img">
                    <div class="package-content package-bg-content">
                        <div class="package-left">
                            <div class="package-title">GÓI ĐỒNG - 199k</div>
                            <div class="package-price">300 xu</div>
                        </div>
                        <div class="package-divider"></div>
                        <div class="package-right">
                            <div class="package-feature">Sử dụng: 3 tháng</div>
                            <div class="package-feature">Không giới hạn lượt tải</div>
                            <div class="package-feature">Cập nhật file mới mỗi ngày</div>
                        </div>
                    </div>
                    <img src="{{ asset('images/d/packages/package1.png') }}" alt="Package 1 Hover" class="package-hover-img">
                    <div class="package-content package-hover-content">
                        <div class="package-left">
                            <div class="package-register">Đăng kí</div>
                        </div>
                        <div class="package-divider"></div>
                        <div class="package-right">
                            <div class="package-feature package-feature-colored-1">Sử dụng: 3 tháng</div>
                            <div class="package-feature package-feature-colored-1">Không giới hạn lượt tải</div>
                            <div class="package-feature package-feature-colored-1">Cập nhật file mới mỗi ngày</div>
                        </div>
                    </div>
                </div>

                <!-- GÓI BẠC - 499k -->
                <div class="package-item" data-package="2">
                    <img src="{{ asset('images/d/packages/bg-package.png') }}" alt="Package 2" class="package-img">
                    <div class="package-content package-bg-content">
                        <div class="package-left">
                            <div class="package-title">GÓI BẠC - 499k</div>
                            <div class="package-price">800 xu</div>
                        </div>
                        <div class="package-divider"></div>
                        <div class="package-right">
                            <div class="package-feature">Sử dụng: 6 tháng</div>
                            <div class="package-feature">Không giới hạn lượt tải</div>
                            <div class="package-feature">Cập nhật file mới mỗi ngày</div>
                        </div>
                    </div>
                    <img src="{{ asset('images/d/packages/package2.png') }}" alt="Package 2 Hover" class="package-hover-img">
                    <div class="package-content package-hover-content">
                        <div class="package-left">
                            <div class="package-register">Đăng kí</div>
                        </div>
                        <div class="package-divider"></div>
                        <div class="package-right">
                            <div class="package-feature package-feature-colored-2">Sử dụng: 6 tháng</div>
                            <div class="package-feature package-feature-colored-2">Không giới hạn lượt tải</div>
                            <div class="package-feature package-feature-colored-2">Cập nhật file mới mỗi ngày</div>
                        </div>
                    </div>
                </div>

                <!-- GÓI VÀNG - 999k -->
                <div class="package-item" data-package="3">
                    <img src="{{ asset('images/d/packages/bg-package.png') }}" alt="Package 3" class="package-img">
                    <div class="package-content package-bg-content">
                        <div class="package-left">
                            <div class="package-title">GÓI VÀNG - 999k</div>
                            <div class="package-price">1800 xu</div>
                        </div>
                        <div class="package-divider"></div>
                        <div class="package-right">
                            <div class="package-feature">Sử dụng: 9 tháng</div>
                            <div class="package-feature">Không giới hạn lượt tải</div>
                            <div class="package-feature">Cập nhật file mới mỗi ngày</div>
                        </div>
                    </div>
                    <img src="{{ asset('images/d/packages/package3.png') }}" alt="Package 3 Hover" class="package-hover-img">
                    <div class="package-content package-hover-content">
                        <div class="package-left">
                            <div class="package-register">Đăng kí</div>
                        </div>
                        <div class="package-divider"></div>
                        <div class="package-right">
                            <div class="package-feature package-feature-colored-3">Sử dụng: 9 tháng</div>
                            <div class="package-feature package-feature-colored-3">Không giới hạn lượt tải</div>
                            <div class="package-feature package-feature-colored-3">Cập nhật file mới mỗi ngày</div>
                        </div>
                    </div>
                </div>

                <!-- GÓI BẠCH KIM - 1499k -->
                <div class="package-item" data-package="4">
                    <img src="{{ asset('images/d/packages/bg-package.png') }}" alt="Package 4" class="package-img">
                    <div class="package-content package-bg-content">
                        <div class="package-left">
                            <div class="package-title">GÓI BẠCH KIM - 1499k</div>
                            <div class="package-price">3000 xu</div>
                        </div>
                        <div class="package-divider"></div>
                        <div class="package-right">
                            <div class="package-feature">Sử dụng: 12 tháng</div>
                            <div class="package-feature">Không giới hạn lượt tải</div>
                            <div class="package-feature">Cập nhật file mới mỗi ngày</div>
                        </div>
                    </div>
                    <img src="{{ asset('images/d/packages/package4.png') }}" alt="Package 4 Hover" class="package-hover-img">
                    <div class="package-content package-hover-content">
                        <div class="package-left">
                            <div class="package-register">Đăng kí</div>
                        </div>
                        <div class="package-divider"></div>
                        <div class="package-right">
                            <div class="package-feature package-feature-colored-4">Sử dụng: 12 tháng</div>
                            <div class="package-feature package-feature-colored-4">Không giới hạn lượt tải</div>
                            <div class="package-feature package-feature-colored-4">Cập nhật file mới mỗi ngày</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="desktop-frame">
            <img src="{{ asset($frameImage) }}" alt="Desktop frame" class="desktop-frame">

            <div class="desktop-content">
                <div class="text-center">
                    <img src="{{ asset('/images/d/desktops/logo.png') }}" alt="Desktop logo"
                        class="desktop-content-logo">
                    <h5 class="fw-bold mt-2 mt-xl-4">CHỌN GÓI TÀI KHOẢN VIP ĐỂ TẢI FILE</h5>
                    <P class="text-justify">Bạn thân mến! Việc <span class="fw-bold">đăng kí VIP</span>, bạn sẽ nhận
                        được các gói XU tương ứng và kích hoạt quyền tải
                        không giới hạn, đồng thời nhận được hỗ trợ chỉnh sửa file từ đội ngũ Hidesign. Với nền tảng chia
                        sẻ file thiết kế, Hidesign liên tục cải tiến nhằm mang đến cho bạn trải nghiệm tốt hơn với các
                        ưu thế:</P>

                    <div class="row">
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
                </div>
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
