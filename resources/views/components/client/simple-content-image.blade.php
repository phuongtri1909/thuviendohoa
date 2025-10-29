@if ($contentImage && $contentImage->image)
    @php
        $imageSrc = str_starts_with($contentImage->image, 'content-images/')
            ? Storage::url($contentImage->image)
            : asset($contentImage->image);
        $imageAlt = $contentImage->name ?? 'Content Image';
        $url = $contentImage->url ?? null;
    @endphp

    <div class="simple-content-image-wrapper">
        @if ($url)
            <a href="{{ $url }}" class="simple-content-link" target="_blank">
                <img src="{{ $imageSrc }}" alt="{{ $imageAlt }}" class="img-fluid">
            </a>
        @else
            <img src="{{ $imageSrc }}" alt="{{ $imageAlt }}" class="img-fluid">
        @endif
    </div>

    @push('styles')
        <style>
            .simple-content-image-wrapper {
                position: relative;
                width: 100%;
            }

            .simple-content-link {
                display: block;
                text-decoration: none;
                transition: opacity 0.3s ease;
            }

            .simple-content-link:hover {
                opacity: 0.9;
            }
        </style>
    @endpush

@endif
