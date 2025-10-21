@props(['title', 'image'])

@once
    @push('styles')
        @vite('resources/assets/frontend/css/components/collection-card.css')
    @endpush
@endonce

<div class="collection-wrapper">
    <div class="collection-card">
        <div class="collection-inner">
            <img src="{{ Storage::url($image) }}" alt="{{ $title }}">
        </div>
    </div>
    <div class="label-collection">{{ $title }}</div>
</div>
