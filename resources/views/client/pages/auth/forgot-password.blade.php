@extends('client.layouts.main')
@section('title', 'Quên mật khẩu')
@section('description', 'Quên mật khẩu . ' . config('app.name'))
@section('keywords', 'Quên mật khẩu . ' . config('app.name'))

@push('styles-main')
    <style>
        .otp-container {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #F8F8F8;
        }

        .otp-input:focus {
            outline: none;
            border-color: #797979;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .invalid-otp {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .fa-eye {
            cursor: pointer;
            color: #8E8E8E;
            z-index: 10;
        }

        #otpContainer > .text-center,
        #passwordContainer > .text-center {
            margin-bottom: 1.5rem;
        }

        #otpContainer p,
        #passwordContainer p {
            color: #333;
            font-size: 0.95rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spinner 0.6s linear infinite;
        }

        @keyframes spinner {
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@section('content-main')
    <div class="login-wrapper rounded-5">
        <div class="login-banner">
            <div class="banner-content">
                <h2>
                    Khôi phục tài khoản<br>
                    <span class="highlight">{{ config('app.name') }}</span>
                </h2>
                <p>Đừng lo, chúng tôi sẽ giúp bạn lấy lại mật khẩu</p>
            </div>
            <div class="banner-image">
                <img src="{{ asset('images/d/login.jpg') }}" alt="Forgot Password Banner">
            </div>
        </div>

        <div class="login-form-section position-relative">
            <button class="close-btn" onclick="window.history.back()">
                <i class="fas fa-times"></i>
            </button>

            <div class="login-header">
                <h4>Quên mật khẩu?</h4>
            </div>

            <div class="social-login">
                <a href="{{ route('login.google') }}" class="social-btn google text-decoration-none">
                    <i class="fab fa-google"></i>
                </a>
                <button class="social-btn facebook">
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button class="social-btn twitter">
                    <i class="fab fa-twitter"></i>
                </button>
            </div>

            <div class="divider">
                <span>Hoặc</span>
            </div>

            <form id="forgotForm">
                <div class="form-email">
                    <div class="form-group">
                        <label class="ms-3" for="email">Email của bạn</label>
                        <input type="email" class="form-input" name="email" id="email"
                            placeholder="admin@example.com" required>
                    </div>
                </div>

                <div id="otpContainer" class="overflow-hidden">
                    <!-- OTP inputs will be inserted here via JavaScript -->
                </div>

                <div id="passwordContainer" class="overflow-hidden">
                    <!-- Password input will be inserted here via JavaScript -->
                </div>

                <div class="box-button">
                    <button type="submit" class="btn-login" id="btn-send">
                        Tiếp Tục
                    </button>
                </div>

                <div class="signup-link">
                    Bạn đã nhớ mật khẩu? <a href="{{ route('login') }}">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts-main')
    <script>
        function handleInput(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
            
            if (input.value.length === 1) {
                const next = input.nextElementSibling;
                if (next && next.classList.contains('otp-input')) {
                    next.focus();
                }
            }
        }

        $(document).on('keydown', '.otp-input', function(e) {
            if (e.key === 'Backspace' && !$(this).val()) {
                const prev = $(this).prev('.otp-input');
                if (prev.length) {
                    prev.focus();
                    prev.val('');
                }
            }
        });

        $(document).ready(function() {
            $('#forgotForm').on('submit', function(e) {
                e.preventDefault();
                const emailInput = $('#email');
                const email = emailInput.val();
                const submitButton = $('#btn-send');

                const oldInvalidFeedback = emailInput.parent().find('.invalid-feedback');
                emailInput.removeClass('is-invalid');
                if (oldInvalidFeedback.length) {
                    oldInvalidFeedback.remove();
                }

                submitButton.prop('disabled', true);
                submitButton.html('<span class="loading-spinner"></span> Đang xử lý...');

                $.ajax({
                    url: '{{ route('forgot.password') }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        email: email
                    }),
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            submitButton.remove();
                            $('.form-email').remove();

                            $('#otpContainer').html(`
                                <div class="text-center mb-3">
                                    <p class="mb-2">${response.message}</p>
                                    <div class="otp-container justify-content-center d-flex flex-column" id="input-otp">
                                        <div>
                                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                        </div>
                                    </div>
                                </div>
                            `);

                            $('.box-button').html(`
                                <button class="btn-login" type="button" id="submitOtp">Tiếp tục</button>
                            `);

                            $('#submitOtp').on('click', function() {
                                const submitBtn = $(this);
                                const otpInputs = $('.otp-input');
                                const input_otp = $('#input-otp');

                                let otp = '';
                                otpInputs.each(function() {
                                    otp += $(this).val();
                                });

                                input_otp.find('.invalid-otp').remove();
                                removeInvalidFeedback(emailInput);

                                // Disable button và hiển thị loading
                                submitBtn.prop('disabled', true);
                                submitBtn.html('<span class="loading-spinner"></span> Đang xử lý...');

                                $.ajax({
                                    url: '{{ route('forgot.password') }}',
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    data: JSON.stringify({
                                        email: email,
                                        otp: otp,
                                    }),
                                    success: function(response) {
                                        if (response.status === 'success') {
                                            showToast(response.message, 'success');
                                            $('#submitOtp').remove();
                                            $('#otpContainer').remove();

                                            $('#passwordContainer').html(`
                                                <div class="text-center mb-3">
                                                    <p class="mb-2">${response.message}</p>
                                                </div>
                                                <div class="form-group position-relative">
                                                    <label class="ms-3" for="password">Mật khẩu mới</label>
                                                    <input type="password" class="form-input" name="password" id="password" placeholder="••••••••" required>
                                                    <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" id="togglePassword" style="margin-top: 0.75rem;"></i>
                                                </div>
                                            `);

                                            $('.box-button').html(`
                                                <button class="btn-login" type="button" id="submitPassword">Xác nhận</button>
                                            `);

                                            // Toggle password visibility
                                            $(document).on('click', '#togglePassword', function() {
                                                const passwordInput = $('#password');
                                                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                                                passwordInput.attr('type', type);
                                                $(this).toggleClass('fa-eye fa-eye-slash');
                                            });

                                            $('#submitPassword').on('click', function() {
                                                const submitBtn = $(this);
                                                const passwordInput = $('#password');
                                                const password = passwordInput.val();

                                                removeInvalidFeedback(passwordInput);

                                                // Disable button và hiển thị loading
                                                submitBtn.prop('disabled', true);
                                                submitBtn.html('<span class="loading-spinner"></span> Đang xử lý...');

                                                $.ajax({
                                                    url: '{{ route('forgot.password') }}',
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    data: JSON.stringify({
                                                        email: email,
                                                        otp: otp,
                                                        password: password
                                                    }),
                                                    success: function(response) {
                                                        if (response.status === 'success') {
                                                            showToast(response.message, 'success');
                                                            saveToast(response.message, response.status);
                                                            
                                                            setTimeout(function() {
                                                                window.location.href = response.url;
                                                            }, 500);
                                                        } else {
                                                            showToast(response.message, 'error');
                                                            submitBtn.prop('disabled', false);
                                                            submitBtn.html('Xác nhận');
                                                        }
                                                    },
                                                    error: function(xhr) {
                                                        const response = xhr.responseJSON;

                                                        if (response && response.status === 'error') {
                                                            if (response.message.password) {
                                                                response.message.password.forEach(error => {
                                                                    const invalidFeedback = $('<div class="invalid-feedback d-block"></div>').text(error);
                                                                    passwordInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                                });
                                                            }
                                                        } else {
                                                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                                                        }
                                                        
                                                        // Enable lại button khi có lỗi
                                                        submitBtn.prop('disabled', false);
                                                        submitBtn.html('Xác nhận');
                                                    }
                                                });
                                            });
                                        } else {
                                            showToast(response.message, 'error');
                                            submitBtn.prop('disabled', false);
                                            submitBtn.html('Tiếp tục');
                                        }
                                    },
                                    error: function(xhr) {
                                        const response = xhr.responseJSON;

                                        if (response && response.status === 'error') {
                                            if (response.message.email) {
                                                response.message.email.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback d-block"></div>').text(error);
                                                    emailInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.otp) {
                                                input_otp.append(`<div class="invalid-otp">${response.message.otp[0]}</div>`);
                                            }
                                        } else {
                                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                                        }
                                        
                                        // Enable lại button khi có lỗi
                                        submitBtn.prop('disabled', false);
                                        submitBtn.html('Tiếp tục');
                                    }
                                });
                            });
                        } else {
                            showToast(response.message, 'error');
                            submitButton.prop('disabled', false);
                            submitButton.html('Tiếp tục');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;

                        if (response && response.message && response.message.email) {
                            response.message.email.forEach(error => {
                                const invalidFeedback = $('<div class="invalid-feedback d-block"></div>').text(error);
                                emailInput.addClass('is-invalid').parent().append(invalidFeedback);
                            });
                        } else {
                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                        }
                        submitButton.prop('disabled', false);
                        submitButton.html('Tiếp tục');
                    }
                });
            });

            // Helper function to remove invalid feedback
            function removeInvalidFeedback(input) {
                const oldInvalidFeedback = input.parent().find('.invalid-feedback');
                input.removeClass('is-invalid');
                if (oldInvalidFeedback.length) {
                    oldInvalidFeedback.remove();
                }
            }
        });
    </script>
@endpush