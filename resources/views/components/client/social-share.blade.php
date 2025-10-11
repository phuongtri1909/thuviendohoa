@props([
    'url' => null,
    'title' => null,
    'showFavorite' => true,
    'favoriteCount' => null,
    'isFavorited' => false,
])

@php
    $shareUrl = $url ?? url()->current();
    $shareTitle = $title ?? config('app.name');
    $encodedUrl = urlencode($shareUrl);
    $encodedTitle = urlencode($shareTitle);
@endphp

@push('styles')
    <style>
        .social-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .social-btn i {
            font-size: 16px;
        }

        .social-btn-facebook {
            background-color: #3b5998;
        }

        .social-btn-facebook:hover {
            background-color: #2d4373;
        }

        .social-btn-twitter {
            background-color: #1da1f2;
        }

        .social-btn-twitter:hover {
            background-color: #0c85d0;
        }

        .social-btn-pinterest {
            background-color: #e60023;
        }

        .social-btn-pinterest:hover {
            background-color: #bd001c;
        }

        .social-btn-linkedin {
            background-color: #0077b5;
        }

        .social-btn-linkedin:hover {
            background-color: #005885;
        }

        .favorite-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
            font-size: 18px;
            color: #999;
            transition: color 0.3s ease;
        }

        .favorite-btn:hover i {
            color: #e60023;
        }

        @media (max-width: 576px) {
            .social-btn {
                padding: 8px 16px;
                font-size: 13px;
            }

            .social-btn i {
                font-size: 14px;
            }

            .favorite-btn {
                padding: 8px 16px;
                font-size: 13px;
            }

            .favorite-btn i {
                font-size: 16px;
            }
        }
    </style>
@endpush

<div class="social-share-wrapper d-flex justify-content-between">
    <div>
        {{-- Facebook --}}
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank"
            rel="noopener noreferrer" class="social-btn social-btn-facebook">
            <i class="fab fa-facebook-f"></i>
            <span>Facebook</span>
        </a>

        {{-- Twitter --}}
        <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}" target="_blank"
            rel="noopener noreferrer" class="social-btn social-btn-twitter">
            <i class="fab fa-twitter"></i>
            <span>Twitter</span>
        </a>

        {{-- Pinterest --}}
        <a href="https://pinterest.com/pin/create/button/?url={{ $encodedUrl }}&description={{ $encodedTitle }}"
            target="_blank" rel="noopener noreferrer" class="social-btn social-btn-pinterest">
            <i class="fab fa-pinterest-p"></i>
            <span>Pinterest</span>
        </a>

        {{-- LinkedIn --}}
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" target="_blank"
            rel="noopener noreferrer" class="social-btn social-btn-linkedin">
            <i class="fab fa-linkedin-in"></i>
            <span>LinkedIn</span>
        </a>
    </div>

    {{-- Favorite Button --}}
    @if ($showFavorite)
        <button type="button" class="favorite-btn {{ $isFavorited ? 'favorited' : '' }}"
            onclick="toggleFavorite(this)">
            <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart"></i>
            <span>Yêu thích</span>
            @if ($favoriteCount)
                <span class="favorite-count">({{ $favoriteCount }})</span>
            @endif
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
    </script>
@endpush
