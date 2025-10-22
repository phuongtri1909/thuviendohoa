@props([
    'url' => null,
    'title' => null,
    'showFavorite' => true,
    'favoriteCount' => null,
    'isFavorited' => false,
    'setId' => null,
])

@php
    $shareUrl = $url ?? url()->current();
    $shareTitle = $title ?? config('app.name');
    $encodedUrl = urlencode($shareUrl);
    $encodedTitle = urlencode($shareTitle);
@endphp

@push('styles')
    <style>
        .social-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .social-btn {
            display: inline-flex;
            align-items: stretch;
            padding: 0;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .social-btn:active {
            transform: translateY(-1px);
        }

        /* Facebook */
        .facebook {
            background-color: #1877F2;
        }

        .facebook:hover {
            background-color: #166fe5;
            color: white;
        }

        /* Twitter */
        .twitter {
            background-color: #1DA1F2;
        }

        .twitter:hover {
            background-color: #1a8cd8;
            color: white;
        }

        /* Pinterest */
        .pinterest {
            background-color: #E60023;
        }

        .pinterest:hover {
            background-color: #cc001a;
            color: white;
        }

        /* LinkedIn */
        .linkedin {
            background-color: #0A66C2;
        }

        .linkedin:hover {
            background-color: #085196;
            color: white;
        }

        .social-btn i {
            font-size: 18px;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .social-btn span {
            padding: 5px 10px;
            display: flex;
            align-items: center;
        }

        .favorite-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            background-color: white;
            color: #666;
            font-size: 14px;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .favorite-btn:hover {
            border-color: #e60023;
            color: #e60023;
            background-color: #fff5f5;
        }

        .favorite-btn.favorited {
            border-color: #e60023;
            color: #e60023;
            background-color: #fff5f5;
        }

        .favorite-btn.favorited i {
            color: #e60023;
        }

        .favorite-btn i {
            color: #999;
            transition: color 0.3s ease;
        }

        .favorite-btn:hover i {
            color: #e60023;
        }

        .social-share-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        @media (min-width: 1350px) {
            .social-share-wrapper {
                flex-direction: row;
                justify-content: space-between;
            }

            .social-buttons {
                justify-content: normal;
                flex-wrap: nowrap;
                text-align: center
            }
        }
    </style>
@endpush

<div class="social-share-wrapper">
    <div class="social-buttons">
        <a href="https://www.facebook.com/sharer/sharer.php?u=https://example.com" target="_blank"
            class="social-btn facebook text-xs-1" title="Chia sẻ trên Facebook">
            <i class="fab fa-facebook-f"></i>
            <span>Facebook</span>
        </a>

        <a href="https://twitter.com/intent/tweet?url=https://example.com&text=Check%20this%20out" target="_blank"
            class="social-btn twitter text-xs-1" title="Chia sẻ trên Twitter">
            <i class="fab fa-twitter"></i>
            <span>Twitter</span>
        </a>

        <a href="https://www.pinterest.com/pin/create/button/?url=https://example.com" target="_blank"
            class="social-btn pinterest text-xs-1" title="Chia sẻ trên Pinterest">
            <i class="fab fa-pinterest-p"></i>
            <span>Pinterest</span>
        </a>

        <a href="https://www.linkedin.com/sharing/share-offsite/?url=https://example.com" target="_blank"
            class="social-btn linkedin text-xs-1" title="Chia sẻ trên LinkedIn">
            <i class="fab fa-linkedin-in"></i>
            <span>LinkedIn</span>
        </a>
    </div>

    {{-- Favorite Button --}}
    @if ($showFavorite)
        <button type="button" class="favorite-btn {{ $isFavorited ? 'favorited' : '' }} text-xs-1"
            @if($setId) data-set-id="{{ $setId }}" onclick="toggleFavoriteModal(this)" @else onclick="toggleFavorite(this)" @endif>
            <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart"></i>
            <span>Yêu thích</span>
        </button>
    @endif
</div>

@push('scripts')
    <script>
        function toggleFavorite(button) {
            const icon = button.querySelector('i');
            const isCurrentlyFavorited = button.classList.contains('favorited');

            if (isCurrentlyFavorited) {
                button.classList.remove('favorited');
                icon.classList.remove('fas');
                icon.classList.add('far');
            } else {
                button.classList.add('favorited');
                icon.classList.remove('far');
                icon.classList.add('fas');
            }
        }

        function toggleFavoriteModal(button) {
    
            const setId = button.getAttribute('data-set-id');
            if (!setId) return;

            const icon = button.querySelector('i');
            const isCurrentlyFavorited = button.classList.contains('favorited');

            fetch(`/search/set/${setId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

                    const favoriteElement = document.querySelector('#imageModal .modal-favorite span');
                    if (favoriteElement) {
                        favoriteElement.textContent = `Yêu thích: ${data.favoriteCount}`;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
@endpush
