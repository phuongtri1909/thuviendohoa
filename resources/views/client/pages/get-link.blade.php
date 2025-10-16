@extends('client.layouts.app')
@section('title', 'Getlink Chất Lượng Cao')
@section('description', 'Tải file premium nhanh chóng và an toàn.')
@section('keyword', 'getlink, download, premium')

@section('content')
    <div class="getlink-page container-custom">
        <div class="p-2 pt-5 p-lg-5 container-getlink bg-white shadow-sm">
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
                        <input type="text" class="getlink-input"
                            placeholder="Sao chép liên kết bạn cần tải dán vào đây và nhấn nút getlink, file sẽ được tự động tải xuống" />
                        <button class="btn-getlink-new">GETLINK</button>
                    </div>

                    <div class="getlink-meta align-items-center mt-3">
                        <div class="getlink-fee getlink-warning fw-semibold">
                            <span class="fee-label">Phí getlink:</span>
                            <x-client.badge value="5 XU" label="Premium" />
                        </div>
                        <div class="getlink-warning fw-semibold">
                            Vui lòng đăng nhập để tải file
                        </div>
                    </div>
                </div>

                <!-- Danh sách trang hỗ trợ -->
                <div class="getlink-sites mt-5 py-0 py-md-5">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=freepik.com&sz=32" alt="Freepik"
                                class="site-favicon">
                            <span class="site-name color-primary-13">Freepik</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=shutterstock.com&sz=32" alt="Shutterstock"
                                class="site-favicon">
                            <span class="site-name color-primary-13">Shutterstock</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=istockphoto.com&sz=32" alt="iStockphoto"
                                class="site-favicon">
                            <span class="site-name color-primary-13">iStockphoto</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=stock.adobe.com&sz=32" alt="Adobe Stock"
                                class="site-favicon">
                            <span class="site-name color-primary-13">Adobe Stock</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=vecteezy.com&sz=32" alt="Vecteezy"
                                class="site-favicon">
                            <span class="site-name color-primary-13">Vecteezy</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=elements.envato.com&sz=32"
                                alt="Envato Elements" class="site-favicon">
                            <span class="site-name color-primary-13">Envato Elements</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=motionarray.com&sz=32" alt="MotionArray"
                                class="site-favicon">
                            <span class="site-name color-primary-13">MotionArray</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=pikbest.com&sz=32" alt="Pikbest"
                                class="site-favicon">
                            <span class="site-name color-primary-13">Pikbest</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=unsplash.com&sz=32" alt="Unsplash"
                                class="site-favicon">
                            <span class="site-name color-primary-13">Unsplash</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=slidesgo.com&sz=32" alt="SlidesGo"
                                class="site-favicon">
                            <span class="site-name color-primary-13">SlidesGo</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=123rf.com&sz=32" alt="123RF"
                                class="site-favicon">
                            <span class="site-name color-primary-13">123RF</span>
                        </div>
                        <div class="site-item">
                            <img src="https://www.google.com/s2/favicons?domain=depositphotos.com&sz=32" alt="Depositphotos"
                                class="site-favicon">
                            <span class="site-name color-primary-13">Depositphotos</span>
                        </div>
                    </div>
                </div>

                <x-client.about-content :title="'HƯỚNG DẪN TẢI FILE:'" :content="'Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không “giảm nhiệt” trong năm nay. Các công nghệ hiện đại và các phần mềm mang lại nhiều cơ hội cho xu hướng 3D phát triển, chúng ta sẽ tiếp tục thấy nhiều tác phẩm thiết kế đồ họa 3D tuyệt vời hơn vào năm 2020. Để tăng sự sáng tạo, các nhà thiết kế thường kết hợp chúng với các yếu tố khác, chẳng hạn như hình ảnh và các yếu tố 2D. Số lượt tìm kiếm “quần áo giá rẻ” bắt đầu giảm mạnh, trong khi cùng thời điểm này, số lượt tìm kiếm “quần áo bền vững” tăng mạnh. Trong cuốn Thế giới không rác thải, tác giả Ron Gonen cho rằng sự chú ý vào xu hướng phát triển bền vững trong ngành thời trang đang tăng đột phá. Trong cuốn Thế giới không rác thải, tác giả Ron Gonen cho rằng sự chú ý vào xu hướng phát triển bền vững trong ngành thời trang đang tăng đột phá'" />

            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/get-link.css')
@endpush

@push('scripts')
@endpush
