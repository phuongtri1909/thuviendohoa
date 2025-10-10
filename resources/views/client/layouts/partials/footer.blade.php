<!-- Social Media Bar -->
<div class="social-bar">
    <a href="#"><i class="fab fa-facebook-f"></i></a>
    <a href="#"><i class="fab fa-instagram"></i></a>
    <a href="#"><i class="fab fa-twitter"></i></a>
    <a href="#"><i class="fab fa-youtube"></i></a>
    <a href="#"><i class="fab fa-google"></i></a>
    <a href="#"><i class="fab fa-linkedin-in"></i></a>
</div>

<!-- Contribution Section -->
<div class="contribution-section ">
    <div class="container-xxl">
        <h3 class="text-md-4">Đóng góp của bạn sẽ giúp tôi hoàn thiện hơn</h3>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Góp ý của bạn về trang...">
            <button class="btn btn-submit" type="button">
                <img src="{{ asset('/images/svg/submit-form.svg') }}" alt="Submit">
            </button>
        </div>
    </div>
</div>

<!-- Footer Content -->
<div class="footer-section">
    <div class="container-xxl">
        <div class="footer-content">
            <div class="row">
                <!-- About Section -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-4 col-footer">
                    <h5 class="footer-title">Về tôi</h5>
                    <div class="footer-card text-center">
                        <div class="fb-page" data-href="https://www.facebook.com/thuvien24hh?locale=vi_VN"
                            data-small-header="false" data-adapt-container-width="true" data-hide-cover="false"
                            data-show-facepile="true">
                        </div>
                    </div>
                </div>

                <!-- Support Section -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-4 col-footer">
                    <h5 class="footer-title">Hỗ trợ</h5>
                    <div class="contact-info text-center">
                        <p>Hotline/Zalo: <a class="text-decoration-none color-primary-3" href="tel:0944133994">0944 133
                                994</a></p>
                        <p>Email: <a class="text-decoration-none color-primary-7"
                                href="mailto:printon.hcm@gmail.com">printon.hcm@gmail.com</a></p>
                        <p>Fanpage: <a class="text-decoration-none color-primary-7" href="#">Printon</a></p>
                    </div>
                </div>

                <!-- Information Section -->
                <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-4 col-footer">
                    <h5 class="footer-title">Thông tin</h5>
                    <a href="#" class="footer-link">Giới thiệu về Printon</a>
                    <a href="#" class="footer-link">Điều khoản chung</a>
                    <a href="#" class="footer-link">Chính sách bảo mật</a>
                </div>

                <!-- Partner Section -->
                <div class="col-12 col-sm-6 col-md-12 col-xl-3 mb-4 col-footer text-center">
                    <h5 class="footer-title">Đối tác chính</h5>
                    <div class="row align-items-center">
                        <div class="col-6">
                            <img class="img-fluid" src="{{ asset('images/d/doi-tac/thegioiinan.png') }}"
                                alt="TheGioiInAn">
                        </div>
                        <div class="col-6">
                            <img class="img-fluid" src="{{ asset('images/d/doi-tac/gifgo.png') }}" alt="Gifgo">
                        </div>
                        <div class="col-6">
                            <img class="img-fluid" src="{{ asset('images/d/doi-tac/checkgo.png') }}" alt="CheckGo">
                        </div>
                        <div class="col-6">
                            <img class="img-fluid" src="{{ asset('images/d/doi-tac/pakgo.png') }}" alt="PakGo">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container-xxl">
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
    @stack('scripts')
    </body>

    </html>
