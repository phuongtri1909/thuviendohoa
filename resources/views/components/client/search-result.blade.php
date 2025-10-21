@props(['sets' => collect(), 'allColors' => collect(), 'allSoftware' => collect(), 'selectedColors' => [], 'selectedSoftware' => [], 'relatedTags' => collect(), 'selectedTags' => []])

<div class="search-result">
    <div class="d-flex justify-content-between align-items-center">
        <div class="color-selection">
            <button class="color-btn color-clear rounded-circle border bg-white" title="Xóa màu đã chọn">
                <i class="fas fa-times"></i>
            </button>
            @foreach ($allColors as $color)
                <button class="color-btn rounded-circle border {{ in_array($color->value, $selectedColors) ? 'active' : '' }}" 
                    style="background-color: {{ $color->value }}"
                    title="Chọn màu {{ $color->name }}"
                    data-color="{{ $color->value }}">
                </button>
            @endforeach
        </div>
        <div class="software-selection">
            <button class="software-btn {{ empty($selectedSoftware) ? 'active' : '' }}" title="Tất cả" data-software="all">
                <img src="{{ asset('images/svg/search-results/menu.svg') }}" alt="Tất cả">
            </button>
            @foreach ($allSoftware as $soft)
                <button class="software-btn {{ in_array($soft->id, $selectedSoftware) ? 'active' : '' }}" 
                    title="{{ $soft->name }}" 
                    data-software="{{ $soft->id }}"
                    data-logo="{{ Storage::url($soft->logo) }}"
                    data-logo-hover="{{ $soft->logo_hover ? Storage::url($soft->logo_hover) : null }}"
                    data-logo-active="{{ $soft->logo_active ? Storage::url($soft->logo_active) : null }}">
                    <img src="{{ Storage::url($soft->logo) }}" alt="{{ $soft->name }}">
                </button>
            @endforeach
        </div>
    </div>
    <div class="bg-white rounded-4 p-2 p-md-4 mt-2">
        <div>
            <span class="fw-semibold">Tags phân loại: </span>
            @if($relatedTags->count() > 0)
                @foreach($relatedTags as $tag)
                    <button class="tag-btn badge bg-primary-10 color-primary-11 p-2 p-md-3 rounded-4 mt-2 border-0 {{ in_array($tag->slug, $selectedTags) ? 'active' : '' }}" 
                        data-tag="{{ $tag->slug }}" 
                        title="Chọn tag {{ $tag->name }}">
                        {{ $tag->name }}
                    </button>
                @endforeach
            @else
                <span class="text-muted">Không có tag nào liên quan</span>
            @endif
        </div>

        <div id="search-results-container">
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
        </div>
        
        @if($sets->count() > 0)
            <div class="pagination-wrapper mt-4">
                {{ $sets->appends(request()->query())->links('components.paginate') }}
            </div>
        @endif
    </div>
</div>

