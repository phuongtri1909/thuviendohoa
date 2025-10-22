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
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
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

                imageClickables.forEach(img => {
                    img.addEventListener('click', function() {
                        const setId = this.getAttribute('data-set-id');
                        loadSetDetails(setId);
                        modal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    });
                });
            }

            function loadSetDetails(setId) {
                const modal = document.getElementById('imageModal');
                if (modal) {
                    modal.scrollTop = 0;
                }
                
                const modalContent = document.querySelector('#imageModal .modal-content');
                if (modalContent) {
                    modalContent.style.opacity = '0.5';
                    modalContent.style.pointerEvents = 'none';
                }
                
                let loadingSpinner = modal.querySelector('.loading-spinner-overlay');
                if (!loadingSpinner) {
                    loadingSpinner = document.createElement('div');
                    loadingSpinner.className = 'loading-spinner-overlay';
                    loadingSpinner.innerHTML = '<div class="spinner"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
                    modal.appendChild(loadingSpinner);
                }
                loadingSpinner.style.display = 'flex';

                fetch(`/search/set/${setId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (loadingSpinner) {
                        loadingSpinner.style.display = 'none';
                    }
                    if (modalContent) {
                        modalContent.style.opacity = '1';
                        modalContent.style.pointerEvents = 'auto';
                    }
                    
                    if (data.success) {
                        renderSetModal(data.data, data.relatedSets, data.featuredSets);
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (loadingSpinner) {
                        loadingSpinner.style.display = 'none';
                    }
                    if (modalContent) {
                        modalContent.style.opacity = '1';
                        modalContent.style.pointerEvents = 'auto';
                    }
                });
            }

            function renderSetModal(set, relatedSets = [], featuredSets = []) {
                const modalImageContainer = document.querySelector('#imageModal .col-12.col-md-7');
                if (modalImageContainer && set.photos && set.photos.length > 0) {
                    if (set.photos.length === 1) {
                        modalImageContainer.innerHTML = `
                            <a href="/storage/${set.photos[0].path}" data-fancybox="modal-gallery" data-caption="${set.name}">
                                <img src="/storage/${set.photos[0].path}" alt="${set.name}" class="img-fluid rounded-4">
                            </a>
                            <div class="mt-4">
                                <div id="social-share-container-${set.id}"></div>
                            </div>
                        `;
                        
                        setTimeout(() => {
                            initFancybox();
                        }, 100);
                    } else {
                        let photosHtml = '';
                        set.photos.forEach(photo => {
                            photosHtml += `
                                <div class="modal-photo-item">
                                    <a href="/storage/${photo.path}" data-fancybox="modal-gallery" data-caption="${set.name}">
                                        <img src="/storage/${photo.path}" alt="${set.name}" class="img-fluid rounded-4">
                                    </a>
                                </div>
                            `;
                        });
                        
                        modalImageContainer.innerHTML = `
                            <div class="modal-photos-masonry" id="modal-photos-masonry">
                                ${photosHtml}
                            </div>
                            <div class="mt-4">
                                <div id="social-share-container-${set.id}"></div>
                            </div>
                        `;
                        
                        setTimeout(() => {
                            initModalMasonry();
                            initFancybox();
                        }, 100);
                    }
                }

                // Update title
                const titleElement = document.querySelector('#imageModal .modal-title');
                if (titleElement) {
                    const words = set.name.split(' ');
                    if (words.length > 1) {
                        titleElement.innerHTML = `<span class="underline-first">${words[0]}</span> ${words.slice(1).join(' ')}`;
                    } else {
                        titleElement.innerHTML = `<span class="underline-first">${set.name}</span>`;
                    }
                }

                // Update description
                const descriptionElement = document.querySelector('#imageModal .modal-description');
                if (descriptionElement) {
                    descriptionElement.textContent = set.description || 'Không có mô tả';
                }

                // Update format
                const formatElement = document.querySelector('#imageModal .modal-format span');
                if (formatElement) {
                    let formatsText = 'Không xác định';
                    
                    if (set.formats) {
                        try {
                            const formatsArray = typeof set.formats === 'string' ? JSON.parse(set.formats) : set.formats;
                            if (Array.isArray(formatsArray) && formatsArray.length > 0) {
                                formatsText = formatsArray.join(', ');
                            }
                        } catch (e) {
                            console.error('Error parsing formats:', e);
                        }
                    }
                    
                    formatElement.textContent = `Định dạng: ${formatsText}`;
                }

                // Update size
                const sizeElement = document.querySelector('#imageModal .modal-size span');
                if (sizeElement) {
                    sizeElement.textContent = `Dung lượng: ${set.size ? set.size + ' MB' : 'Không xác định'}`;
                }

                // Update favorite count
                const favoriteElement = document.querySelector('#imageModal .modal-favorite span');
                if (favoriteElement) {
                    favoriteElement.textContent = `Yêu thích: ${set.bookmarks ? set.bookmarks.length : 0}`;
                }

                // Render social share
                renderSocialShare(set);

                // Update tags
                const tagsContainer = document.querySelector('.tags-product-list');
                if (tagsContainer) {
                    if (set.tags && set.tags.length > 0) {
                        const tagsHtml = set.tags.map(tag => `<span class="tags-product-item p-1 p-md-2 text-xs-2">${tag.tag.name}</span>`).join('');
                        tagsContainer.innerHTML = `
                            <span class="tags-product p-1 me-2 text-xs-2">
                                <img src="/images/svg/search-results/tag.svg" alt="">
                                Tags sản phẩm:
                            </span>
                            ${tagsHtml}
                        `;
                    } else {
                        tagsContainer.innerHTML = '';
                    }
                }

                // Update keywords
                const keywordWrapper = document.querySelector('#imageModal .modal-keywords-wrapper');
                
                if (keywordWrapper) {
                    let keywordsContent = '';
                    
                    if (set.keywords) {
                        try {
                            const keywordsArray = typeof set.keywords === 'string' ? JSON.parse(set.keywords) : set.keywords;
                            if (Array.isArray(keywordsArray) && keywordsArray.length > 0) {
                                const firstKeywords = keywordsArray.slice(0, 2).map(keyword => `<a class="color-primary-9" href="#">${keyword}</a>`).join(' ; ');
                                keywordsContent = firstKeywords;
                            }
                        } catch (e) {
                            console.error('Error parsing keywords:', e);
                        }
                    }
                    
                    keywordWrapper.innerHTML = `<span class="modal-keywords color-primary-12">Từ khóa:</span> ${keywordsContent} - <span class="color-primary-6">Mẫu #${set.id}</span>`;
                }

                // Update badge
                const badgeContainer = document.querySelector('#imageModal .d-flex.flex-column.mt-4');
                if (badgeContainer) {
                    const badgeType = set.type === 'free' ? 'free' : 'premium';
                    const badgeLabel = set.type === 'free' ? 'Free' : 'Premium';
                    const badgeValue = set.price || '0';
                    const badgeColor = set.type === 'free' ? '#27ae60' : '#F0A610';
                    
                    badgeContainer.innerHTML = `
                        <div class="custom-badge">
                            <div class="custom-badge-value" style="background-color: ${badgeColor}; color: #fff;">
                                ${badgeValue} XU
                            </div>
                            <div class="custom-badge-divider"></div>
                            <div class="custom-badge-label" style="background-color: ${badgeColor}; color: #fff;">
                                ${badgeLabel}
                            </div>
                        </div>
                        
                        <button class="btn-download btn fw-semibold py-3 px-5 d-flex mt-2">
                            <img src="/images/svg/arrow-right.svg" alt="" class="arrow-original">
                            <img src="/images/svg/arrow-right.svg" alt="" class="arrow-new">
                            Tải về máy
                        </button>
                    `;
                }
                
                const relatedSetsContainer = document.querySelector('#imageModal #sliderWrapper1');
                if (relatedSetsContainer && relatedSets && relatedSets.length > 0) {
                    relatedSetsContainer.innerHTML = '';
                    relatedSets.forEach(relatedSet => {
                        const imageSrc = `/storage/${relatedSet.image}`;
                        
                        const slideItem = document.createElement('div');
                        slideItem.className = 'slide-item';
                        slideItem.innerHTML = `
                            <a href="#" class="related-set-link" data-set-id="${relatedSet.id}">
                                <img src="${imageSrc}" alt="${relatedSet.name}" loading="lazy">
                            </a>
                        `;
                        relatedSetsContainer.appendChild(slideItem);
                    });
                    
                    if (window.ImageSlider) {
                        new window.ImageSlider('sliderWrapper1', 'prevBtn1', 'nextBtn1');
                    }
                }
                
                const featuredSetsContainer = document.querySelector('#imageModal #sliderAuto1');
                if (featuredSetsContainer && featuredSets && featuredSets.length > 0) {
                    featuredSetsContainer.innerHTML = '';
                    featuredSets.forEach(featuredSet => {
                        const imageSrc = `/storage/${featuredSet.image}`;
                        
                        const slideItem = document.createElement('div');
                        slideItem.className = 'slide-item';
                        slideItem.innerHTML = `
                            <a href="#" class="featured-set-link" data-set-id="${featuredSet.id}">
                                <img src="${imageSrc}" alt="${featuredSet.name}" loading="lazy">
                            </a>
                        `;
                        featuredSetsContainer.appendChild(slideItem);
                    });
                    
                    const autoSliderHost = document.querySelector('#imageModal .image-slider-auto');
                    if (autoSliderHost && window.AutoImageSlider) {
                        new window.AutoImageSlider(autoSliderHost, 'sliderAuto1');
                    }
                }
            }

            function renderSocialShare(set) {
                const container = document.querySelector(`#social-share-container-${set.id}`);
                if (!container) return;

                const shareUrl = `${window.location.origin}/search?set=${set.id}`;
                const shareTitle = set.name;
                const encodedUrl = encodeURIComponent(shareUrl);
                const encodedTitle = encodeURIComponent(shareTitle);

                container.innerHTML = `
                    <div class="social-share-wrapper">
                        <div class="social-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}" target="_blank"
                                class="social-btn facebook text-xs-1" title="Chia sẻ trên Facebook">
                                <i class="fab fa-facebook-f"></i>
                                <span>Facebook</span>
                            </a>

                            <a href="https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}" target="_blank"
                                class="social-btn twitter text-xs-1" title="Chia sẻ trên Twitter">
                                <i class="fab fa-twitter"></i>
                                <span>Twitter</span>
                            </a>

                            <a href="https://www.pinterest.com/pin/create/button/?url=${encodedUrl}" target="_blank"
                                class="social-btn pinterest text-xs-1" title="Chia sẻ trên Pinterest">
                                <i class="fab fa-pinterest-p"></i>
                                <span>Pinterest</span>
                            </a>

                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}" target="_blank"
                                class="social-btn linkedin text-xs-1" title="Chia sẻ trên LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                                <span>LinkedIn</span>
                            </a>
                        </div>

                        <button type="button" class="favorite-btn text-xs-1 ${set.isFavorited ? 'favorited' : ''}" data-set-id="${set.id}" onclick="toggleFavoriteModal(this)">
                            <i class="${set.isFavorited ? 'fas' : 'far'} fa-heart"></i>
                            <span>Yêu thích</span>
                        </button>
                    </div>
                `;
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

            function initModalMasonry() {
                const container = document.getElementById('modal-photos-masonry');
                if (!container) return;
                
                const images = container.querySelectorAll('img');
                let loadedImages = 0;
                
                if (images.length === 0) return;
                
                const checkAllImagesLoaded = () => {
                    loadedImages++;
                    if (loadedImages === images.length) {
                        if (typeof Masonry !== 'undefined') {
                            new Masonry(container, {
                                itemSelector: '.modal-photo-item',
                                columnWidth: '.modal-photo-item',
                                percentPosition: true,
                                gutter: 15
                            });
                        }
                    }
                };
                
                images.forEach(img => {
                    if (img.complete) {
                        checkAllImagesLoaded();
                    } else {
                        img.addEventListener('load', checkAllImagesLoaded);
                        img.addEventListener('error', checkAllImagesLoaded);
                    }
                });
            }

            function initFancybox() {
                if (typeof $.fancybox !== 'undefined') {
                    $('[data-fancybox="modal-gallery"]').fancybox({
                        buttons: [
                            "slideShow",
                            "thumbs",
                            "zoom",
                            "fullScreen",
                            "share",
                            "close"
                        ],
                        loop: true,
                        protect: true,
                        animationEffect: "fade",
                        transitionEffect: "slide",
                        toolbar: true,
                        infobar: true,
                        arrows: true
                    });
                } else if (typeof Fancybox !== 'undefined') {
                    Fancybox.bind('[data-fancybox="modal-gallery"]', {
                        Toolbar: {
                            display: {
                                left: ['infobar'],
                                middle: ['zoomIn', 'zoomOut', 'toggle1to1', 'rotateCCW', 'rotateCW', 'flipX', 'flipY'],
                                right: ['slideshow', 'fullscreen', 'thumbs', 'close']
                            }
                        }
                    });
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

            const modal = document.getElementById('imageModal');
            const closeModal = document.getElementById('closeModal');

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
            attachImageClickEvents();
            
            const modalUrlParams = new URLSearchParams(window.location.search);
            const setId = modalUrlParams.get('set');
            if (setId) {
                loadSetDetails(setId);
                document.getElementById('imageModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            
            document.addEventListener('click', function(e) {
                const relatedLink = e.target.closest('.related-set-link');
                const featuredLink = e.target.closest('.featured-set-link');
                
                if (relatedLink || featuredLink) {
                    e.preventDefault();
                    const link = relatedLink || featuredLink;
                    const setId = link.getAttribute('data-set-id');
                    if (setId) {
                        loadSetDetails(setId);
                    }
                }
            });

            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(refreshMasonry, 250);
            });
        });
    </script>
@endpush
