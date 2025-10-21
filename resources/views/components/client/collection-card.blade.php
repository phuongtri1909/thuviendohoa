@props(['title', 'image', 'albumSlug'])

@once
    @push('styles')
        @vite('resources/assets/frontend/css/components/collection-card.css')
    @endpush
@endonce

<a href="{{ route('search', ['album' => $albumSlug]) }}" class="collection-wrapper text-decoration-none">
    <div class="collection-card">
        <div class="collection-inner">
            <img src="{{ Storage::url($image) }}" alt="{{ $title }}">
        </div>
    </div>
    <div class="label-collection">{{ $title }}</div>
</a>
