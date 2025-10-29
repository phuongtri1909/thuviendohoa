@extends('client.layouts.app')
@section('title', 'Getlink Chất Lượng Cao')
@section('description', 'Tải file premium nhanh chóng và an toàn.')
@section('keyword', 'getlink, download, premium')

@section('content')
    <div class="getlink-page">
        <div class="container-custom">
            <div class="pt-0 p-2 px-lg-5 pb-lg-5  container-getlink bg-white shadow-sm">
                <div class="pt-3">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb color-primary-12">
                            <li class="breadcrumb-item "><a class="color-primary-12 text-decoration-none"
                                    href="{{ route('home') }}">TRANG CHỦ</a></li>
                            <li class="breadcrumb-item color-primary-12 active fw-semibold" aria-current="page">Getlink</li>
                        </ol>
                    </nav>
                </div>
                <div class="px-0 p-lg-5">
                <div class="text-center mb-4">
                    <h2 class="text-uppercase fw-bold fs-3 color-primary">GETLINK CHẤT LƯỢNG CAO</h2>
                    <p class="color-primary-12 mt-2 px-2 px-md-5">
                        Trang web này giúp bạn get premium chỉ trong 1 giây tự động hoàn toàn,
                        hãy chọn đúng trang tải nguyên bạn cần và sao chép liên kết cần tải dán vào ô phía dưới:
                    </p>
                </div>

                <!-- Form getlink -->
                <div class="getlink-form-wrapper mx-auto mt-5 py-0 py-md-5">
                    <div class="getlink-input-group">
                        <input type="text" class="getlink-input" id="url-input"
                            placeholder="Sao chép liên kết bạn cần tải dán vào đây và nhấn nút getlink, file sẽ được tự động tải xuống" />
                        <button class="btn-getlink-new" id="getlink-btn">GETLINK</button>
                    </div>

                    <div class="getlink-meta align-items-center mt-3">
                        <div class="getlink-fee getlink-warning fw-semibold">
                            <span class="fee-label">Phí getlink:</span>
                            <div class="custom-badge">
                                <div class="custom-badge-value" style="background-color: #F0A610; color: #fff;">
                                    <span id="coins-display">{{ $config->coins }}</span> XU
                                </div>
                                <div class="custom-badge-divider"></div>
                                <div class="custom-badge-label" style="background-color: #F0A610; color: #fff;">
                                    Premium
                                </div>
                            </div>
                        </div>
                        <div class="getlink-warning fw-semibold" id="user-status">
                            @auth
                                Xu hiện tại: <strong id="user-coins">{{ auth()->user()->coins }}</strong> xu
                            @else
                                Vui lòng đăng nhập để tải file
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Danh sách trang hỗ trợ -->
                <div class="getlink-sites mt-5 py-0 py-md-5">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @foreach($supportedSites as $site)
                            <a href="{{ $site['url'] }}" class="site-item text-decoration-none" target="_blank">
                                <img src="{{ $site['favicon'] }}" 
                                     alt="{{ $site['name'] }}" 
                                     class="site-favicon"
                                     onerror="this.src='https://www.google.com/s2/favicons?domain={{ $site['domain'] }}&sz=32'">
                                <span class="site-name color-primary-13">{{ $site['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <x-client.about-content :title="'HƯỚNG DẪN TẢI FILE:'" :content="'Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không “giảm nhiệt” trong năm nay. Các công nghệ hiện đại và các phần mềm mang lại nhiều cơ hội cho xu hướng 3D phát triển, chúng ta sẽ tiếp tục thấy nhiều tác phẩm thiết kế đồ họa 3D tuyệt vời hơn vào năm 2020. Để tăng sự sáng tạo, các nhà thiết kế thường kết hợp chúng với các yếu tố khác, chẳng hạn như hình ảnh và các yếu tố 2D. Số lượt tìm kiếm “quần áo giá rẻ” bắt đầu giảm mạnh, trong khi cùng thời điểm này, số lượt tìm kiếm “quần áo bền vững” tăng mạnh. Trong cuốn Thế giới không rác thải, tác giả Ron Gonen cho rằng sự chú ý vào xu hướng phát triển bền vững trong ngành thời trang đang tăng đột phá. Trong cuốn Thế giới không rác thải, tác giả Ron Gonen cho rằng sự chú ý vào xu hướng phát triển bền vững trong ngành thời trang đang tăng đột phá'" />

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/get-link.css')
    <style>
        .custom-badge {
            display: inline-flex;
            align-items: stretch;
            font-weight: 600;
            font-size: 16px;
            height: 32px;
            position: relative;
            margin-right: 15px;
            white-space: nowrap;
        }

        .custom-badge-value {
            padding: 0 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 4px 0 0 4px;
            font-size: 17px;
            font-weight: 700;
            min-width: 55px;
        }

        .custom-badge-divider {
            width: 2px;
            background-color: rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .custom-badge-label {
            padding: 0 40px 0 5px;
            display: flex;
            align-items: center;
            position: relative;
            font-size: 15px;
            font-weight: 600;
            min-width: 100px;
            clip-path: polygon(0 0, calc(100% - 20px) 0, 70% 50%, calc(100% - 20px) 100%, 0 100%);
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlInput = document.getElementById('url-input');
        const getlinkBtn = document.getElementById('getlink-btn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        getlinkBtn.addEventListener('click', function() {
            const url = urlInput.value.trim();

            if (!url) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu URL',
                    text: 'Vui lòng nhập URL cần get link',
                    confirmButtonColor: '#667eea'
                });
                return;
            }

            @guest
                Swal.fire({
                    icon: 'warning',
                    title: 'Chưa đăng nhập',
                    text: 'Vui lòng đăng nhập để sử dụng dịch vụ',
                    confirmButtonText: 'Đăng nhập',
                    confirmButtonColor: '#667eea',
                    showCancelButton: true,
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("login") }}';
                    }
                });
                return;
            @endguest

            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng đợi',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("get.link.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ url: url })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Get link thành công!',
                        html: `
                            <div class="text-start">
                                <p><strong>URL:</strong> <a href="${data.data.url}" target="_blank" class="text-break">${data.data.url}</a></p>
                                <p><strong>Title:</strong> ${data.data.title}</p>
                                <p><strong>Xu đã trừ:</strong> <span class="text-danger">${data.data.coins_spent} xu</span></p>
                                <p><strong>Xu còn lại:</strong> <span class="text-success">${data.data.remaining_coins} xu</span></p>
                            </div>
                        `,
                        confirmButtonColor: '#667eea',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        urlInput.value = '';
                        
                        @auth
                            const userCoins = document.getElementById('user-coins');
                            if (userCoins) {
                                userCoins.textContent = data.data.remaining_coins;
                            }
                        @endauth
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message,
                        confirmButtonColor: '#667eea'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi xử lý yêu cầu',
                    confirmButtonColor: '#667eea'
                });
            });
        });

        urlInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                getlinkBtn.click();
            }
        });
    });
</script>
@endpush
