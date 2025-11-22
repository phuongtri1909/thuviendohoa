@props(['key' => null, 'title' => null, 'content' => null])

@php
    if ($key) {
        if (isset($aboutContents) && $aboutContents->has($key)) {
            $aboutContent = $aboutContents[$key];
            $displayTitle = $title ?? $aboutContent->title;
            $displayContent = $content ?? $aboutContent->content;
        } else {
            $aboutContent = \App\Models\AboutContent::getOrCreateByKey($key, $title, $content ?? '');
            $displayTitle = $title ?? $aboutContent->title;
            $displayContent = $content ?? $aboutContent->content;
        }
    } else {
        $displayTitle = $title ?? '';
        $displayContent = $content ?? '';
    }
@endphp

@if($displayContent)
    <div class="about-content p-4">
        @if($displayTitle)
            <h6 class="fw-semibold">{{ $displayTitle }}</h6>
        @endif
        <p class="mb-0">
            {{ $displayContent }}
        </p>
    </div>
@endif

@push('styles')
    <style>
        .about-content {
            border-radius: 10px;
            border: 1px solid #FFEAD7;
            background: #FFF9F9
        }
    </style>
@endpush

@push('scripts')
@endpush
