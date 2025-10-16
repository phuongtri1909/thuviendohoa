<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Đăng nhập trang quản trị</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    @vite('resources/assets/frontend/css/styles.css')
    @vite('resources/assets/frontend/css/styles-auth.css')

</head>

<body>
    <div class="login-wrapper rounded-5">
        <div class="login-banner">
            <div class="banner-content">
                <h2>
                    Chào mừng trở lại<br>
                    <span class="highlight">{{ config('app.name') }}</span>
                </h2>
                <p>Các file ảnh chất lượng đang chờ bạn</p>
            </div>
            <div class="banner-image">
                <img src="{{ asset('images/d/login.jpg') }}" alt="Login Banner">
            </div>
        </div>

        <div class="login-form-section">
            <button class="close-btn" onclick="window.history.back()">
                <i class="fas fa-times"></i>
            </button>

            <div class="login-header">
                <h4>Bạn cần đăng nhập bằng:</h4>
            </div>

            <div class="social-login">
                <button class="social-btn google">
                    <i class="fab fa-google"></i>
                </button>
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

            @include('components.toast-main')
            @include('components.toast')

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Số Phone hoặc Gmail của bạn</label>
                    <input type="email" class="form-input @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
                    @error('email')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" class="form-input @error('password') is-invalid @enderror" id="password"
                        name="password" placeholder="••••••••" required>
                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-check-group">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Nhớ đăng nhập</label>
                    </div>
                    <a href="#" class="forgot-password">Bạn quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-login">
                    Đăng nhập
                </button>
            </form>

            <div class="signup-link">
                Bạn chưa có tài khoản? <a href="#">Đăng ký ngay</a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
