@extends('client.layouts.app')
@section('title', 'Bộ sưu tập - ' . config('app.name'))
@section('description', 'Xem tất cả bộ sưu tập - ' . config('app.name'))
@section('keywords', 'bộ sưu tập, albums, ' . config('app.name'))

@section('content')
    <!-- Albums Banner -->
    <x-albums-static-banner :banners="$banners" :hasBanners="$has_banners" :searchQuery="request('search', '')" :interval="4000" />

    <div class="albums-section-positioned">
        <div class="container-custom">
            <div class="albums-content">
                <div class="row g-3" id="albums-container">
                    @foreach ($albums as $album)
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <x-client.collection-card :title="$album->name" :image="$album->image" :album-slug="$album->slug" />
                        </div>
                    @endforeach
                </div>

                @if ($albums->hasMorePages())
                    <div class="text-center mt-4">
                        <button id="load-more-btn" class="btn btn-primary load-more-btn">
                            <i class="fas fa-plus"></i> Xem thêm
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4 mt-md-5 px-0">
        <x-client.content-image />
    </div>

    <div class="mt-3 mt-md-5">
        <x-client.simple-content-image />
    </div>


    <div class="pt-3 pt-md-5 mt-md-5">
        <x-client.desktop desktop-image="images/d/desktops/desktop.png" background-image="images/d/desktops/background.png"
            frame-image="images/d/desktops/khung.png" alt="Desktop Screenshot" />
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentPage = 1;
            let isLoading = false;
            const loadMoreBtn = document.getElementById('load-more-btn');
            const albumsContainer = document.getElementById('albums-container');

            function adjustAlbumsPosition() {
                const banner = document.querySelector('.banner-static');
                const albumsSection = document.querySelector('.albums-section-positioned');

                if (banner && albumsSection) {
                    const marginTop = -450;
                    albumsSection.style.marginTop = marginTop + 'px';
                }
            }

            adjustAlbumsPosition();
            window.addEventListener('resize', adjustAlbumsPosition);

            const searchForm = document.querySelector('form[action="{{ route('albums') }}"]');
            if (searchForm) {
                searchForm.addEventListener('submit', function() {
                    setTimeout(adjustAlbumsPosition, 100);
                });
            }

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    if (isLoading) return;

                    isLoading = true;
                    loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
                    loadMoreBtn.disabled = true;

                    currentPage++;

                    fetch(`{{ route('albums') }}?page=${currentPage}&search={{ request('search', '') }}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            data.albums.forEach(album => {
                                const col = document.createElement('div');
                                col.className = 'col-lg-3 col-md-6 col-sm-12';
                                col.innerHTML = `
                        <div class="collection-wrapper">
                            <div class="collection-card">
                                <div class="collection-inner">
                                    <img src="/storage/${album.image}" alt="${album.name}">
                                </div>
                            </div>
                            <div class="label-collection">${album.name}</div>
                        </div>
                    `;
                                albumsContainer.appendChild(col);
                            });

                            if (!data.hasMore) {
                                loadMoreBtn.style.display = 'none';
                            } else {
                                loadMoreBtn.innerHTML = '<i class="fas fa-plus"></i> Xem thêm';
                                loadMoreBtn.disabled = false;
                            }

                            isLoading = false;
                            setTimeout(adjustAlbumsPosition, 100);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            loadMoreBtn.innerHTML = '<i class="fas fa-plus"></i> Xem thêm';
                            loadMoreBtn.disabled = false;
                            isLoading = false;
                        });
                });
            }
        });
    </script>
@endpush

@push('styles')
    @vite('resources/assets/frontend/css/albums.css')
@endpush
