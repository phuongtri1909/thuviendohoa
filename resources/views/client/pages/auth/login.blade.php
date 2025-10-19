@extends('client.layouts.main')
@section('title', 'Tạo tài khoản')
@section('description', 'Tạo tài khoản . ' . config('app.name'))
@section('keywords', 'Tạo tài khoản . ' . config('app.name'))
@section('content-main')
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

        <div class="login-form-section position-relative">
            <button class="close-btn" onclick="window.history.back()">
                <i class="fas fa-times"></i>
            </button>

            <div class="login-header">
                <h4>Bạn cần đăng nhập bằng:</h4>
            </div>

            <div class="social-login">
                <a href="{{ route('login.google') }}" class="social-btn google text-decoration-none">
                    <i class="fab fa-google"></i>
                </a>
                <a href="{{ route('login.facebook') }}" class="social-btn facebook text-decoration-none">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="{{ route('login.twitter') }}" class="social-btn twitter text-decoration-none">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>

            <div class="divider">
                <span>Hoặc</span>
            </div>

            @include('components.toast-main')
            @include('components.toast')

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label class="ms-3" for="email">Gmail của bạn</label>
                    <input type="email" class="form-input @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
                    @error('email')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="ms-3" for="password">Mật khẩu</label>
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
                        <label class="color-primary-13 fst-italic" for="remember">Nhớ đăng nhập</label>
                    </div>
                    <a href="{{ route('forgot-password') }}" class="forgot-password color-primary-13 fst-italic">Bạn quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-login">
                    Đăng nhập
                </button>
            </form>

            <div class="signup-link">
                Bạn chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
            </div>
        </div>
    </div>
@endsection
