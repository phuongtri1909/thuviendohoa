<!-- Social Media Bar -->
<div class="social-bar">
    @foreach ($socials as $social)
    <a href="{{ $social->url }}"><i class="{{ $social->icon }}"></i></a>
    @endforeach
</div>

<!-- Contribution Section -->
<div class="contribution-section ">
    <div class="container-custom">
        <h3 class="text-md-4">Đóng góp của bạn sẽ giúp tôi hoàn thiện hơn</h3>
        <form id="feedbackForm" class="feedback-form">
            <div class="input-group">
                <input type="text" id="feedbackMessage" class="form-control" placeholder="Góp ý của bạn về trang..." required>
                <button class="btn btn-submit" type="submit">
                    <img src="{{ asset('/images/svg/submit-form.svg') }}" alt="Submit">
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Footer Content -->
<div class="footer-section">
    <div class="container-custom">
        <div class="footer-content">
            <div class="row">
                <!-- About Section -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-4 col-footer">
                    <h5 class="footer-title">Về tôi</h5>
                    <div class="footer-card text-center">
                        @if($footerSetting && $footerSetting->facebook_url)
                            <div class="fb-page" data-href="{{ $footerSetting->facebook_url }}"
                                data-small-header="false" data-adapt-container-width="true" data-hide-cover="false"
                                data-show-facepile="true">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Support Section -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-4 col-footer">
                    <h5 class="footer-title">Hỗ trợ</h5>
                    <div class="contact-info text-center">
                        @if($footerSetting && $footerSetting->support_hotline)
                            <p>Hotline/Zalo: <a class="text-decoration-none color-primary-3" href="tel:{{ preg_replace('/\s+/', '', $footerSetting->support_hotline) }}">{{ $footerSetting->support_hotline }}</a></p>
                        @endif
                        @if($footerSetting && $footerSetting->support_email)
                            <p>Email: <a class="text-decoration-none color-primary-7" href="mailto:{{ $footerSetting->support_email }}">{{ $footerSetting->support_email }}</a></p>
                        @endif
                        @if($footerSetting && $footerSetting->support_fanpage)
                            <p>Fanpage: 
                                @if($footerSetting->support_fanpage_url)
                                    <a class="text-decoration-none color-primary-7" href="{{ $footerSetting->support_fanpage_url }}" target="_blank" rel="noopener">{{ $footerSetting->support_fanpage }}</a>
                                @else
                                    <span class="color-primary-7">{{ $footerSetting->support_fanpage }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Information Section -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-4 col-footer">
                    <h5 class="footer-title">Thông tin</h5>
                    @if(isset($footerPages) && $footerPages->count() > 0)
                        @foreach($footerPages as $page)
                            <a href="{{ route('page.show', $page->slug) }}" class="footer-link">{{ $page->title }}</a>
                        @endforeach
                    @endif
                </div>

                <!-- Partner Section -->
                <div class="col-12 col-sm-6 col-md-12 col-xl-3 mb-4 col-footer text-center">
                    <h5 class="footer-title">Đối tác chính</h5>
                    <div class="row align-items-center">
                        @if($footerSetting && $footerSetting->partners && count($footerSetting->partners) > 0)
                            @foreach($footerSetting->partners as $partner)
                                <div class="col-6 mb-3">
                                    @if(!empty($partner['url']))
                                        <a href="{{ $partner['url'] }}" target="_blank" rel="noopener">
                                            <img class="img-fluid" src="{{ Storage::url($partner['image']) }}" alt="{{ $partner['name'] ?? 'Partner' }}">
                                        </a>
                                    @else
                                        <img class="img-fluid" src="{{ Storage::url($partner['image']) }}" alt="{{ $partner['name'] ?? 'Partner' }}">
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container-custom">
            <div class="row align-items-center">
                <div class="col-12 col-xl-10">
                    <p><strong>{{ config('app.name') }}</strong> - Bản quyền {{ date('Y') }}. Mọi hành vi sao chép
                        mà không
                        được sự cho phép của chúng tôi sẽ bị loại khỏi kết quả Google mà không cần báo trước.</p>
                    <p>Giấy chứng nhận ĐKKD số 0313908625 do Sở KH & ĐT TP.HCM cấp ngày 11/07/2016. Mobile: 0949.003.999
                        -
                        Email: sales@icon-technic.com</p>
                </div>
                <div class="col-12 col-xl-2">
                    <div class="dmca-badge">
                        <img src="{{ asset('images/d/dmca.png') }}" alt="DMCA">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v17.0"
        nonce="random_nonce"></script>
    
    @include('components.sweetalert')
    
    <script>
        $(document).ready(function() {
            let captchaQuestion = '';
            let captchaAnswer = '';
            let submitCount = 0;
            let lastSubmitTime = 0;
            const SPAM_THRESHOLD = 2;
            const SPAM_TIME_WINDOW = 60000;

            function loadCaptcha(callback) {
                $.get('{{ route("feedback.captcha") }}')
                    .done(function(data) {
                        captchaQuestion = data.question;
                        captchaAnswer = data.answer;
                        if (callback) callback();
                    })
                    .fail(function() {
                        showAlert('Lỗi', 'Không thể tải captcha. Vui lòng thử lại.', 'error');
                    });
            }

            function checkSpamAndSubmit() {
                const message = $('#feedbackMessage').val().trim();
                
                const now = Date.now();
                if (lastSubmitTime > 0 && (now - lastSubmitTime) < SPAM_TIME_WINDOW) {
                    submitCount++;
                } else {
                    submitCount = 1;
                }
                lastSubmitTime = now;


                if (submitCount < SPAM_THRESHOLD) {
                    submitFeedback({ message: message, captcha: '' });
                    return;
                }

                showCaptchaModal(message);
            }

            function showCaptchaModal(message) {
                loadCaptcha(function() {
                    Swal.fire({
                        title: 'Mã xác thực',
                        html: `
                            <div class="captcha-modal">
                                <p class="mb-3">Bạn đã gửi quá nhiều góp ý. Vui lòng nhập mã xác thực để tiếp tục:</p>
                                <div class="captcha-question-modal text-center">
                                    <div class="mb-3">
                                        <span id="modalCaptchaQuestion" style="font-size: 18px; font-weight: bold; color: #333;">${captchaQuestion}</span>
                                    </div>
                                    <input type="text" id="modalCaptchaAnswer" class="form-control text-center" placeholder="Nhập kết quả" style="width: 150px; margin: 0 auto; font-size: 16px; font-weight: bold;">
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Xác thực',
                        cancelButtonText: 'Hủy',
                        confirmButtonColor: 'var(--primary-color-3)',
                        cancelButtonColor: '#6c757d',
                        width: '400px',
                        didOpen: function() {
                            $('#modalCaptchaAnswer').focus();
                        },
                        preConfirm: function() {
                            const modalCaptcha = $('#modalCaptchaAnswer').val().trim();
                            
                            
                            if (!modalCaptcha) {
                                Swal.showValidationMessage('Vui lòng nhập mã xác thực.');
                                return false;
                            }

                            if (parseInt(modalCaptcha) !== parseInt(captchaAnswer)) {
                                Swal.showValidationMessage('Mã xác thực không đúng.');
                                return false;
                            }

                            return {
                                message: message,
                                captcha: modalCaptcha
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitFeedback(result.value);
                        }
                    });
                });
            }

            function submitFeedback(formData) {
                $.ajax({
                    url: '{{ route("feedback.store") }}',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Thành công', response.message, 'success');
                            $('#feedbackForm')[0].reset();
                        } else {
                            showAlert('Lỗi', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 429) {
                            showAlert('Cảnh báo', 'Bạn đã gửi quá nhiều góp ý. Vui lòng thử lại sau.', 'warning');
                        } else {
                            const response = xhr.responseJSON;
                            showAlert('Lỗi', response.message || 'Có lỗi xảy ra. Vui lòng thử lại.', 'error');
                        }
                    }
                });
            }

            $('#feedbackForm').on('submit', function(e) {
                e.preventDefault();
                
                const message = $('#feedbackMessage').val().trim();
                if (message.length < 10) {
                    showAlert('Lỗi', 'Vui lòng nhập ít nhất 10 ký tự cho góp ý.', 'error');
                    return;
                }

                checkSpamAndSubmit();
            });
        });
    </script>
    
    @stack('scripts')
    </body>

    </html>
