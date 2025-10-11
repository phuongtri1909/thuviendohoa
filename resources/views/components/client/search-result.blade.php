<div class="search-result">
    <div class="d-flex justify-content-between align-items-center">
        <div class="color-selection">
            <button class="color-btn color-clear rounded-circle border bg-white" title="Xóa màu đã chọn">
                <i class="fas fa-times"></i>
            </button>
            @php
                $colors = [
                    '#FF0000',
                    '#00FF00',
                    '#0000FF',
                    '#FFFF00',
                    '#FF00FF',
                    '#00FFFF',
                    '#FFA500',
                    '#800080',
                    '#008000',
                    '#FFC0CB',
                ];
            @endphp
            @foreach ($colors as $color)
                <button class="color-btn rounded-circle border" style="background-color: {{ $color }}"
                    title="Chọn màu {{ $color }}">
                </button>
            @endforeach
        </div>
        <div class="software-selection">
            <button class="software-btn" title="Tất cả">
                <img src="{{ asset('images/svg/search-results/menu.svg') }}" alt="Tất cả">
            </button>
            <button class="software-btn" title="Adobe Illustrator">
                <img src="{{ asset('images/svg/search-results/ai.svg') }}" alt="Adobe Illustrator">
            </button>
            <button class="software-btn" title="Adobe Photoshop">
                <img src="{{ asset('images/svg/search-results/ps.svg') }}" alt="Adobe Photoshop">
            </button>
            <button class="software-btn" title="CorelDRAW">
                <img src="{{ asset('images/svg/search-results/pen.svg') }}" alt="CorelDRAW">
            </button>
            <button class="software-btn" title="Hình ảnh">
                <img src="{{ asset('images/svg/search-results/image.svg') }}" alt="Hình ảnh">
            </button>
        </div>
    </div>
    <div class="bg-white rounded-4 p-2 p-md-4 mt-2">
        <div>
            <span class="fw-semibold">tags phân loại: </span>
            @for ($i = 0; $i < 10; $i++)
                <span class="badge bg-primary-10 color-primary-11 p-2 p-md-3 rounded-4 mt-2">hasgtag Trung thu</span>
            @endfor
        </div>

        <div class="result-wrapper" id="masonry-container">
            @php
                $sampleImages = [
                    ['url' => 'https://picsum.photos/300/400?random=1', 'title' => 'Hình ảnh 1', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/400/300?random=2', 'title' => 'Hình ảnh 2', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/350/500?random=3', 'title' => 'Hình ảnh 3', 'height' => 'tall'],
                    ['url' =>  asset("images/d/bancoytuong.png"), 'title' => 'Bạn có ý tưởng thiết kế của riêng mình?', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/300/300?random=4', 'title' => 'Hình ảnh 4', 'height' => 'square'],
                    ['url' => 'https://picsum.photos/450/350?random=5', 'title' => 'Hình ảnh 5', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/320/450?random=6', 'title' => 'Hình ảnh 6', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/380/280?random=7', 'title' => 'Hình ảnh 7', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/300/350?random=8', 'title' => 'Hình ảnh 8', 'height' => 'medium'],
                    ['url' => 'https://picsum.photos/400/450?random=9', 'title' => 'Hình ảnh 9', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/350/300?random=10', 'title' => 'Hình ảnh 10', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/300/500?random=11', 'title' => 'Hình ảnh 11', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/420/320?random=12', 'title' => 'Hình ảnh 12', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/300/400?random=13', 'title' => 'Hình ảnh 1', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/400/300?random=14', 'title' => 'Hình ảnh 2', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/350/500?random=15', 'title' => 'Hình ảnh 3', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/300/300?random=16', 'title' => 'Hình ảnh 4', 'height' => 'square'],
                    ['url' => 'https://picsum.photos/450/350?random=17', 'title' => 'Hình ảnh 5', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/320/450?random=18', 'title' => 'Hình ảnh 6', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/380/280?random=19', 'title' => 'Hình ảnh 7', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/300/350?random=20', 'title' => 'Hình ảnh 8', 'height' => 'medium'],
                    ['url' => 'https://picsum.photos/400/450?random=21', 'title' => 'Hình ảnh 9', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/350/300?random=22', 'title' => 'Hình ảnh 10', 'height' => 'wide'],
                    ['url' => 'https://picsum.photos/300/500?random=23', 'title' => 'Hình ảnh 11', 'height' => 'tall'],
                    ['url' => 'https://picsum.photos/420/320?random=24', 'title' => 'Hình ảnh 12', 'height' => 'wide'],
                    
                ];
            @endphp
            
            @foreach($sampleImages as $index => $image)
                <div class="masonry-item" data-height="{{ $image['height'] }}">
                    <div class="image-card">
                        @if($image['title'] === 'Bạn có ý tưởng thiết kế của riêng mình?')
                            <a href="#" class="image-link">
                                <img src="{{ $image['url'] }}" alt="{{ $image['title'] }}" loading="lazy">
                            </a>
                        @else
                            <img src="{{ $image['url'] }}" alt="{{ $image['title'] }}" loading="lazy">
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- <x-client.pagination 
                :paginator="$sampleImages" 
                :show-select="false" 
                :max-visible="7" 
            /> --}}
        </div>
    </div>
</div>

@push('styles')
    @vite('resources/assets/frontend/css/search-result.css')
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý color selection
            const colorBtns = document.querySelectorAll('.color-btn');
            let selectedColor = null;

            colorBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Xóa class active khỏi tất cả nút màu
                    colorBtns.forEach(b => b.classList.remove('active'));

                    if (this.classList.contains('color-clear')) {
                        // Nếu click nút xóa
                        selectedColor = null;
                        console.log('Đã xóa màu đã chọn');
                    } else {
                        // Nếu click nút màu
                        this.classList.add('active');
                        selectedColor = this.style.backgroundColor;
                        console.log('Đã chọn màu:', selectedColor);
                    }
                });
            });

            // Xử lý software selection
            const softwareBtns = document.querySelectorAll('.software-btn');
            let selectedSoftware = null;

            softwareBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Xóa class active khỏi tất cả nút software
                    softwareBtns.forEach(b => b.classList.remove('active'));

                    // Thêm class active cho nút được click
                    this.classList.add('active');
                    selectedSoftware = this.title;
                    console.log('Đã chọn phần mềm:', selectedSoftware);
                });
            });

            // Xử lý masonry layout
            function initMasonry() {
                const container = document.getElementById('masonry-container');
                if (container) {
                    // Đảm bảo tất cả ảnh đã load xong
                    const images = container.querySelectorAll('img');
                    let loadedImages = 0;
                    
                    if (images.length === 0) return;
                    
                    images.forEach(img => {
                        img.addEventListener('load', () => {
                            loadedImages++;
                            if (loadedImages === images.length) {
                                // Tất cả ảnh đã load, refresh layout
                                refreshMasonry();
                            }
                        });
                    });
                }
            }

            function refreshMasonry() {
                const container = document.getElementById('masonry-container');
                if (container) {
                    // Force reflow để masonry layout được tính toán lại
                    container.style.display = 'none';
                    container.offsetHeight; // Trigger reflow
                    container.style.display = '';
                }
            }

            // Xử lý click cho image link (bancoytuong)
            const imageLinks = document.querySelectorAll('.image-link');
            imageLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const imageTitle = this.querySelector('img').alt;
                    
                    console.log(`Click vào: ${imageTitle}`);
                    
                    // Thêm hiệu ứng click
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                    
                    // Có thể thêm logic redirect hoặc modal ở đây
                    // window.location.href = 'your-link-here';
                });
            });

            // Khởi tạo masonry
            initMasonry();

            // Refresh masonry khi resize window
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(refreshMasonry, 250);
            });
        });
    </script>
@endpush
