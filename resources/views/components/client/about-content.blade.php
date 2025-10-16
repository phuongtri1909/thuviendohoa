@props(['title' => '', 'content' => ''])

<div class="about-content p-4">
    <h6 class="fw-semibold">{{ $title }}</h6>
    <p class="mb-0">
        {{ $content }}
    </p>
</div>

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
