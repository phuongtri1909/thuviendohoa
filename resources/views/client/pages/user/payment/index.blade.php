@extends('client.layouts.information')

@section('info_title', 'Đăng ký gói')
@section('info_description', 'Đăng ký gói trên ' . request()->getHost())
@section('info_keyword', 'Đăng ký gói, thanh toán, Casso, ' . request()->getHost())
@section('info_section_title', 'Chọn gói tài khoản VIP để tải file')
@section('info_section_desc',
    'Bạn thân mến! Việc đăng kí VIP, bạn sẽ nhận được các gói XU tương ứng và kích hoạt quyền tải
    không giới hạn, đồng thời nhận được hỗ trợ chỉnh sửa file từ đội ngũ. Với nền tảng chia sẻ file thiết kế,
    liên tục cải tiến nhằm mang đến cho bạn trải nghiệm tốt hơn.')

@push('styles')
    <style>
            .package-grid {
                display: flex;
                flex-direction: column;
                gap: 10px;
                width: 100%;
                height: 100%;
                max-width: 100%;
                max-height: 100%;
            }

            .package-item {
                flex: 1;
                width: 100%;
                height: calc(25% - 3px);
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 0;
                position: relative;
                overflow: hidden;
                border-radius: 6px;
            }

            .package-item:hover {
                transform: translateY(-5px);
            }

            .package-img {
                width: 100%;
                height: 100%;
            object-fit: contain;
                border-radius: 6px;
                transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
                max-width: 100%;
                max-height: 100%;
                position: relative;
                z-index: 2;
            }

            .package-hover-img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: contain;
                border-radius: 6px;
                max-width: 100%;
                max-height: 100%;
                opacity: 0;
                transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1;
            }

            .package-item:hover .package-img {
                opacity: 0;
            }

            .package-item:hover .package-hover-img {
                opacity: 1;
            }

            .package-content {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                padding: 25px;
                box-sizing: border-box;
                z-index: 3;
            }

            .package-bg-content {
                opacity: 1;
            }

            .package-hover-content {
                opacity: 0;
            }

            .package-item:hover .package-bg-content {
                opacity: 0;
            }

            .package-item:hover .package-hover-content {
                opacity: 1;
            }

            .package-left {
                flex: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 8px;
                text-align: center;
                transition: transform 0.3s ease;
            }

            .package-item:hover .package-left {
                transform: translateY(-10px);
            }

            .package-right {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 8px;
            position: relative;
                text-align: center;
            }

            .package-title {
                font-size: clamp(17px, 1.2vw, 12px);
                font-weight: bold;
                color: white;
                margin-bottom: 2px;
                line-height: 1.2;
            }

            .package-price {
                font-size: clamp(35px, 1.5vw, 14px);
                font-weight: bold;
                color: white;
                line-height: 1.2;
            }

            .package-register {
                font-size: clamp(35px, 2vw, 16px);
                font-weight: bold;
                color: white;
                text-align: center;
                width: 100%;
                text-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
                margin-top: 10px;
            }

            .package-feature {
                font-size: clamp(13px, 0.8vw, 8px);
                color: #666;
                margin-bottom: 2px;
                line-height: 2;
                position: relative;
            }

            .package-feature:nth-child(1)::after,
            .package-feature:nth-child(2)::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 0;
                width: 100%;
                height: 1px;
                background: repeating-linear-gradient(to right,
                        #ccc 0px,
                        #ccc 2px,
                        transparent 2px,
                        transparent 4px);
            }

            .package-feature-colored-1 {
                color: #CD8053;
                font-weight: 500;
            }

            .package-feature-colored-2 {
                color: #7E7E7E;
                font-weight: 500;
            }

            .package-feature-colored-3 {
                color: #F5C42E;
                font-weight: 500;
            }

            .package-feature-colored-4 {
                color: #BE40E8;
                font-weight: 500;
            }

            .package-hover-img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: contain;
                border-radius: 6px;
            max-width: 100%;
                max-height: 100%;
                opacity: 0;
                transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1;
            }

            .package-item:hover .package-hover-img {
                opacity: 1;
            }

            /* Responsive scaling for different screen sizes */
            @media (max-width: 1350px) {

                .package-title {
                    font-size: clamp(14px, 1.2vw, 12px);
                }

                .package-price {
                    font-size: clamp(31px, 1.5vw, 14px);
                }
            }

            @media (max-width: 1250px) {
                .package-register {
                    font-size: clamp(31px, 2vw, 16px);
                }
            }

            @media (max-width: 1050px) {
                .package-title {
                    font-size: clamp(12px, 1.2vw, 12px);
                }

                .package-price {
                    font-size: clamp(29px, 1.5vw, 14px);
                }

                .package-register {
                    font-size: clamp(27px, 2vw, 16px);
                }
            }

            @media (max-width: 992px) {
                .package-feature {
                    font-size: clamp(13px, 0.8vw, 8px);
                }

                .package-title {
                    font-size: clamp(10px, 1.2vw, 12px);
                }

                .package-price {
                    font-size: clamp(25px, 1.5vw, 14px);
                }

                .package-register {
                    font-size: clamp(25px, 2vw, 16px);
                }
            }

            @media (max-width: 800px) {
                .package-feature {
                    font-size: clamp(12px, 0.8vw, 8px);
                }

                .package-title {
                    font-size: clamp(8px, 1.2vw, 12px);
                }

                .package-price {
                    font-size: clamp(21px, 1.5vw, 14px);
                }

                .package-register {
                    font-size: clamp(35px, 2vw, 16px);
                }
            }

            @media (max-width: 768px) {
                .package-feature {
                    font-size: clamp(11px, 0.8vw, 8px);
                }

                .package-title {
                    font-size: clamp(17px, 1.2vw, 12px);
                }

                .package-price {
                    font-size: clamp(35px, 1.5vw, 14px);
                }

                .package-register {
                    font-size: clamp(30px, 2vw, 16px);
                }
            }

            @media (max-width: 610px) {
                .package-feature {
                    font-size: clamp(10px, 0.8vw, 8px);
                }

                .package-title {
                    font-size: clamp(14px, 1.2vw, 12px);
                }

                .package-price {
                    font-size: clamp(31px, 1.5vw, 14px);
                }

                .package-register {
                    font-size: clamp(28px, 2vw, 16px);
                }
            }

            @media (max-width: 500px) {
                .package-feature {
                    font-size: clamp(7px, 0.8vw, 8px);
                }

                .package-title {
                    font-size: clamp(12px, 1.2vw, 12px);
                }

                .package-price {
                    font-size: clamp(29px, 1.5vw, 14px);
                }

                .package-register {
                    font-size: clamp(23px, 2vw, 16px);
                }
            }

            @media (max-width: 400px) {
                .package-feature {
                    font-size: clamp(7px, 0.8vw, 8px);
                }

                .package-title {
                    font-size: clamp(10px, 1.2vw, 12px);
                }

                .package-price {
                    font-size: clamp(25px, 1.5vw, 14px);
                }
            }

            /* Modal styles */
            .modal-dialog {
                max-width: 900px;
            }

            .modal-content {
                border: none;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                border-radius: 15px;
            }

            .modal-header {
                color: white;
                border-radius: 15px 15px 0 0;
                padding: 20px 25px;
                border: none;
            }

            .modal-header .btn-close {
                filter: brightness(0) invert(1);
            }

            .modal-body {
                padding: 25px;
            }

            .modal-header.package-bronze {
                background: #CD8053;
            }

            .modal-header.package-silver {
                background: #7E7E7E;
            }

            .modal-header.package-gold {
                background: #F5C42E;
            }

            .modal-header.package-platinum {
                background: #BE40E8;
            }

            .modal-header.package-gold,
            .modal-header.package-silver {
                color: #333 !important;
            }

            .modal-header.package-gold .btn-close,
            .modal-header.package-silver .btn-close {
                filter: none;
            }

            .payment-info-card {
                border-radius: 15px;
                padding: 25px;
                color: white;
                margin-bottom: 20px;
            }

            .payment-info-card.package-bronze {
                background: #CD8053;
                box-shadow: 0 8px 20px rgba(205, 127, 50, 0.3);
            }

            .payment-info-card.package-silver {
                background: #7E7E7E;
                box-shadow: 0 8px 20px rgba(192, 192, 192, 0.3);
                color: #333 !important;
            }

            .payment-info-card.package-silver .payment-info-label,
            .payment-info-card.package-silver .payment-info-value-text {
                color: #333 !important;
            }

            .payment-info-card.package-silver .copy-button {
                background: rgba(51, 51, 51, 0.1);
                border-color: rgba(51, 51, 51, 0.2);
                color: #333;
            }

            .payment-info-card.package-silver .copy-button:hover {
                background: rgba(51, 51, 51, 0.2);
            }

            .payment-info-card.package-gold {
                background: #F5C42E;
                box-shadow: 0 8px 20px rgba(255, 215, 0, 0.3);
                color: #333 !important;
            }

            .payment-info-card.package-gold .payment-info-label,
            .payment-info-card.package-gold .payment-info-value-text {
                color: #333 !important;
            }

            .payment-info-card.package-gold .copy-button {
                background: rgba(51, 51, 51, 0.1);
                border-color: rgba(51, 51, 51, 0.2);
                color: #333;
            }

            .payment-info-card.package-gold .copy-button:hover {
                background: rgba(51, 51, 51, 0.2);
            }

            .payment-info-card.package-platinum {
                background: #BE40E8;
                box-shadow: 0 8px 20px rgba(185, 200, 211, 0.3);
            }

            .payment-info-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-top: 15px;
            }

            @media (max-width: 768px) {
                .payment-info-row {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
            }

            .payment-info-item {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                padding: 15px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: all 0.3s ease;
            }

            .payment-info-item:hover {
                background: rgba(255, 255, 255, 0.15);
                transform: translateY(-2px);
            }

            .payment-info-label {
                font-size: 12px;
                opacity: 0.9;
                margin-bottom: 8px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

        .payment-info-value {
                font-size: 16px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: space-between;
                word-break: break-all;
            }

            .payment-info-value-text {
                flex: 1;
            user-select: all;
            cursor: text;
            padding: 3px 5px;
                border-radius: 5px;
            transition: background-color 0.2s;
        }

            .payment-info-value-text:hover {
                background-color: rgba(255, 255, 255, 0.1);
            }

            .copy-button {
                padding: 6px 10px;
                font-size: 12px;
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: white;
                border-radius: 6px;
                margin-left: 10px;
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .copy-button:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.05);
            }

            .payment-qr-section {
                text-align: center;
                margin-top: 20px;
            }

            .payment-qr-code {
                display: inline-block;
                background: white;
                border-radius: 15px;
                padding: 20px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .payment-qr-code img {
                max-height: 250px;
                width: auto;
                border-radius: 10px;
            }

            .alert-gradient {
                background: var(--bg-gradient);
                border: none;
                color: white;
                border-radius: 10px;
            }

            .alert-gradient ul {
            margin-bottom: 0;
        }

            .alert-gradient li {
                margin-bottom: 5px;
            }

            .alert-gradient strong {
                background: rgba(255, 255, 255, 0.2);
                padding: 2px 6px;
                border-radius: 4px;
            }

            /* Success Modal */
            #successModal .modal-header {
                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            }

            #successModal .modal-content {
                border-radius: 15px;
            }

            .success-icon-container {
                animation: scaleIn 0.5s ease-out;
            }

            @keyframes scaleIn {
                0% {
                    transform: scale(0);
                    opacity: 0;
                }

                50% {
                    transform: scale(1.1);
                }

                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .selected-package-display {
                text-align: center;
        }
    </style>
@endpush

@section('info_content')

    <div class="package-grid">
        @foreach ($packages as $index => $package)
            <div class="package-item" data-package-id="{{ $package->id }}" data-package-plan="{{ $package->plan }}">
                <img src="{{ asset('images/d/packages/bg-package.png') }}" alt="{{ $package->name }}" class="package-img">

                <div class="package-content package-bg-content">
                    <div class="package-left">
                        <div class="package-title">{{ strtoupper($package->name) }}</div>
                        <div class="package-price">{{ number_format($package->coins) }} xu</div>
                    </div>
                    <div class="package-divider"></div>
                    <div class="package-right">
                        <div class="package-feature">Sử dụng: <span class="fw-bold">{{ $package->expiry }} tháng</span>
                        </div>
                        <div class="package-feature">Không giới hạn lượt tải</div>
                        <div class="package-feature">Vĩnh viên nhận <span class="fw-bold">+ {{ $package->bonus_coins }}
                                XU </span> mỗi tháng</div>
                    </div>
                </div>

                <img src="{{ asset('images/d/packages/package' . ($index + 1) . '.png') }}" alt="{{ $package->name }} Hover"
                    class="package-hover-img">

                <div class="package-content package-hover-content">
                    <div class="package-left">
                        <div class="package-register">Đăng kí</div>
                                                        </div>
                    <div class="package-divider"></div>
                    <div class="package-right">
                        <div class="package-feature package-feature-colored-{{ $index + 1 }}">Sử dụng: <span
                                class="fw-bold">{{ $package->expiry }} tháng</span></div>
                        <div class="package-feature package-feature-colored-{{ $index + 1 }}">Không giới hạn lượt tải
                                                    </div>
                        <div class="package-feature package-feature-colored-{{ $index + 1 }}">Vĩnh viên nhận <span
                                class="fw-bold">+ {{ $package->bonus_coins }} XU </span> mỗi tháng</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                            </div>


    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-credit-card me-2"></i>Thông tin thanh toán
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                <div class="modal-body" id="paymentModalBody">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Thanh toán thành công
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                <div class="modal-body text-center py-5" id="successModalBody">
                    <div class="success-icon-container mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                                </div>
                    <h3 class="mb-3 fw-bold">Giao dịch thành công!</h3>
                    <p class="text-muted fs-5" id="successMessage"></p>
                    <div class="mt-4">
                        <button type="button" class="btn btn-success btn-lg px-5" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt me-2"></i>Tải lại trang
                                </button>
                            </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@once
    @push('info_scripts')
        <script>
            $(document).ready(function() {
                let currentTransactionCode = null;
                let sseConnection = null;

                const urlParams = new URLSearchParams(window.location.search);
                const packagePlan = urlParams.get('package');
                if (packagePlan) {
                    const $targetPackage = $(`.package-item[data-package-plan="${packagePlan}"]`);

                    if ($targetPackage.length > 0) {
                                setTimeout(() => {
                            $targetPackage.trigger('click');
                            
                            urlParams.delete('package');
                            const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                            window.history.replaceState({}, '', newUrl);
                        }, 500);
                    }
                }

                $('.package-item').on('click', function() {
                    const packageId = $(this).data('package-id');
                    const $packageItem = $(this);

                    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                    modal.show();

                            $.ajax({
                        url: '{{ route('user.payment.store') }}',
                                type: 'POST',
                                data: {
                            package_id: packageId,
                            _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                currentTransactionCode = response.transaction_code;
                                showPaymentInfo(response, $packageItem);
                                    } else {
                                showError(response.message || 'Có lỗi xảy ra khi tạo giao dịch');
                            }
                        },
                        error: function(xhr) {
                                let errorMessage = 'Đã xảy ra lỗi khi xử lý yêu cầu';

                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.errors) {
                                        const errors = xhr.responseJSON.errors;
                                        const firstError = Object.values(errors)[0];
                                        errorMessage = firstError[0] || errorMessage;
                                    } else if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                }

                            showError(errorMessage);
                        }
                    });
                });

                function showPaymentInfo(response, $packageItem) {
                const bankInfo = response.bank_info;
                const transactionCode = response.transaction_code;
                const amount = response.amount;
                const coins = response.coins;
                    const packageName = response.package_name;
                    const expiry = response.expiry;

                    const packageIndex = $('.package-item').index($packageItem);
                    const packageClasses = ['package-bronze', 'package-silver', 'package-gold', 'package-platinum'];
                    const packageClass = packageClasses[packageIndex] || 'package-bronze';
                    const amountFormatted = amount.toLocaleString('vi-VN');

                    $('#paymentModalLabel').parent().removeClass(
                        'package-bronze package-silver package-gold package-platinum').addClass(packageClass);

                    const $clonedPackage = $packageItem.clone();
                    $clonedPackage.find('.package-register').text('Đã chọn');

                    const paymentInfoHtml = `
                        <div class="selected-package-display mb-4">
                            ${$clonedPackage[0].outerHTML}
                        </div>
                        
                        <div class="payment-info-card ${packageClass}">
                            <h5 class="mb-3">
                                    <i class="fas fa-university me-2"></i>Thông tin chuyển khoản
                            </h5>
                            <p class="mb-3 opacity-75">Ngân hàng: <strong>${bankInfo.name} (${bankInfo.code})</strong></p>

                            ${bankInfo.qr_code ? `
                                            <div class="payment-qr-section">
                                                <div class="payment-qr-code">
                                                    <img src="${bankInfo.qr_code}" alt="QR Code" style="max-width: 100%; height: auto;">
                                        </div>
                                                <p class="text-muted mt-3 mb-0">
                                                    <i class="fas fa-qrcode me-1"></i>Quét mã QR để thanh toán nhanh
                                                </p>
                                                <div class="alert alert-info mt-2">
                                                    <small>
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        QR code chứa thông tin: STK, số tiền và nội dung chuyển khoản
                                                    </small>
                                            </div>
                                        </div>
                                        ` : ''}
                                
                        
                            
                            <div class="payment-info-row">
                                <div class="payment-info-item">
                                    <div class="payment-info-label">
                                        <i class="fas fa-credit-card me-1"></i>Số tài khoản
                                        </div>
                                    <div class="payment-info-value">
                                        <span class="payment-info-value-text" tabindex="0" onclick="this.focus();this.select()" onfocus="this.select()">${bankInfo.account_number}</span>
                                        <button type="button" class="copy-button" onclick="copyToClipboardFallback('${bankInfo.account_number}', this)" title="Sao chép">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                
                                <div class="payment-info-item">
                                    <div class="payment-info-label">
                                        <i class="fas fa-user me-1"></i>Chủ tài khoản
                                            </div>
                                    <div class="payment-info-value">
                                        <span class="payment-info-value-text">${bankInfo.account_name}</span>
                                    </div>
                                </div>
                                
                                <div class="payment-info-item">
                                    <div class="payment-info-label">
                                        <i class="fas fa-money-bill-wave me-1"></i>Số tiền
                                            </div>
                                    <div class="payment-info-value">
                                        <span class="payment-info-value-text" tabindex="0" onclick="this.focus();this.select()" onfocus="this.select()">${amountFormatted} VNĐ</span>
                                        <button type="button" class="copy-button" onclick="copyToClipboardFallback('${amount}', this)" title="Sao chép">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                        </div>
                                </div>
                                
                                <div class="payment-info-item">
                                    <div class="payment-info-label">
                                        <i class="fas fa-comment-dots me-1"></i>Nội dung CK
                                    </div>
                                    <div class="payment-info-value">
                                        <span class="payment-info-value-text" tabindex="0" onclick="this.focus();this.select()" onfocus="this.select()">${transactionCode}</span>
                                        <button type="button" class="copy-button" onclick="copyToClipboardFallback('${transactionCode}', this)" title="Sao chép">
                                                    <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                                </div>
                                
                                <div class="alert alert-gradient mt-3">
                            <h6 class="mb-2">
                                <i class="fas fa-exclamation-circle me-2"></i>Lưu ý quan trọng
                            </h6>
                            <ul class="small mb-0 ps-3">
                                <li>Nội dung chuyển khoản: <strong>${transactionCode}</strong></li>
                                <li>Số tiền chính xác: <strong>${amountFormatted} VNĐ</strong></li>
                                <li>Xu tự động cộng trong <strong>1-5 phút</strong> sau khi chuyển khoản</li>
                                <li>Liên hệ hỗ trợ nếu chưa nhận xu sau 10 phút</li>
                                    </ul>
                    </div>
                `;

                    $('#paymentModalBody').html(paymentInfoHtml);
                }

                window.copyToClipboardFallback = function(text, button) {
                    const $button = $(button);
                    const originalHtml = $button.html();

                    $button.html('<i class="fas fa-spinner fa-spin"></i>');

                    const tempElement = document.createElement('div');
                    tempElement.style.position = 'fixed';
                    tempElement.style.left = '-9999px';
                    tempElement.style.top = '-9999px';
                    tempElement.style.width = '1px';
                    tempElement.style.height = '1px';
                    tempElement.style.opacity = '0';
                    tempElement.textContent = text;

                    document.body.appendChild(tempElement);

                    const range = document.createRange();
                    range.selectNodeContents(tempElement);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);

                    try {
                    const successful = document.execCommand('copy');
                        document.body.removeChild(tempElement);
                        selection.removeAllRanges();

                    if (successful) {
                            $button.html('<i class="fas fa-check"></i>');
                            setTimeout(() => $button.html(originalHtml), 1500);
                    } else {
                            $button.html('<i class="fas fa-times"></i>');
                            setTimeout(() => $button.html(originalHtml), 1500);
                    }
                } catch (err) {
                        document.body.removeChild(tempElement);
                        selection.removeAllRanges();
                        $button.html('<i class="fas fa-times"></i>');
                        setTimeout(() => $button.html(originalHtml), 1500);
                    }
                };


                function showError(message) {
                    $('#paymentModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>${message}
                        </div>
                    `);
                }

            function startSSEConnection(transactionCode) {

                if (sseConnection) {
                    sseConnection.close();
                }

                    const sseUrl = '{{ route('user.payment.sse') }}?transaction_code=' + encodeURIComponent(
                        transactionCode);
                sseConnection = new EventSource(sseUrl);

                sseConnection.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);

                        if (data.type === 'close') {
                            sseConnection.close();
                            return;
                        }

                            if (data.type === 'payment' && data.status === 'success') {
                                if (sseConnection) {
                                    sseConnection.close();
                                    sseConnection = null;
                                }
                                if (sseCheckInterval) {
                                    clearInterval(sseCheckInterval);
                                    sseCheckInterval = null;
                                }

                                const paymentModal = bootstrap.Modal.getInstance(document.getElementById(
                                    'paymentModal'));
                                if (paymentModal) {
                                    paymentModal.hide();
                                }

                                $('#successMessage').text(
                                    `Bạn đã nhận được ${data.coins.toLocaleString('vi-VN')} xu.`);
                                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                                successModal.show();

                            setTimeout(() => {
                                window.location.reload();
                                }, 5000);
                        }
                    } catch (error) {
                        console.error('SSE parsing error:', error);
                    }
                };

                sseConnection.onerror = function(event) {
                    console.error('SSE connection error:', event);
                        sseConnection.close();
                    };
                }

                let sseCheckInterval = null;
                let isFirstCheck = true;

                function startSSECheck() {
                    if (sseCheckInterval) {
                        clearInterval(sseCheckInterval);
                    }

                    if (isFirstCheck) {
                        sseCheckInterval = setTimeout(() => {
                            if (currentTransactionCode && $('#paymentModal').hasClass('show')) {
                                startSSEConnection(currentTransactionCode);
                                isFirstCheck = false;

                                sseCheckInterval = setInterval(() => {
                                    if (currentTransactionCode && $('#paymentModal').hasClass('show')) {
                                        startSSEConnection(currentTransactionCode);
                                    }
                                }, 3000);
                            }
                        }, 5000);
                    } else {
                        // Subsequent checks every 3 seconds
                        sseCheckInterval = setInterval(() => {
                            if (currentTransactionCode && $('#paymentModal').hasClass('show')) {
                                startSSEConnection(currentTransactionCode);
                            }
                        }, 3000);
                    }
                }

                $('#paymentModal').on('shown.bs.modal', function() {
                    if (currentTransactionCode) {
                        isFirstCheck = true;
                        startSSECheck();
                    }
                });

                $('#paymentModal').on('hidden.bs.modal', function() {
                    // Stop SSE and interval when modal is closed
                    if (sseConnection) {
                        sseConnection.close();
                        sseConnection = null;
                    }
                    if (sseCheckInterval) {
                        clearInterval(sseCheckInterval);
                        sseCheckInterval = null;
                    }
                    isFirstCheck = true;
                    currentTransactionCode = null;
                });
            });
        </script>
    @endpush
@endonce
