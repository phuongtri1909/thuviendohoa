@if($sets->count() > 0)
    <div class="result-wrapper" id="masonry-container">
        @foreach($sets as $set)
            @if($set->photos && $set->photos->count() > 0)
                <div class="masonry-item" data-height="tall">
                    <div class="image-card">
                        <img src="{{ Storage::url($set->image) }}" alt="{{ $set->name }}" loading="lazy"
                            class="image-clickable" data-image-url="{{ Storage::url($set->image) }}"
                            data-image-title="{{ $set->name }}"
                            data-set-id="{{ $set->id }}">
                    </div>
                </div>
            @endif
        @endforeach
        
        {{-- Item đặc biệt để chèn link --}}
        <div class="masonry-item" data-height="wide">
            <div class="image-card">
                <a href="{{ url('/y-tuong-thiet-ke') }}" class="text-decoration-none color-primary-12">
                    <img src="{{ asset('images/d/bancoytuong.png') }}" alt="Bạn có ý tưởng thiết kế của riêng mình?" loading="lazy">
                </a>
            </div>
        </div>
    </div>
@else
    <div class="d-flex flex-column align-items-center justify-content-center py-5">
        <div class="text-center">
            <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
            <h4 class="text-muted mb-0">Không tìm thấy kết quả nào</h4>
        </div>
    </div>
@endif
