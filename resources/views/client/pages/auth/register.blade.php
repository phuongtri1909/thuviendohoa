@extends('client.layouts.main')
@section('title', 'Tạo tài khoản')
@section('description', 'Tạo tài khoản . ' . config('app.name'))
@section('keywords', 'Tạo tài khoản . ' . config('app.name'))

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

        .avatar-upload {
            cursor: pointer;
        }

        .avatar-preview {
            background: #f8f8f8;
            transition: all 0.3s ease;
        }

        .avatar-preview:hover {
            border-color: #797979 !important;
        }

        .avatar-helper {
            margin-top: 0.5rem;
        }

        .fa-eye {
            cursor: pointer;
            color: #8E8E8E;
            z-index: 10;
        }

        #otpPasswordContainer > .text-center {
            margin-bottom: 1.5rem;
        }

        #otpPasswordContainer p {
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
                    Tham gia cùng chúng tôi<br>
                    <span class="highlight">{{ config('app.name') }}</span>
                </h2>
                <p>Các file ảnh chất lượng đang chờ bạn</p>
            </div>
            <div class="banner-image">
                <img src="{{ asset('images/d/login.jpg') }}" alt="Register Banner">
            </div>
        </div>

        <div class="login-form-section position-relative">
            <button class="close-btn" onclick="window.history.back()">
                <i class="fas fa-times"></i>
            </button>

            <div class="login-header">
                <h4>Đăng ký bằng:</h4>
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

            <form id="registerForm">
                <div class="form-email">
                    <div class="form-group">
                        <label class="ms-3" for="email">Email của bạn</label>
                        <input type="email" class="form-input" name="email" id="email"
                            placeholder="admin@example.com" required>
                    </div>
                </div>

                <div id="otpPasswordContainer" class="overflow-hidden">
                    <!-- OTP inputs will be inserted here via JavaScript -->
                </div>

                <div class="box-button">
                    <button type="submit" class="btn-login" id="btn-send">
                        Tiếp Tục
                    </button>
                </div>

                <div class="signup-link">
                    Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts-main')
    <script>
        function handleInput(input) {
            // Chỉ cho phép nhập số
            input.value = input.value.replace(/[^0-9]/g, '');
            
            if (input.value.length === 1) {
                const next = input.nextElementSibling;
                if (next && next.classList.contains('otp-input')) {
                    next.focus();
                }
            }
        }

        // Xử lý backspace để xóa từ phải sang trái
        $(document).on('keydown', '.otp-input', function(e) {
            if (e.key === 'Backspace' && !$(this).val()) {
                const prev = $(this).prev('.otp-input');
                if (prev.length) {
                    prev.focus();
                    prev.val('');
                }
            }
        });

        // Xử lý khi người dùng nhấn nút gửi mã OTP
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                const emailInput = $('#email');
                const email = emailInput.val();
                const submitButton = $('#btn-send');

                // Xóa thông báo lỗi cũ nếu tồn tại
                const oldInvalidFeedback = emailInput.parent().find('.invalid-feedback');
                emailInput.removeClass('is-invalid');
                if (oldInvalidFeedback.length) {
                    oldInvalidFeedback.remove();
                }

                // Thay đổi nút submit thành trạng thái loading
                submitButton.prop('disabled', true);
                submitButton.html('<span class="loading-spinner"></span> Đang xử lý...');

                $.ajax({
                    url: '{{ route('register.post') }}',
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

                            $('#otpPasswordContainer').html(`
                                <div class="text-center mb-3">
                                    <p class="mb-2">${response.message}</p>
                                    <div class="otp-container justify-content-center d-flex" id="input-otp">
                                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" pattern="[0-9]" inputmode="numeric" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-center mb-4">
                                        <div class="avatar-upload position-relative mx-auto" style="width: 120px;height:120px;">
                                            <input type="file" class="d-none" id="avatarInput" name="avatar" accept="image/*">
                                            <div id="avatarPreview" class="avatar-preview rounded-circle cursor-pointer d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; border: 2px dashed #ccc; overflow: hidden;">
                                                <i class="fas fa-camera fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="avatar-helper">
                                            <small class="text-muted mt-2">Click để chọn ảnh đại diện (không bắt buộc)</small>
                                        </div>
                                    </div>

                                    <div class="form-group position-relative">
                                        <label class="ms-3" for="password">Mật khẩu</label>
                                        <input type="password" class="form-input" name="password" id="password" placeholder="••••••••" required>
                                        <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" id="togglePassword" style="margin-top: 0.75rem;"></i>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="ms-3" for="name">Họ và tên</label>
                                        <input type="text" class="form-input" name="full_name" id="full_name" placeholder="Nguyễn Văn A" required>
                                    </div>
                                </div>
                            `);

                            $('.box-button').html(`
                                <button class="btn-login" type="button" id="submitOtpPassword">Xác nhận</button>
                            `);

                            // Toggle password visibility
                            $(document).on('click', '#togglePassword', function() {
                                const passwordInput = $('#password');
                                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                                passwordInput.attr('type', type);
                                $(this).toggleClass('fa-eye fa-eye-slash');
                            });

                            // Đoạn js xử lý chọn ảnh đại diện
                            const avatarPreview = document.getElementById('avatarPreview');
                            const avatarInput = document.getElementById('avatarInput');

                            if (avatarPreview && avatarInput) {
                                avatarPreview.addEventListener('click', function() {
                                    avatarInput.click();
                                });

                                avatarInput.addEventListener('change', function(e) {
                                    if (e.target.files && e.target.files[0]) {
                                        const reader = new FileReader();
                                        $('.avatar-helper').find('.invalid-feedback').remove();

                                        reader.onload = function(e) {
                                            avatarPreview.innerHTML = `<img src="${e.target.result}" class="w-100 h-100" style="object-fit: cover;">`;
                                            avatarPreview.style.border = 'none';
                                            $('.avatar-helper small').removeClass('d-none');
                                        }

                                        reader.readAsDataURL(e.target.files[0]);
                                    }
                                });
                            }

                            $('#submitOtpPassword').on('click', function() {
                                const submitBtn = $(this);
                                const otpInputs = $('.otp-input');
                                const input_otp = $('#input-otp');
                                const passwordInput = $('#password');
                                const fullNameInput = $('#full_name');
                                const avatarInput = $('#avatarInput')[0];

                                let otp = '';
                                otpInputs.each(function() {
                                    otp += $(this).val();
                                });
                                const formData = new FormData();
                                formData.append('email', email);
                                formData.append('otp', otp);
                                formData.append('password', passwordInput.val());
                                formData.append('full_name', fullNameInput.val());
                                if (avatarInput.files[0]) {
                                    formData.append('avatar', avatarInput.files[0]);
                                }

                                removeInvalidFeedback(passwordInput);
                                input_otp.find('.invalid-otp').remove();
                                removeInvalidFeedback(emailInput);
                                removeInvalidFeedback(fullNameInput);
                                $('.avatar-helper').find('.invalid-feedback').remove();

                                // Disable button và hiển thị loading
                                submitBtn.prop('disabled', true);
                                submitBtn.html('<span class="loading-spinner"></span> Đang xử lý...');

                                $.ajax({
                                    url: '{{ route('register.post') }}',
                                    method: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
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
                                            if (response.message.email) {
                                                response.message.email.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback d-block"></div>').text(error);
                                                    emailInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.otp) {
                                                input_otp.append(`<div class="invalid-otp">${response.message.otp[0]}</div>`);
                                            }
                                            if (response.message.password) {
                                                response.message.password.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback d-block"></div>').text(error);
                                                    passwordInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.full_name) {
                                                response.message.full_name.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback d-block"></div>').text(error);
                                                    fullNameInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.avatar) {
                                                $('.avatar-helper small').addClass('d-none');
                                                response.message.avatar.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback d-block text-center"></div>').text(error);
                                                    $('.avatar-helper').append(invalidFeedback);
                                                });
                                            } else {
                                                $('.avatar-helper small').removeClass('d-none');
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
        });

        function removeInvalidFeedback(input) {
            const oldInvalidFeedback = input.parent().find('.invalid-feedback');
            input.removeClass('is-invalid');
            if (oldInvalidFeedback.length) {
                oldInvalidFeedback.remove();
            }
        }
    </script>
@endpush