@props([
    'sets' => collect(),
    'allColors' => collect(),
    'allSoftware' => collect(),
    'selectedColors' => [],
    'selectedSoftware' => [],
    'relatedTags' => collect(),
    'selectedTags' => [],
])

<div class="search-result">
    <div class="d-flex justify-content-between align-items-center">
        <div class="color-selection">
            <button class="color-btn color-clear rounded-circle border bg-white" title="Xóa màu đã chọn">
                <i class="fas fa-times"></i>
            </button>
            @foreach ($allColors as $color)
                <button
                    class="color-btn rounded-circle border {{ in_array($color->value, $selectedColors) ? 'active' : '' }}"
                    style="background-color: {{ $color->value }}" title="Chọn màu {{ $color->name }}"
                    data-color="{{ $color->value }}">
                </button>
            @endforeach
        </div>
        <div class="software-selection">
            <button class="software-btn {{ empty($selectedSoftware) ? 'active' : '' }}" title="Tất cả"
                data-software="all">
                <img src="{{ asset('images/svg/search-results/menu.svg') }}" alt="Tất cả">
            </button>
            @foreach ($allSoftware as $soft)
                <button class="software-btn {{ in_array($soft->id, $selectedSoftware) ? 'active' : '' }}"
                    title="{{ $soft->name }}" data-software="{{ $soft->id }}"
                    data-logo="{{ Storage::url($soft->logo) }}"
                    data-logo-hover="{{ $soft->logo_hover ? Storage::url($soft->logo_hover) : null }}"
                    data-logo-active="{{ $soft->logo_active ? Storage::url($soft->logo_active) : null }}">
                    <img src="{{ Storage::url($soft->logo) }}" alt="{{ $soft->name }}">
                </button>
            @endforeach
        </div>
    </div>
    <div class="bg-white rounded-4 p-2 py-md-4 px-md-5 mt-2">
        <div>
            <span class="fw-semibold fs-6 text-xs-1 me-2">Tags phân loại: </span>
            @if ($relatedTags->count() > 0)
                @foreach ($relatedTags as $tag)
                    <button
                        class="me-2 tag-btn badge bg-primary-10 color-primary-11 p-2 rounded-4 mt-2 border-0 {{ in_array($tag->slug, $selectedTags) ? 'active' : '' }}"
                        data-tag="{{ $tag->slug }}" title="Chọn tag {{ $tag->name }}">
                        {{ $tag->name }}
                    </button>
                @endforeach
            @else
                <span class="text-muted">Không có tag nào liên quan</span>
            @endif
        </div>

        <div id="search-results-container">
            @if ($sets->count() > 0)
                <div class="result-wrapper" id="masonry-container">
                    @foreach ($sets as $set)
                        @if ($set->photos && $set->photos->count() > 0)
                            <div class="masonry-item" data-height="tall">
                                <div class="image-card" data-set-type="{{ $set->type }}"
                                    data-set-price="{{ $set->price }}" data-set-id="{{ $set->id }}"
                                    onclick="openSetModalFromCard(event, {{ $set->id }})">
                                    <img src="{{ Storage::url($set->image) }}" alt="{{ $set->name }}"
                                        loading="lazy" class="image-clickable"
                                        data-image-url="{{ Storage::url($set->image) }}"
                                        data-image-title="{{ $set->name }}" data-set-id="{{ $set->id }}">

                                    @if ($set->software && $set->software->count() > 0)
                                        <div class="software-icons-overlay">
                                            @foreach ($set->software as $softwareSet)
                                                @php
                                                    $soft = $softwareSet->software;
                                                    $logoToShow = $soft->logo_active
                                                        ? Storage::url($soft->logo_active)
                                                        : ($soft->logo_hover
                                                            ? Storage::url($soft->logo_hover)
                                                            : Storage::url($soft->logo));
                                                @endphp
                                                <div class="software-icon-item" title="{{ $soft->name }}">
                                                    <img src="{{ $logoToShow }}" alt="{{ $soft->name }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="image-card-hover-overlay">
                                        @if ($set->type === 'premium')
                                            <div class="overlay-price-badge">
                                                <span>{{ $set->price }}</span>
                                                <img src="{{ asset('/images/svg/coins.svg') }}" alt="Xu"
                                                    class="mt-1">
                                            </div>
                                        @endif

                                        <div class="overlay-actions-right">
                                            @php
                                                $isFavorited = false;
                                                if (auth()->check()) {
                                                    $isFavorited =
                                                        $set->bookmarks &&
                                                        $set->bookmarks->where('user_id', auth()->id())->count() > 0;
                                                }
                                            @endphp
                                            @auth
                                                <button
                                                    class="overlay-action-btn favorite-btn-card {{ $isFavorited ? 'favorited' : '' }}"
                                                    data-set-id="{{ $set->id }}"
                                                    onclick="toggleFavoriteCard(event, this)" title="Yêu thích">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16"
                                                        viewBox="0 0 17 16" fill="none" class="mt-1 heart-svg">
                                                        <path
                                                            d="M11.8828 0.505859C12.9949 0.463268 13.9409 0.814387 14.7637 1.56055L14.9268 1.71582C15.6008 2.40349 16.0009 3.2188 16.1133 4.20215V4.20508C16.2017 4.94809 16.0705 5.65581 15.792 6.36426C15.3403 7.44936 14.6964 8.40091 13.9385 9.32129L13.9326 9.32812L13.9277 9.33594C13.5473 9.83351 13.1373 10.2919 12.6953 10.7451L11.8789 11.5615C11.044 12.3835 10.155 13.1648 9.22852 13.8887C8.91804 14.1203 8.60681 14.3696 8.31641 14.6025C7.55442 14.0279 6.80397 13.4423 6.09961 12.8145L5.77441 12.5186C5.16959 11.9545 4.57928 11.4036 4.04004 10.8301L4.02734 10.8174L3.69238 10.4775C2.9221 9.67654 2.23244 8.81707 1.6377 7.86621C1.23044 7.21185 0.879675 6.54773 0.675781 5.83105L0.597656 5.52051C0.284434 3.93626 0.726723 2.60981 1.89844 1.5293C2.61741 0.889289 3.43781 0.551504 4.40234 0.505859L4.4043 0.504883C5.29292 0.459726 6.12157 0.706887 6.90039 1.19629L6.91016 1.20117C7.28286 1.42424 7.61919 1.71901 7.94238 2.09473L8.32617 2.54004L8.7041 2.09082C9.11036 1.60865 9.60524 1.21953 10.166 0.944336C10.7206 0.685791 11.2829 0.528453 11.8818 0.505859H11.8828Z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button class="overlay-action-btn favorite-btn-card"
                                                    onclick="event.stopPropagation(); window.location.href='{{ route('login') }}'"
                                                    title="Đăng nhập để yêu thích">
                                                    <img src="{{ asset('/images/svg/whitelist.svg') }}" alt="Whitelist"
                                                        class="mt-1">
                                                </button>
                                            @endauth

                                            <button class="overlay-action-btn download-btn-card"
                                                data-set-id="{{ $set->id }}"
                                                onclick="handleDownloadClick(event, {{ $set->id }})"
                                                title="Tải về">
                                                <img src="{{ asset('/images/svg/search-results/download.svg') }}"
                                                    alt="Whitelist" class="mt-1"
                                                    style="{{ $isFavorited ? 'filter: contrast(0);' : '' }}">
                                            </button>
                                        </div>

                                        <div class="overlay-website-logo">
                                            <img src="{{ $logoPath }}" alt="Logo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div class="masonry-item" data-height="wide">
                        <div class="image-card">
                            <a href="{{ url('/y-tuong-thiet-ke') }}" class="text-decoration-none color-primary-12">
                                <img src="{{ asset('images/d/bancoytuong.png') }}"
                                    alt="Bạn có ý tưởng thiết kế của riêng mình?" loading="lazy">
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

        @if ($sets->count() > 0)
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
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                'content'));
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
                container.innerHTML =
                    '<div class="text-center py-5"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

                fetch('{{ route('search.filter') }}', {
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
                                attachPaginationEvents();
                            }, 100);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        container.innerHTML =
                        '<div class="text-center py-5"><h4>Lỗi khi tải dữ liệu</h4></div>';
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

            function attachPaginationEvents() {
                const paginationLinks = document.querySelectorAll('.pagination-wrapper .pagination-item');

                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const url = this.getAttribute('href');
                        if (!url || url === '#') return;

                        const pageMatch = url.match(/[?&]page=(\d+)/);
                        if (pageMatch) {
                            loadFilteredPage(parseInt(pageMatch[1]));
                        }
                    });
                });
            }

            function loadFilteredPage(page) {
                if (isLoading) return;

                const urlParams = new URLSearchParams(window.location.search);
                const category = urlParams.get('category') || '';
                const album = urlParams.get('album') || '';
                const query = urlParams.get('q') || '';

                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                'content'));
                formData.append('category', category);
                formData.append('album', album);
                formData.append('q', query);
                formData.append('page', page);

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
                container.innerHTML =
                    '<div class="text-center py-5"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

                fetch('{{ route('search.filter') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            container.innerHTML = data.html;

                            // Scroll to top of results
                            const searchResult = document.querySelector('.search-result');
                            if (searchResult) {
                                searchResult.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }

                            setTimeout(() => {
                                initMasonry();
                                attachImageClickEvents();
                                attachPaginationEvents();
                            }, 100);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        container.innerHTML =
                        '<div class="text-center py-5"><h4>Lỗi khi tải dữ liệu</h4></div>';
                    })
                    .finally(() => {
                        isLoading = false;
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
                    loadingSpinner.innerHTML =
                        '<div class="spinner"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
                    modal.appendChild(loadingSpinner);
                }
                loadingSpinner.style.display = 'flex';

                fetch(`/search/set/id/${setId}`, {
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
                            showSetNotFoundError(data.message, data.featuredSets || []);
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
                        showSetNotFoundError('Có lỗi xảy ra khi tải thông tin file', []);
                    });
            }

            function loadSetDetailsBySlug(setSlug) {
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
                    loadingSpinner.innerHTML =
                        '<div class="spinner"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
                    modal.appendChild(loadingSpinner);
                }
                loadingSpinner.style.display = 'flex';

                // Kiểm tra nếu setSlug là số (ID) thì dùng route ID
                const isNumeric = /^\d+$/.test(setSlug);
                const apiUrl = isNumeric ? `/search/set/id/${setSlug}` : `/search/set/${setSlug}`;

                fetch(apiUrl, {
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
                            showSetNotFoundError(data.message, data.featuredSets || []);
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
                        showSetNotFoundError('Có lỗi xảy ra khi tải thông tin file', []);
                    });
            }

            function renderSetModal(set, relatedSets = [], featuredSets = []) {
                const errorContent = document.querySelector('.error-content');
                if (errorContent) {
                    errorContent.remove();
                }

                const modalContent = document.querySelector('#imageModal .modal-content');
                if (modalContent) {
                    const hiddenRows = modalContent.querySelectorAll('.row:not(.error-content)');
                    hiddenRows.forEach(row => {
                        row.style.display = '';
                    });

                    const imageSlider = modalContent.querySelector('.image-slider');
                    if (imageSlider) {
                        imageSlider.style.display = '';
                    }

                    const tagsList = modalContent.querySelector('.tags-product-list');
                    if (tagsList) {
                        tagsList.style.display = '';
                    }
                }

                const modalImageContainer = document.querySelector('#imageModal .col-12.col-md-7');
                if (modalImageContainer && set.photos && set.photos.length > 0) {
                    if (set.photos.length === 1) {
                        modalImageContainer.innerHTML = `
                            <div class="modal-image-container">
                                <a href="/storage/${set.photos[0].path}" data-fancybox="modal-gallery" data-caption="${set.name}">
                                    <img src="/storage/${set.photos[0].path}" alt="${set.name}" class="img-fluid rounded-4">
                                </a>
                            </div>
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
                            <div class="modal-image-container">
                                <div class="modal-photos-masonry" id="modal-photos-masonry">
                                    ${photosHtml}
                                </div>
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

                const titleElement = document.querySelector('#imageModal .modal-title');
                if (titleElement) {
                    const words = set.name.split(' ');
                    if (words.length > 1) {
                        titleElement.innerHTML =
                            `<span class="underline-first">${words[0]}</span> ${words.slice(1).join(' ')}`;
                    } else {
                        titleElement.innerHTML = `<span class="underline-first">${set.name}</span>`;
                    }
                }

                const descriptionElement = document.querySelector('#imageModal .modal-description');
                if (descriptionElement) {
                    descriptionElement.textContent = set.description || 'Không có mô tả';
                }

                const formatElement = document.querySelector('#imageModal .modal-format span');
                if (formatElement) {
                    let formatsText = 'Không xác định';

                    if (set.formats) {
                        try {
                            const formatsArray = typeof set.formats === 'string' ? JSON.parse(set.formats) : set
                                .formats;
                            if (Array.isArray(formatsArray) && formatsArray.length > 0) {
                                formatsText = formatsArray.join(', ');
                            }
                        } catch (e) {
                            console.error('Error parsing formats:', e);
                        }
                    }

                    formatElement.textContent = `Định dạng: ${formatsText}`;
                }

                const sizeElement = document.querySelector('#imageModal .modal-size span');
                if (sizeElement) {
                    sizeElement.textContent = `Dung lượng: ${set.size ? set.size + ' MB' : 'Không xác định'}`;
                }

                const favoriteElement = document.querySelector('#imageModal .modal-favorite span');
                if (favoriteElement) {
                    favoriteElement.textContent = `Yêu thích: ${set.bookmarks ? set.bookmarks.length : 0}`;
                }

                renderSocialShare(set);

                const tagsContainer = document.querySelector('.tags-product-list');
                if (tagsContainer) {
                    if (set.tags && set.tags.length > 0) {
                        const tagsHtml = set.tags.map(tag =>
                            `<span class="tags-product-item p-1 p-md-2 text-xs-2">${tag.tag.name}</span>`).join(
                            '');
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

                const keywordWrapper = document.querySelector('#imageModal .modal-keywords-wrapper');

                if (keywordWrapper) {
                    let keywordsContent = '';

                    if (set.keywords) {
                        try {
                            const keywordsArray = typeof set.keywords === 'string' ? JSON.parse(set.keywords) : set
                                .keywords;
                            if (Array.isArray(keywordsArray) && keywordsArray.length > 0) {
                                const firstKeywords = keywordsArray.slice(0, 2).map(keyword =>
                                    `<a class="color-primary-9" href="#">${keyword}</a>`).join(' ; ');
                                keywordsContent = firstKeywords;
                            }
                        } catch (e) {
                            console.error('Error parsing keywords:', e);
                        }
                    }

                    keywordWrapper.innerHTML =
                        `<span class="modal-keywords color-primary-12">Từ khóa:</span> ${keywordsContent} - <span class="color-primary-6">Mẫu #${set.id}</span>`;
                }

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
                        
                        <button class="btn-download btn fw-semibold py-3 d-flex mt-2" onclick="initDownload(${set.id})" data-set-id="${set.id}">
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

            function showSetNotFoundError(message, featuredSets = []) {
                const modal = document.getElementById('imageModal');
                if (!modal) return;

                const modalContent = modal.querySelector('.modal-content');
                if (modalContent) {
                    const existingRows = modalContent.querySelectorAll('.row:not(.error-content)');
                    existingRows.forEach(row => {
                        row.style.display = 'none';
                    });

                    const imageSlider = modalContent.querySelector('.image-slider');
                    if (imageSlider) {
                        imageSlider.style.display = 'none';
                    }

                    const tagsList = modalContent.querySelector('.tags-product-list');
                    if (tagsList) {
                        tagsList.style.display = 'none';
                    }

                    const oldError = modalContent.querySelector('.error-content');
                    if (oldError) {
                        oldError.remove();
                    }

                    let featuredHtml = '';
                    if (featuredSets && featuredSets.length > 0) {
                        featuredHtml = `
                            <div class="mt-5">
                                <h5 class="text-center mb-4">Thiết kế nổi bật</h5>
                                <div class="row">
                                    ${featuredSets.map(set => `
                                            <div class="col-6 col-md-3 mb-3">
                                                <div class="featured-set-item" onclick="loadSetDetailsBySlug('${set.slug || set.id}')" style="cursor: pointer;">
                                                    <img src="${set.photos && set.photos[0] ? '/storage/' + set.photos[0].path : '/images/default-set.png'}" 
                                                         alt="${set.name}" class="img-fluid rounded">
                                                    <h6 class="mt-2 text-center">${set.name}</h6>
                                                </div>
                                            </div>
                                        `).join('')}
                                </div>
                            </div>
                        `;
                    }

                    const errorContent = document.createElement('div');
                    errorContent.className = 'row error-content';
                    errorContent.innerHTML = `
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="error-icon mb-4">
                                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                                </div>
                                <h4 class="text-muted mb-3">File không tồn tại</h4>
                                <p class="text-muted mb-4">${message || 'File bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.'}</p>
                                <button type="button" class="btn btn-primary" onclick="var modal = document.getElementById('imageModal'); modal.style.display = 'none'; document.body.style.overflow = 'auto'; var urlParams = new URLSearchParams(window.location.search); if (urlParams.has('set')) { urlParams.delete('set'); var newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : ''); window.history.replaceState({}, '', newUrl); }">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </button>
                                ${featuredHtml}
                            </div>
                        </div>
                    `;

                    const firstImageSlider = modalContent.querySelector('.image-slider');
                    if (firstImageSlider) {
                        firstImageSlider.parentNode.insertBefore(errorContent, firstImageSlider);
                    } else {
                        modalContent.appendChild(errorContent);
                    }
                }
            }



            function renderSocialShare(set) {
                const container = document.querySelector(`#social-share-container-${set.id}`);
                if (!container) return;

                const shareUrl = `${window.location.origin}/search?set=${set.slug}`;
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
                            <button type="button" class="social-btn copy-link text-xs-1" title="Sao chép liên kết" onclick="copySetLink('${shareUrl}')" style="background:#ffffff;color:#111827">
                                <i class="fas fa-link" style="color:#111827;"></i>
                                <span style="color:#111827;">Sao chép link</span>
                            </button>
                        </div>

                        <button type="button" class="favorite-btn text-xs-1 ${set.isFavorited ? 'favorited' : ''}" data-set-id="${set.id}" onclick="toggleFavoriteModal(this)">
                            <i class="${set.isFavorited ? 'fas' : 'far'} fa-heart"></i>
                            <span>Yêu thích</span>
                        </button>
                    </div>
                `;
            }

            window.copySetLink = async function(url) {
                try {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        await navigator.clipboard.writeText(url);
                    } else {
                        const ta = document.createElement('textarea');
                        ta.value = url;
                        ta.style.position = 'fixed';
                        ta.style.opacity = '0';
                        document.body.appendChild(ta);
                        ta.focus();
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                    }
                    if (typeof showToast === 'function') {
                        showToast('Đã sao chép liên kết sản phẩm', 'success');
                    }
                } catch (e) {
                    if (typeof showToast === 'function') {
                        showToast('Không thể sao chép liên kết', 'error');
                    }
                }
            }



            function positionBanner() {
                const container = document.getElementById('masonry-container');
                if (!container) return;

                const banner = Array.from(container.children).find(item => {
                    const link = item.querySelector('a[href*="/y-tuong-thiet-ke"]');
                    return link !== null;
                });

                if (!banner) return;

                const getColumnCount = () => {
                    const width = window.innerWidth;
                    if (width < 577) return 1;
                    if (width < 769) return 2;
                    if (width < 1200) return 3;
                    return 4;
                };
                
                const columnCount = getColumnCount();
                
                const allItems = Array.from(container.children);
                const itemsWithoutBanner = allItems.filter(item => item !== banner);
                
                const targetIndex = columnCount;
                
                if (itemsWithoutBanner.length >= columnCount - 1) {
                    const insertAfterItem = itemsWithoutBanner[columnCount - 1];
                    if (insertAfterItem && insertAfterItem.nextSibling !== banner) {
                        container.insertBefore(banner, insertAfterItem.nextSibling);
                    } else if (insertAfterItem && !insertAfterItem.nextSibling) {
                        container.appendChild(banner);
                    }
                } else {
                    container.appendChild(banner);
                }
            }

            function initMasonry() {
                const container = document.getElementById('masonry-container');
                if (container && container.children.length > 0) {
                    const images = container.querySelectorAll('img');
                    let loadedImages = 0;

                    if (images.length === 0) {
                        positionBanner();
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
                                    positionBanner();
                                    refreshMasonry();
                                }
                            });
                        }
                    });

                    if (loadedImages === images.length) {
                        positionBanner();
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
                                middle: ['zoomIn', 'zoomOut', 'toggle1to1', 'rotateCCW', 'rotateCW',
                                    'flipX', 'flipY'
                                ],
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
                    setTimeout(() => {
                        positionBanner();
                    }, 50);
                }
            }

            const modal = document.getElementById('imageModal');
            const closeModal = document.getElementById('closeModal');

            function closeImageModal() {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';

                // Xóa ?set= khỏi URL nếu có
                const url = new URL(window.location);
                if (url.searchParams.has('set')) {
                    url.searchParams.delete('set');
                    window.history.replaceState({}, '', url);
                }
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

            // Listen for custom event from search.blade.php
            document.addEventListener('openSetModal', function(event) {
                const setSlug = event.detail.setSlug;
                if (setSlug) {
                    loadSetDetailsBySlug(setSlug);
                    document.getElementById('imageModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            });

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
                resizeTimeout = setTimeout(() => {
                    positionBanner();
                    refreshMasonry();
                }, 250);
            });

            function showSwal(options) {
                const result = Swal.fire({
                    ...options,
                    backdrop: true,
                    didOpen: () => {
                        const swalContainer = document.querySelector('.swal2-container');
                        if (swalContainer) {
                            swalContainer.style.setProperty('z-index', '99999999', 'important');
                        }
                    }
                });
                return result;
            }

            // Download & Purchase Functions
            window.initDownload = function(setId) {
                if (!setId) {
                    showToast('Không thể xác định file cần tải', 'error');
                    return;
                }

                // Show loading
                showSwal({
                    title: 'Đang kiểm tra...',
                    text: 'Vui lòng đợi',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Check download condition
                fetch(`/user/purchase/check/${setId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        if (!data.success) {
                            // Cannot download
                            showSwal({
                                icon: 'error',
                                title: 'Không thể tải',
                                text: data.message,
                                confirmButtonColor: '#667eea'
                            });
                            return;
                        }

                        // Can download - show confirmation modal
                        showDownloadConfirmModal(data, setId);
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('Error:', error);

                        if (error.status === 401) {
                            showSwal({
                                icon: 'warning',
                                title: 'Chưa đăng nhập',
                                text: 'Vui lòng đăng nhập để tải file',
                                confirmButtonText: 'Đăng nhập',
                                confirmButtonColor: '#667eea'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '/login';
                                }
                            });
                        } else {
                            showSwal({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Có lỗi xảy ra khi kiểm tra điều kiện tải',
                                confirmButtonColor: '#667eea'
                            });
                        }
                    });
            };

            function showDownloadConfirmModal(data, setId) {
                const set = data.set;
                let message = data.message;
                let confirmButtonText = 'Xác nhận tải';

                // Build message content
                let htmlContent = `
                    <div class="text-start">
                        <h5 class="mb-3">${set.name}</h5>
                `;

                if (data.already_purchased) {
                    htmlContent +=
                        `<p class="text-success"><i class="fas fa-check-circle me-2"></i>Bạn đã mua file này</p>`;
                    confirmButtonText = 'Tải ngay';
                } else if (data.is_free) {
                    if (data.unlimited) {
                        htmlContent +=
                            `<p class="text-success"><i class="fas fa-infinity me-2"></i>File miễn phí - Tải không giới hạn (VIP)</p>`;
                    } else if (data.free_downloads_left) {
                        htmlContent +=
                            `<p class="text-info"><i class="fas fa-gift me-2"></i>File miễn phí - Còn ${data.free_downloads_left} lượt tải</p>`;
                    }
                    confirmButtonText = 'Tải ngay';
                } else if (data.requires_purchase) {
                    htmlContent += `
                        <div class="alert alert-warning">
                            <strong>Mua file Premium</strong><br>
                            Giá: <strong>${set.price} XU</strong><br>
                            Xu hiện tại: <strong>${data.user.coins} XU</strong><br>
                            Xu còn lại sau khi mua: <strong>${data.user.remaining_coins} XU</strong>
                        </div>
                    `;
                    confirmButtonText = 'Xác nhận mua';
                }

                htmlContent += `</div>`;

                showSwal({
                    title: data.already_purchased ? 'Tải file' : (data.is_free ? 'Tải file miễn phí' :
                        'Xác nhận mua file'),
                    html: htmlContent,
                    icon: data.already_purchased || data.is_free ? 'success' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: 'Hủy',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirmPurchaseAndDownload(setId);
                    }
                });
            }

            function confirmPurchaseAndDownload(setId) {
                // Thêm vào popup và stream tiến trình tải trực tiếp
                const setNameEl = document.querySelector('#imageModal .modal-title');
                const setName = setNameEl ? setNameEl.textContent.trim() : 'File';
                if (window.startDownloadWithPopup) {
                    window.startDownloadWithPopup({ endpoint: `/user/purchase/confirm/${setId}`, setId, setName });
                } else {
                    // Fallback: vẫn gọi như cũ nếu popup script chưa load
                    fetch(`/user/purchase/confirm/${setId}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/zip,application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ user_confirmed: true })
                    });
                }
            }

            window.toggleFavoriteCard = function(event, button) {
                event.stopPropagation();

                const setId = button.getAttribute('data-set-id');
                if (!setId) return;

                const icon = button.querySelector('i');
                const isCurrentlyFavorited = button.classList.contains('favorited');

                fetch(`user/search/set/${setId}/favorite`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.isFavorited) {
                                button.classList.add('favorited');
                                icon.classList.remove('far');
                                icon.classList.add('fas');
                            } else {
                                button.classList.remove('favorited');
                                icon.classList.remove('fas');
                                icon.classList.add('far');
                            }
                        } else {
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            };

            window.handleDownloadClick = function(event, setId) {
                event.stopPropagation();

                if (!setId) {
                    console.error('Set ID is missing');
                    return;
                }

                loadSetDetails(setId);
                const modal = document.getElementById('imageModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            };

            // Open modal when clicking anywhere on card (except action buttons)
            window.openSetModalFromCard = function(event, setId) {
                // If click is on an action button, don't open modal
                if (event.target.closest('.overlay-action-btn')) {
                    return;
                }

                if (!setId) {
                    console.error('Set ID is missing');
                    return;
                }

                loadSetDetails(setId);
                const modal = document.getElementById('imageModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            };
        });
    </script>
@endpush
