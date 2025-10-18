@extends('client.layouts.information')

@section('info_title', 'Thông tin cá nhân')
@section('info_description', 'Thông tin cá nhân của bạn trên ' . request()->getHost())
@section('info_keyword', 'Thông tin cá nhân, thông tin tài khoản, ' . request()->getHost())
@section('info_section_title', 'Thông tin người dùng')
@section('info_section_desc', 'Quản lý thông tin cá nhân của bạn')

@section('info_content')
    <div class="row">
        <div class="col-12 col-md-4">
            <div class="text-center">
                <div class="profile-avatar-edit" id="avatar">
                    @if (!empty($user->avatar))
                        <img id="avatarImage" class="profile-avatar" src="{{ Storage::url($user->avatar) }}" alt="Avatar">
                    @else
                        <div class="profile-avatar d-flex align-items-center justify-content-center bg-light">
                            <i class="fa-solid fa-user fa-2x" id="defaultIcon"></i>
                        </div>
                    @endif
                    <div class="avatar-edit-overlay">
                        <i class="fas fa-camera me-1"></i> Cập nhật
                    </div>
                </div>
                <input type="file" id="avatarInput" style="display: none;" accept="image/*">

                <div class="mt-3">
                    <h5 class="mb-1">{{ $user->full_name }}</h5>
                    <div class="text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i> Tham gia từ: {{ $user->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 mt-3 mt-md-0">
            <div class="profile-info-card">
                <div class="profile-info-item">
                    <div class="profile-info-label">
                        <i class="fas fa-fingerprint"></i> ID
                    </div>
                    <div class="profile-info-value">
                        {{ $user->id }}
                    </div>
                </div>

                <div class="profile-info-item">
                    <div class="profile-info-label">
                        <i class="fas fa-user"></i> <span class="d-none d-sm-inline">Họ và tên</span>
                    </div>
                    <div class="profile-info-value d-flex align-items-center">
                        <span class="me-2">{{ $user->full_name ?: 'Chưa cập nhật' }}</span>
                        <button class="btn btn-sm profile-edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-type="name">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>

                <div class="profile-info-item">
                    <div class="profile-info-label">
                        <i class="fas fa-envelope"></i> <span class="d-none d-sm-inline">Email</span>
                    </div>
                    <div class="profile-info-value">
                        {{ $user->email }}
                    </div>
                </div>

                <div class="profile-info-item">
                    <div class="profile-info-label">
                        <i class="fas fa-lock"></i> <span class="d-none d-sm-inline">Mật khẩu</span>
                    </div>
                    <div class="profile-info-value d-flex align-items-center">
                        <span class="me-2">••••••••</span>
                        <button class="btn btn-sm profile-edit-btn" data-bs-toggle="modal" data-bs-target="#otpPWModal">
                            <i class="fas fa-key"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Chỉnh sửa thông tin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('user.update.name') }}" method="post">
                        @csrf
                        <div class="mb-3" id="formContent">
                            <!-- Nội dung sẽ được cập nhật dựa trên loại dữ liệu được chọn -->
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-outline-success click-scroll"
                                id="saveChanges">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="otpPWModal" tabindex="-1" aria-labelledby="otpPWModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpPWModalLabel">Xác thực OTP để đổi mật khẩu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="otpPWForm">
                        @csrf
                        <div class="mb-3 d-flex flex-column align-items-center" id="formOTPPWContent">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="text-end box-button-update">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-outline-success" id="btn-send-otpPW">Tiếp tục</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            $('#avatar').on('click', function() {
                $('#avatarInput').click();
            });

            // Handle name update
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('user.update.name') }}",
                    type: 'POST',
                    data: {
                        name: formData.get('name'),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        showToast(response.message, 'error');
                    }
                });
            });

            // Handle password update
            let isPasswordStep = false;
            let verifiedOTP = '';
            $('#otpPWForm').on('submit', function(e) {
                e.preventDefault();
                const otpInputs = document.querySelectorAll('#input-otp-pw .otp-digit');
                const otp = Array.from(otpInputs).map(input => input.value).join('');

                if (!isPasswordStep) {
                   
                    if (otp.length !== 6) {
                        showToast('Vui lòng nhập đầy đủ 6 số OTP', 'error');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('user.update.password') }}",
                        type: 'POST',
                        data: {
                            otp: otp,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success' && response.verified) {
                                verifiedOTP = otp;
                                const formContent = $('#formOTPPWContent');
                                formContent.html(`
                                    <div class="mb-3">
                                        <label class="form-label">Mật khẩu mới</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Xác nhận mật khẩu</label>
                                        <input type="password" class="form-control" name="password_confirmation" required>
                                    </div>
                                `);
                                $('#btn-send-otpPW').text('Cập nhật mật khẩu');
                                isPasswordStep = true;
                                showToast('Xác thực OTP thành công! Vui lòng nhập mật khẩu mới.', 'success');
                            } else {
                                showToast(response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showToast(response.message, 'error');
                        }
                    });
                } else {
                    // Second step - update password
                    const password = $('input[name="password"]').val();
                    const passwordConfirmation = $('input[name="password_confirmation"]').val();

                    if (!password || !passwordConfirmation) {
                        showToast('Vui lòng nhập đầy đủ mật khẩu và xác nhận mật khẩu', 'error');
                        return;
                    }

                    if (password !== passwordConfirmation) {
                        showToast('Mật khẩu xác nhận không khớp', 'error');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('user.update.password') }}",
                        type: 'POST',
                        data: {
                            otp: verifiedOTP,
                            password: password,
                            password_confirmation: passwordConfirmation,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                showToast(response.message, 'success');
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showToast(response.message, 'error');
                        }
                    });
                }
            });

            $('#avatarInput').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('avatar', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    $.ajax({
                        url: "{{ route('user.update.avatar') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status === 'success') {
                                showToast(response.message, 'success');
                                $('#avatarImage').attr('src', response.avatar_url);
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            showToast(response.message?.avatar?.[0] || 'Có lỗi xảy ra', 'error');
                        }
                    });
                }
            });

            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var type = button.data('type');
                var modal = $(this);

                var formContent = $('#formContent');
                formContent.empty();

                if (type == 'name') {
                    modal.find('.modal-title').text('Chỉnh sửa Họ và Tên');
                    formContent.append(`
                        <label for="editValue" class="form-label">Họ và Tên</label>
                        <input type="text" class="form-control" id="editValue" name="name" value="{{ $user->full_name }}" required>
                    `);
                } else if (type == 'phone') {
                    modal.find('.modal-title').text('Chỉnh sửa Số điện thoại');
                    formContent.append(`
                        <label for="editValue" class="form-label">Số điện thoại</label>
                        <input type="number" class="form-control" id="editValue" name="phone" value="{{ $user->phone ?? '' }}" required>
                    `);
                }else {
                    showToast('Thao tác sai, hãy thử lại', 'error');
                }
            });

            $('#otpPWModal').on('show.bs.modal', function(event) {
                var modal = $(this);
                $('#btn-send-otpPW').text('Tiếp tục');
                isPasswordStep = false;
                verifiedOTP = '';

                var formOTPContent = $('#formOTPPWContent');
                formOTPContent.empty();
                formOTPContent.append(`
                    <p class="text-center mb-3">
                        Chúng tôi sẽ gửi mã xác nhận OTP đến email của bạn.
                        Vui lòng đợi trong giây lát...
                    </p>
                    <div class="text-center">
                        <div class="spinner-border text-success mb-3" role="status">
                            <span class="visually-hidden">Đang gửi OTP...</span>
                        </div>
                        <p class="text-muted">Đang gửi mã OTP...</p>
                    </div>
                `);

                $.ajax({
                    url: "{{ route('user.update.password') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            formOTPContent.html(`
                                <p class="text-center mb-3">
                                    Mã OTP đã được gửi đến email của bạn.
                                    Vui lòng nhập mã nhận được để tiếp tục.
                                </p>
                                <div class="otp-input-container" id="input-otp-pw">
                                    <input type="text" maxlength="1" class="otp-digit" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
                                    <input type="text" maxlength="1" class="otp-digit" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
                                    <input type="text" maxlength="1" class="otp-digit" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
                                    <input type="text" maxlength="1" class="otp-digit" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
                                    <input type="text" maxlength="1" class="otp-digit" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
                                    <input type="text" maxlength="1" class="otp-digit" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
                                </div>
                            `);
                            
                            
                            setTimeout(() => {
                                $('#input-otp-pw .otp-digit').first().focus();
                            }, 100);
                        } else {
                            showToast(response.message || 'Có lỗi xảy ra khi gửi mã OTP', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        formOTPContent.html(`
                            <div class="text-center text-danger">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <p>Có lỗi xảy ra khi gửi mã OTP. Vui lòng thử lại sau.</p>
                            </div>
                        `);
                        showToast('Có lỗi xảy ra khi gửi mã OTP', 'error');
                    }
                });
            });

            @if (session('success'))
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('{{ session('success') }}', 'success');
                });
            @endif

            @if (session('error'))
                document.addEventListener('DOMContentLoaded', function() {
                    @if (is_array(session('error')))
                        @foreach (session('error') as $message)
                            @foreach ($message as $key => $value)
                                showToast('{{ $value }}', 'error');
                            @endforeach
                        @endforeach
                    @else
                        showToast('{{ session('error') }}', 'error');
                    @endif
                });
            @endif

        
            $(document).on('input', '.otp-digit', function(e) {
                const input = this;
            
                input.value = input.value.replace(/[^0-9]/g, '');
                const inputs = Array.from(input.parentElement.getElementsByClassName('otp-digit'));
                const currentIndex = inputs.indexOf(input);


                if (input.value && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                }
            });

            
            $(document).on('keydown', '.otp-digit', function(e) {
                const input = this;
                const inputs = Array.from(input.parentElement.getElementsByClassName('otp-digit'));
                const currentIndex = inputs.indexOf(input);

                if (e.key === 'Backspace') {
                    if (input.value) {
                        input.value = '';
                    } else if (currentIndex > 0) {
                        e.preventDefault();
                        inputs[currentIndex - 1].focus();
                        inputs[currentIndex - 1].value = '';
                    }
                }
            });

            $(document).on('paste', '.otp-digit', function(e) {
                e.preventDefault();
                const pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
                const inputs = Array.from(this.parentElement.getElementsByClassName('otp-digit'));
                const currentIndex = inputs.indexOf(this);

                const numbers = pastedData.replace(/[^0-9]/g, '').split('');

                numbers.forEach((number, index) => {
                    if (currentIndex + index < inputs.length) {
                        inputs[currentIndex + index].value = number;
                    }
                });

                const lastFilledIndex = Math.min(currentIndex + numbers.length - 1, inputs.length - 1);
                if (lastFilledIndex < inputs.length - 1) {
                    inputs[lastFilledIndex + 1].focus();
                }
            });
        });
    </script>
@endpush