@push('styles')
    @vite('resources/assets/frontend/css/search-result.css')
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colorBtns = document.querySelectorAll('.color-btn');
            let selectedColors = [];
            let debounceTimer = null;
            let isLoading = false;

            colorBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.classList.contains('color-clear')) {
                        selectedColors = [];
                        colorBtns.forEach(b => b.classList.remove('active'));
                    } else {
                        const colorValue = this.getAttribute('data-color');
                        
                        if (this.classList.contains('active')) {
                            selectedColors = selectedColors.filter(c => c !== colorValue);
                            this.classList.remove('active');
                        } else {
                            if (!selectedColors.includes(colorValue)) {
                                selectedColors.push(colorValue);
                                this.classList.add('active');
                            }
                        }
                    }

                    debouncedUpdateFilters();
                });
            });

            const softwareBtns = document.querySelectorAll('.software-btn');
            let selectedSoftware = [];

            const urlParams = new URLSearchParams(window.location.search);
            const selectedTags = urlParams.get('tags') ? urlParams.get('tags').split(',') : [];

            const tagBtns = document.querySelectorAll('.tag-btn');
            let selectedTagsArray = [...selectedTags];

            tagBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tagValue = this.getAttribute('data-tag');
                    
                    if (this.classList.contains('active')) {
                        selectedTagsArray = selectedTagsArray.filter(t => t !== tagValue);
                        this.classList.remove('active');
                    } else {
                        if (!selectedTagsArray.includes(tagValue)) {
                            selectedTagsArray.push(tagValue);
                            this.classList.add('active');
                        }
                    }
                    
                    debouncedUpdateFilters();
                });
            });

            softwareBtns.forEach(btn => {
                const img = btn.querySelector('img');
                const softwareValue = btn.getAttribute('data-software');
                const logo = btn.getAttribute('data-logo');
                const logoHover = btn.getAttribute('data-logo-hover');
                const logoActive = btn.getAttribute('data-logo-active');

                if (softwareValue !== 'all') {
                    if (!logoHover || logoHover === 'null') {
                        btn.classList.add('no-logo-hover');
                    }
                    if (!logoActive || logoActive === 'null') {
                        btn.classList.add('no-logo-active');
                    }
                }
                if (softwareValue !== 'all' && logoHover && logoHover !== 'null') {
                    btn.addEventListener('mouseenter', function() {
                        if (!this.classList.contains('active')) {
                            img.src = logoHover;
                        }
                    });

                    btn.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('active')) {
                            img.src = logo;
                        }
                    });
                }

                btn.addEventListener('click', function() {
                    if (softwareValue === 'all') {
                        selectedSoftware = [];
                        softwareBtns.forEach(b => {
                            b.classList.remove('active');
                            const bImg = b.querySelector('img');
                            const bLogo = b.getAttribute('data-logo');
                            if (bImg && bLogo) {
                                bImg.src = bLogo;
                            }
                        });
                        this.classList.add('active');
                    } else {
                        if (this.classList.contains('active')) {
                            selectedSoftware = selectedSoftware.filter(s => s !== softwareValue);
                            this.classList.remove('active');
                            img.src = logo;
                        } else {
                            if (!selectedSoftware.includes(softwareValue)) {
                                selectedSoftware.push(softwareValue);
                                this.classList.add('active');
                                
                                if (logoActive && logoActive !== 'null') {
                                    img.src = logoActive;
                                }
                            }
                        }
                        const allBtn = document.querySelector('[data-software="all"]');
                        if (selectedSoftware.length === 0) {
                            allBtn.classList.add('active');
                        } else {
                            allBtn.classList.remove('active');
                        }
                    }

                    debouncedUpdateFilters();
                });
            });

            function debouncedUpdateFilters() {
                if (debounceTimer) {
                    clearTimeout(debounceTimer);
                }
                
                debounceTimer = setTimeout(() => {
                    updateFilters();
                }, 300);
            }

            function updateFilters() {
                if (isLoading) {
                    return;
                }
                const urlParams = new URLSearchParams(window.location.search);
                const category = urlParams.get('category') || '';
                const album = urlParams.get('album') || '';
                const query = urlParams.get('q') || '';
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('category', category);
                formData.append('album', album);
                formData.append('q', query);
                
                selectedColors.forEach(color => {
                    formData.append('colors[]', color);
                });
                
                selectedSoftware.forEach(software => {
                    formData.append('software[]', software);
                });
                
                selectedTagsArray.forEach(tag => {
                    formData.append('tags[]', tag);
                });
                isLoading = true;
                const container = document.getElementById('search-results-container');
                container.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

                fetch('{{ route("search.filter") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        container.innerHTML = data.html;
                        setTimeout(() => {
                            initMasonry();
                            attachImageClickEvents();
                        }, 100);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = '<div class="text-center py-5"><h4>Lỗi khi tải dữ liệu</h4></div>';
                })
                .finally(() => {
                    isLoading = false;
                });
            }


            function attachImageClickEvents() {
                const imageClickables = document.querySelectorAll('.image-clickable');
                const modal = document.getElementById('imageModal');
                const modalImage = document.getElementById('modalImage');

                imageClickables.forEach(img => {
                    img.addEventListener('click', function() {
                        const imageUrl = this.getAttribute('data-image-url');
                        modalImage.src = imageUrl;
                        modal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    });
                });
            }

            function initMasonry() {
                const container = document.getElementById('masonry-container');
                if (container && container.children.length > 0) {
                    const images = container.querySelectorAll('img');
                    let loadedImages = 0;

                    if (images.length === 0) {
                        refreshMasonry();
                        return;
                    }

                    images.forEach(img => {
                        if (img.complete) {
                            loadedImages++;
                        } else {
                            img.addEventListener('load', () => {
                                loadedImages++;
                                if (loadedImages === images.length) {
                                    refreshMasonry();
                                }
                            });
                        }
                    });

                    if (loadedImages === images.length) {
                        refreshMasonry();
                    }
                }
            }

            function refreshMasonry() {
                const container = document.getElementById('masonry-container');
                if (container) {
                    container.style.display = 'none';
                    container.offsetHeight;
                    container.style.display = '';
                }
            }

            const imageClickables = document.querySelectorAll('.image-clickable');
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const closeModal = document.getElementById('closeModal');

            imageClickables.forEach(img => {
                img.addEventListener('click', function() {
                    const imageUrl = this.getAttribute('data-image-url');

                    modalImage.src = imageUrl;

                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                });
            });

            function closeImageModal() {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            closeModal.addEventListener('click', closeImageModal);

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    closeImageModal();
                }
            });

            initMasonry();

            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(refreshMasonry, 250);
            });
        });
    </script>
@endpush
