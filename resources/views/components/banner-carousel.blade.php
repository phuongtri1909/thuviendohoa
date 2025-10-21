@props([
    'banners',
    'hasBanners',
    'categories',
    'interval' => 3500,
    'showIcons' => true,
    'showOverlay' => false,
    'overlayContent' => null,
])

<div id="bannerCarousel" class="carousel slide banner-slide" data-bs-ride="carousel" data-bs-interval="{{ $interval }}">
    <div class="carousel-inner">
        @if ($hasBanners)
            @foreach ($banners as $index => $banner)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ Storage::url($banner->image) }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">
                    @if ($showOverlay && $overlayContent)
                        <div class="carousel-caption d-none d-md-block">
                            {!! $overlayContent !!}
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <!-- Fallback banners if no banners in database -->
            <div class="carousel-item active">
                <img src="{{ asset('/images/d/banners/banner1.png') }}" class="d-block w-100" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('/images/d/banners/banner2.png') }}" class="d-block w-100" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('/images/d/banners/banner3.png') }}" class="d-block w-100" alt="Banner 3">
            </div>
        @endif
    </div>


    <!-- Icon Buttons -->
    <div class="banner-icons">
        @if(isset($categories) && $categories->count() > 0)
            @foreach($categories as $category)
                <a href="{{ route('search', ['category' => $category->slug]) }}" class="color-primary-12 icon-box text-decoration-none">
                    <img class="rounded-4" src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}">
                    {{ $category->name }}
                </a>
            @endforeach
        @else
            <!-- Fallback static icons -->
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/text-effect-3d.png') }}" alt="">
                Hiệu ứng chữ (text effect 3D)
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/font-vh.png') }}" alt="">
                Tổng hợp font Việt Hóa
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/mockup-branding.png') }}" alt="">
                Bộ Mockup Branding
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/effect.png') }}" alt="">
                Hiệu ứng ánh sáng (effects)
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/adobe.png') }}" alt="">
                Full bộ Adobe bản quyền
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/plugin-preset.png') }}" alt="">
                Plugin, Preset, Brushes thiết kế
            </div>
            <div class="icon-box">
                <img class="rounded-4" src="{{ asset('/images/d/software.png') }}" alt="">
                Tổng hợp phần mềm cần thiết
            </div>
        @endif
    </div>

</div>
